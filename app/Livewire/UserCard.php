<?php

namespace App\Livewire;

use App\Enums\FriendshipStatus;
use App\Enums\Gender;
use App\Enums\NotificationType;
use App\Enums\Severity;
use App\Models\Block;
use App\Models\Friendship;
use App\Models\Notification;
use App\Models\Report;
use App\Models\User;
use App\Models\UserAnswer;
use App\Models\UserMatchScore;
use App\Traits\HasFeatureAccess;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

/**
 * Class UserCard
 *
 * A Livewire component for displaying and managing user interactions in a card format.
 *
 * @package App\Livewire
 */
class UserCard extends Component
{
    use HasFeatureAccess;

    /** @var User The user being displayed in the card */
    public User $user;

    /** @var Friendship|null The friendship relationship between the current user and the displayed user */
    public ?Friendship $friendship = null;

    /** @var Block|null The block status between the current user and the displayed user */
    public ?Block $block = null;

    /** @var float The match percentage between the current user and the displayed user */
    public float $matchPercentage = 0;

    /** @var string The reason for reporting the user */
    public $reportReason = '';

    public $showModal = false;
    /** @var string The description for reporting the user */
    public $reportDescription = '';

    /** @var array Validation rules for reporting */
    protected $rules = [
        'reportReason' => 'required|string|max:255',
        'reportDescription' => 'required|string|min:10|max:1000',
    ];

    /**
     * Mount the component with the specified user.
     *
     * @param User $user The user to display
     * @return void
     */
    public function mount(User $user): void
    {
        $this->user = $user;
        $this->checkRelationship();
        $this->calculateMatchPercentage();
    }

    /**
     * Check the relationship (friendship or block) between the current user and the displayed user.
     *
     * @return void
     */
    private function checkRelationship(): void
    {
        $currentUser = auth()->user();

        $this->friendship = Friendship::where(function ($query) use ($currentUser) {
            $query->where('user_id', $currentUser->id)->where('target_id', $this->user->id);
        })
            ->orWhere(function ($query) use ($currentUser) {
                $query->where('user_id', $this->user->id)->where('target_id', $currentUser->id);
            })
            ->first();

        $this->block = Block::where('user_id', $currentUser->id)->where('target_id', $this->user->id)->first();
    }

    /**
     * Calculate the match percentage between the current user and the displayed user.
     *
     * @return void
     */
    private function calculateMatchPercentage(): void
    {
        $this->matchPercentage =
            UserMatchScore::where('user_id', auth()->user()->id)
                ->where('target_id', $this->user->id)
                ->value('match_score') ?? 0;
    }

    /**
     * Send a friendship request to the displayed user.
     *
     * @return void
     */
    public function sendFriendshipRequest(): void
    {
        if (!$this->hasFeatureAccess('send_request')) {
            redirect()->route('plans.upgrade')->with('error', 'Upgrade your plan to send friend requests.');
            return;
        }

        $friendship = Friendship::create([
            'user_id' => auth()->user()->id,
            'target_id' => $this->user->id,
            'status' => FriendshipStatus::Pending->value,
        ]);

        Notification::create([
            'user_id' => $this->user->id,
            'sender_id' => auth()->user()->id,
            'type' => NotificationType::FriendRequest->value,
            'title' => \Illuminate\Support\Str::limit(auth()->user()->display_name . ' sent you a friend request', 100, '...'),
            'content' => 'Click to view the request.',
            'action_url' => route('friends.received'),
            'related_id' => $friendship->id,
            'related_type' => 'Friendship',
            'priority' => 2,
        ]);

        $this->dispatch('friendship-request-sent');
        $this->checkRelationship();
    }

