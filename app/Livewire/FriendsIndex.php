<?php

namespace App\Livewire;

use App\Enums\FriendshipStatus;
use App\Enums\Gender;
use App\Models\Block;
use App\Models\Friendship;
use App\Models\Report;
use App\Models\User;
use App\Models\UserAnswer;
use Livewire\Component;
use Livewire\WithPagination;

class FriendsIndex extends Component
{
    use WithPagination;

    public string $activeTab = 'suggestions';
    public int $perPage = 15;
    public bool $isLoading = false; // حالت لود

    public function mount(): void
    {
        $this->activeTab = request()->routeIs('friends.suggestions') ? 'suggestions' :
                           (request()->routeIs('friends.my-friends') ? 'my-friends' :
                            (request()->routeIs('friends.pending') ? 'pending' :
                             (request()->routeIs('friends.received') ? 'received' :
                              (request()->routeIs('friends.blocked') ? 'blocked' : 'reports'))));
    }

    public function setTab($tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function loadMore(): void
    {
        $this->isLoading = true; // شروع لود
        $this->perPage += 15;
        $this->isLoading = false; // پایان لود
    }

    public function render()
    {
        $currentUser = auth()->user();

        $tabs = [
            'suggestions' => [
                'label' => 'Browse',
                'users' => $this->getSuggestions($currentUser),
            ],
            'my-friends' => [
                'label' => 'My Friends',
                'users' => User::whereIn('id', Friendship::where('status', FriendshipStatus::Accepted->value)
                    ->where(function ($query) use ($currentUser) {
                        $query->where('user_id', $currentUser->id)
                              ->orWhere('target_id', $currentUser->id);
                    })
                    ->selectRaw("IF(user_id = ?, target_id, user_id) as friend_id", [$currentUser->id])
                    ->pluck('friend_id'))
                    ->get(),
            ],
            'pending' => [
                'label' => 'Pending Requests',
                'users' => User::whereIn('id', Friendship::where('user_id', $currentUser->id)
                    ->where('status', FriendshipStatus::Pending->value)
                    ->pluck('target_id'))
                    ->get(),
            ],
            'received' => [
                'label' => 'Received Requests',
                'users' => User::whereIn('id', Friendship::where('target_id', $currentUser->id)
                    ->where('status', FriendshipStatus::Pending->value)
                    ->pluck('user_id'))
                    ->get(),
            ],
            'blocked' => [
                'label' => 'Blocked Users',
                'users' => User::whereIn('id', Block::where('user_id', $currentUser->id)
                    ->pluck('target_id'))
                    ->get(),
            ],
            'reports' => [
                'label' => 'Reports',
                'users' => User::whereIn('id', Report::where('user_id', $currentUser->id)
                    ->pluck('target_id'))
                    ->get(),
            ],
        ];

        return view('livewire.friends-index', [
            'tabs' => $tabs,
        ]);
    }

    private function getSuggestions($currentUser)
    {
        $blockedIds = Block::where('user_id', $currentUser->id)->pluck('target_id')->toArray();
        $blockedByIds = Block::where('target_id', $currentUser->id)->pluck('user_id')->toArray();
        $excludedIds = array_merge($blockedIds, $blockedByIds, [$currentUser->id]);

        $currentUserAge = $currentUser->birth_date->diffInYears(now());
        $currentUserAnswers = $currentUser->userAnswers->pluck('answer', 'question_id')->toArray();

        return User::where('gender', $currentUser->gender === Gender::Male ? Gender::Female : Gender::Male)
            ->whereBetween('birth_date', [
                now()->subYears($currentUserAge),
                now()->subYears($currentUserAge - 10),
            ])
            ->whereNotIn('id', $excludedIds)
            ->with('userAnswers')
            ->get()
            ->map(function ($user) use ($currentUserAnswers) {
                $targetUserAnswers = $user->userAnswers->pluck('answer', 'question_id')->toArray();
                $totalWeight = 0;
                $matchedWeight = 0;

                foreach ($currentUserAnswers as $questionId => $currentAnswer) {
                    if (isset($targetUserAnswers[$questionId])) {
                        $question = \App\Models\Question::find($questionId);
                        $targetAnswer = $targetUserAnswers[$questionId];
                        $weight = $question->weight ?? 1;

                        $totalWeight += $weight;
                        if (json_encode($currentAnswer) === json_encode($targetAnswer)) {
                            $matchedWeight += $weight;
                        }
                    }
                }

                $matchPercentage = $totalWeight > 0 ? round(($matchedWeight / $totalWeight) * 100, 1) : 0;
                $user->match_percentage = $matchPercentage;
                return $user;
            })
            ->sortByDesc('match_percentage')
            ->take($this->perPage);
    }
}
