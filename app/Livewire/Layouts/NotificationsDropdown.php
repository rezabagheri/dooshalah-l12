<?php

namespace App\Livewire\Layouts;

use Livewire\Volt\Component;
use Illuminate\Contracts\View\View;

class NotificationsDropdown extends Component
{
    public array $notifications = [
        [
            'icon' => 'bi bi-envelope',
            'text' => '4 new messages',
            'time' => '3 mins',
        ],
        [
            'icon' => 'bi bi-people-fill',
            'text' => '8 friend requests',
            'time' => '12 hours',
        ],
        [
            'icon' => 'bi bi-file-earmark-fill',
            'text' => '3 new reports',
            'time' => '2 days',
        ],
    ];

    public int $notificationCount = 15;

    public function render(): View
    {
        return view('components.layouts.notifications-dropdown', [
            'notifications' => $this->notifications,
            'notificationCount' => $this->notificationCount,
        ]);
    }
}