    /**
     * Accept a friendship request from the displayed user.
     *
     * @return void
     */
    public function acceptFriendship(): void
    {
        if (!$this->hasFeatureAccess('accept_request')) {
            redirect()->route('plans.upgrade')->with('error', 'Upgrade your plan to accept friend requests.');
            return;
        }

        if ($this->friendship && $this->friendship->user_id === $this->user->id) {
            $this->friendship->update(['status' => FriendshipStatus::Accepted->value]);

            Notification::create([
                'user_id' => $this->user->id,
                'sender_id' => auth()->user()->id,
                'type' => NotificationType::FriendAccepted->value,
                'title' => \Illuminate\Support\Str::limit(auth()->user()->display_name . ' accepted your friend request', 100, '...'),
                'content' => 'You are now friends!',
                'action_url' => route('friends.my-friends'),
                'related_id' => $this->friendship->id,
                'related_type' => 'Friendship',
                'priority' => 2,
            ]);

            $this->dispatch('friendship-accepted');
            $this->checkRelationship();
        }
    }

    /**
     * Reject a friendship request from the displayed user.
     *
     * @return void
     */
    public function rejectFriendship(): void
    {
        if (!$this->hasFeatureAccess('accept_request')) {
            redirect()->route('plans.upgrade')->with('error', 'Upgrade your plan to reject friend requests.');
            return;
        }

        if ($this->friendship && $this->friendship->user_id === $this->user->id) {
            $this->friendship->update(['status' => FriendshipStatus::Rejected->value]);
            $this->dispatch('friendship-rejected');
            $this->checkRelationship();
        }
    }

    /**
     * Cancel a sent friendship request.
     *
     * @return void
     */
    public function cancelFriendship(): void
    {
        if (!$this->hasFeatureAccess('send_request')) {
            redirect()->route('plans.upgrade')->with('error', 'Upgrade your plan to cancel friend requests.');
            return;
        }

        if ($this->friendship && $this->friendship->user_id === auth()->user()->id) {
            $this->friendship->delete();
            $this->dispatch('friendship-cancelled');
            $this->checkRelationship();
        }
    }

    /**
     * Remove the displayed user from friends list.
     *
     * @return void
     */
    public function unfriend(): void
    {
        if (!$this->hasFeatureAccess('remove_friend')) {
            redirect()->route('plans.upgrade')->with('error', 'Upgrade your plan to remove friends.');
            return;
        }

        if ($this->friendship && $this->friendship->status === FriendshipStatus::Accepted) {
            $this->friendship->delete();
            $this->dispatch('friendship-removed');
            $this->checkRelationship();
        }
    }

    public function blockUser(): void
    {
        if (config('app.debug')) {
            Log::info('BlockUser method called', ['user_id' => auth()->user()->id, 'target_id' => $this->user->id]);
        }

        if (!$this->hasFeatureAccess('block_user')) {
            redirect()->route('plans.upgrade')->with('error', 'Upgrade your plan to block users.');
            return;
        }

        // if ($this->isBlocked) {
        //     $this->dispatch('toast-message', [
        //         'type' => 'warning',
        //         'message' => 'This user is already blocked!',
        //     ]);
        //     return;
        // }

        if (config('app.debug')) {
            Log::info('Attempting to block user', ['user_id' => auth()->user()->id, 'target_id' => $this->user->id]);
        }

        $blocked = Block::create([
            'user_id' => auth()->user()->id,
            'target_id' => $this->user->id,
        ]);

        if ($blocked) {
            if (config('app.debug')) {
                Log::info('User blocked successfully', ['block_id' => $blocked->id]);
            }
            $this->dispatch('toast-message', [
                'type' => 'success',
                'message' => 'User blocked successfully!',
            ]);
        } else {
            if (config('app.debug')) {
                Log::error('Failed to block user', ['user_id' => auth()->user()->id, 'target_id' => $this->user->id]);
            }
            $this->dispatch('toast-message', [
                'type' => 'error',
                'message' => 'Failed to block user. Please try again.',
            ]);
            return;
        }
        // if ($blocked) {
        //     if (config('app.debug')) {
        //         Log::info('User blocked successfully', ['block_id' => $blocked->id]);
        //     }
        // } else {
        //     Log::error('Failed to block user', ['user_id' => auth()->user()->id, 'target_id' => $this->user->id]);
        // }

        if ($this->friendship) {
            $this->friendship->delete();
            Log::info('Friendship deleted due to block', ['friendship_id' => $this->friendship->id]);
        }

        $this->dispatch('user-blocked');
        //$this->dispatch('test-blocked');
        $this->checkRelationship();
    }


