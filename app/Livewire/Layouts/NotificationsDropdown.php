<?php

namespace App\Livewire\Layouts;

use App\Models\Notification;
use App\Enums\NotificationType;
use Livewire\Volt\Component;
use Illuminate\Contracts\View\View;

class NotificationsDropdown extends Component
{
    public $notifications = [];
    public $notificationCount = 0;

    protected $listeners = [
        'notification-updated' => 'loadNotifications', // Listener برای رویداد
    ];

    public function mount()
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        $this->notifications = Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($notification) {
                return [
                    'icon' => $this->getIconForType($notification->type),
                    'text' => $notification->title,
                    'time' => $notification->created_at->diffForHumans(),
                    'action_url' => $notification->action_url,
                ];
            })->toArray();

        $this->notificationCount = Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->count();
    }

    private function getIconForType(NotificationType $type)
    {
        return match ($type) {
            NotificationType::FriendRequest => 'bi bi-person-plus-fill',
            NotificationType::FriendAccepted => 'bi bi-person-check-fill',
            NotificationType::PaymentSuccess => 'bi bi-cash-stack',
            NotificationType::PaymentFailed => 'bi bi-exclamation-circle',
            NotificationType::NewMessage => 'bi bi-envelope',
            NotificationType::NewChatMessage => 'bi bi-chat-dots',
            NotificationType::AdminMessage => 'bi bi-megaphone',
            NotificationType::Other => 'bi bi-bell',
        };
    }

    public function markAsRead($notificationId)
    {
        $notification = Notification::findOrFail($notificationId);
        $notification->update(['is_read' => true, 'read_at' => now()]);
        $this->loadNotifications();
    }

    public function render(): View
    {
        return view('components.layouts.notifications-dropdown', [
            'notifications' => $this->notifications,
            'notificationCount' => $this->notificationCount,
        ]);
    }
}
