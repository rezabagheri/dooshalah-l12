<?php

namespace App\Livewire\Layouts;

use Livewire\Volt\Component;
use Illuminate\Contracts\View\View;

class UserMenu extends Component
{
    public function render(): View
    {
        $user = auth()->user();

        return view('components.layouts.user-menu', [
            'user' => $user,
            'profilePicture' => $user->profilePicture()?->media->path ? asset('storage/' . $user->profilePicture()->media->path) : '/dist/assets/img/user2-160x160.jpg',
            'displayName' => $user->display_name ?? 'User',
            'joinDate' => $user->created_at->format('M. Y'),
        ]);
    }
}
