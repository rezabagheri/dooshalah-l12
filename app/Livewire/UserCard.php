<?php

namespace App\Livewire;

use App\Enums\FriendshipStatus;
use App\Enums\Gender;
use App\Models\Block;
use App\Models\Friendship;
use App\Models\Report;
use App\Models\Subscription;
use App\Models\User;
use App\Models\UserAnswer;
use App\Models\UserMatchScore;
use Livewire\Component;

class UserCard extends Component
{
    public User $user;
    public ?Friendship $friendship = null;
    public ?Block $block = null;
    public float $matchPercentage = 0;

    public function mount(User $user): void
    {
        $this->user = $user;
        $this->checkRelationship();
        $this->calculateMatchPercentage();
    }

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

    private function calculateMatchPercentage(): void
    {
        $this->matchPercentage = UserMatchScore::where('user_id', auth()->user()->id)
            ->where('target_id', $this->user->id)
            ->value('match_score') ?? 0;
    }

    private function hasFeatureAccess($feature): bool
    {
        $activeSubscription = Subscription::where('user_id', auth()->id())
            ->where('status', 'active')
            ->where('end_date', '>=', now())
            ->first();

        return $activeSubscription && $activeSubscription->plan->features()
            ->where('name', $feature)
            ->exists();
    }

    public function sendFriendshipRequest(): void
    {
        if (!$this->hasFeatureAccess('send_request')) {
            redirect()->route('plans.upgrade')->with('error', 'Upgrade your plan to send friend requests.');
            return;
        }

        Friendship::create([
            'user_id' => auth()->user()->id,
            'target_id' => $this->user->id,
            'status' => FriendshipStatus::Pending->value,
        ]);
        $this->dispatch('friendship-request-sent');
        $this->checkRelationship();
    }

    public function acceptFriendship(): void
    {
        if (!$this->hasFeatureAccess('accept_request')) {
            redirect()->route('plans.upgrade')->with('error', 'Upgrade your plan to accept friend requests.');
            return;
        }

        if ($this->friendship && $this->friendship->user_id === $this->user->id) {
            $this->friendship->update(['status' => FriendshipStatus::Accepted->value]);
            $this->dispatch('friendship-accepted');
            $this->checkRelationship();
        }
    }

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

    public function block(): void
    {
        if (!$this->hasFeatureAccess('block_user')) {
            redirect()->route('plans.upgrade')->with('error', 'Upgrade your plan to block users.');
            return;
        }

        Block::create([
            'user_id' => auth()->user()->id,
            'target_id' => $this->user->id,
        ]);
        if ($this->friendship) {
            $this->friendship->delete();
        }
        $this->dispatch('user-blocked');
        $this->checkRelationship();
    }

    public function unblock(): void
    {
        if (!$this->hasFeatureAccess('block_user')) {
            redirect()->route('plans.upgrade')->with('error', 'Upgrade your plan to unblock users.');
            return;
        }

        if ($this->block) {
            $this->block->delete();
            $this->dispatch('user-unblocked');
            $this->checkRelationship();
        }
    }

    public function report(): void
    {
        if (!$this->hasFeatureAccess('report_user')) {
            redirect()->route('plans.upgrade')->with('error', 'Upgrade your plan to report users.');
            return;
        }

        Report::create([
            'user_id' => auth()->user()->id,
            'target_id' => $this->user->id,
            'report' => 'Reported via UserCard',
            'status' => FriendshipStatus::Pending->value,
            'severity' => \App\Enums\Severity::Medium->value,
        ]);
        $this->dispatch('user-reported');
    }

    public function render()
    {
        $visibleInterests = UserAnswer::where('user_id', $this->user->id)
            ->join('questions', 'user_answers.question_id', '=', 'questions.id')
            ->where('questions.is_visible', true)
            ->select('user_answers.*')
            ->orderByDesc('questions.weight')
            ->take(4)
            ->with('question')
            ->get()
            ->map(fn ($answer) => [
                'label' => $answer->question->answer_label,
                'value' => is_array($answer->answer) ? implode(', ', $answer->answer) : $answer->answer,
            ]);

        return view('livewire.user-card', [
            'user' => $this->user,
            'visibleInterests' => $visibleInterests,
        ]);
    }
}