    public function block(): void
    {
        //Log::info('Block method called', ['user_id' => auth()->user()->id, 'target_id' => $this->user->id]);
        //$this->dispatch('user-blocked');

        Log::info('Block method called', ['user_id' => auth()->user()->id, 'target_id' => $this->user->id]);

        if (!$this->hasFeatureAccess('block_user')) {
            Log::info('User lacks block_user feature access', ['user_id' => auth()->user()->id]);
            redirect()->route('plans.upgrade')->with('error', 'Upgrade your plan to block users.');
            return;
        }

        Log::info('Attempting to block user', ['user_id' => auth()->user()->id, 'target_id' => $this->user->id]);

        $blocked = Block::create([
            'user_id' => auth()->user()->id,
            'target_id' => $this->user->id,
        ]);

        if ($blocked) {
            Log::info('User blocked successfully', ['block_id' => $blocked->id]);
        } else {
            Log::error('Failed to block user', ['user_id' => auth()->user()->id, 'target_id' => $this->user->id]);
        }

        if ($this->friendship) {
            $this->friendship->delete();
            Log::info('Friendship deleted due to block', ['friendship_id' => $this->friendship->id]);
        }

        $this->dispatch('user-blocked');
        $this->checkRelationship();
    }

    /**
     * Unblock the displayed user.
     *
     * @return void
     */
    public function unblock(): void
    {
        if (!$this->hasFeatureAccess('unblock_user')) {
            redirect()->route('plans.upgrade')->with('error', 'Upgrade your plan to unblock users.');
            return;
        }

        if ($this->block) {
            $this->block->delete();
            $this->dispatch('user-unblocked');
            $this->checkRelationship();
        }
    }



    public function submitReport($data): void
    {
        Log::info('submitReport called', [
            'user_id' => auth()->user()->id,
            'target_id' => $this->user->id,
            'reason' => $data['reportReason'],
            'description' => $data['reportDescription'],
        ]);

        $validator = \Illuminate\Support\Facades\Validator::make($data, [
            'reportReason' => 'required|string|max:255',
            'reportDescription' => 'required|string|min:10|max:1000',
        ]);

        if ($validator->fails()) {
            Log::info('Validation failed', ['errors' => $validator->errors()->all()]);
            $this->dispatch('show-error-modal', [
                'errors' => $validator->errors()->all(),
                'userId' => $this->user->id,
            ]);
            return;
        }

        $report = Report::create([
            'user_id' => auth()->user()->id,
            'target_id' => $this->user->id,
            'report' => $data['reportReason'],
            'description' => $data['reportDescription'],
            'status' => FriendshipStatus::Pending->value,
            'severity' => Severity::Medium->value,
        ]);

        Log::info('Report created', ['report_id' => $report->id]);
        $this->dispatch('report-success', ['userId' => $this->user->id]);
    }
    /**
     * Redirect to the chat page with the displayed user.
     *
     * @return void
     */
    public function startChat(): void
    {
        if (!$this->hasFeatureAccess('use_chat')) {
            redirect()->route('plans.upgrade')->with('error', 'Upgrade your plan to use chat.');
            return;
        }

        redirect()->route('chat', ['user' => $this->user->id]);
    }

    /**
     * Render the user card view.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        $visibleInterests = UserAnswer::where('user_id', $this->user->id)->join('questions', 'user_answers.question_id', '=', 'questions.id')->where('questions.is_visible', true)->select('user_answers.*')->orderByDesc('questions.weight')->take(4)->with('question')->get()->map(
            fn($answer) => [
                'label' => $answer->question->answer_label,
                'value' => is_array($answer->answer) ? implode(', ', $answer->answer) : $answer->answer,
            ],
        );

        return view('livewire.user-card', [
            'user' => $this->user,
            'visibleInterests' => $visibleInterests,
        ]);
    }
}
