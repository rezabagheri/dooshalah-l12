<?php

namespace App\Livewire;

use App\Enums\FriendshipStatus;
use App\Models\Block;
use App\Models\Friendship;
use App\Models\Report;
use App\Models\User;
use Livewire\Component;

class FriendsIndex extends Component
{
    public string $activeTab = 'my-friends';

    public function mount(): void
    {
        $this->activeTab = request()->routeIs('friends.my-friends') ? 'my-friends' :
                           (request()->routeIs('friends.pending') ? 'pending' :
                            (request()->routeIs('friends.received') ? 'received' :
                             (request()->routeIs('friends.blocked') ? 'blocked' : 'reports')));
    }

    public function setTab($tab): void
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        $currentUser = auth()->user();

        $tabs = [
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
}
