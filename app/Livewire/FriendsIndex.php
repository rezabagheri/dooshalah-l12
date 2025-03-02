<?php

namespace App\Livewire;

use App\Enums\FriendshipStatus;
use App\Enums\Gender;
use App\Models\Block;
use App\Models\Friendship;
use App\Models\Report;
use App\Models\User;
use App\Models\UserMatchScore;
use Livewire\Component;
use Livewire\WithPagination;

class FriendsIndex extends Component
{
    use WithPagination;

    public string $activeTab = 'suggestions';
    public int $perPage = 15;
    public bool $isLoading = false;

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
        $this->isLoading = true;
        $this->perPage += 15;
        $this->isLoading = false;
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

        return User::where('gender', $currentUser->gender === Gender::Male ? Gender::Female : Gender::Male)
            ->whereBetween('birth_date', [
                now()->subYears($currentUserAge),
                now()->subYears($currentUserAge - 10),
            ])
            ->whereNotIn('users.id', $excludedIds) // مشخص کردن جدول users
            ->join('user_match_scores', function ($join) use ($currentUser) {
                $join->on('users.id', '=', 'user_match_scores.target_id')
                     ->where('user_match_scores.user_id', $currentUser->id);
            })
            ->select('users.*', 'user_match_scores.match_score as match_percentage')
            ->orderByDesc('match_score')
            ->take($this->perPage)
            ->get();
    }
}
