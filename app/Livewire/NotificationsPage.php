<?php

namespace App\Livewire;

use App\Enums\NotificationType;
use App\Models\Notification;
use Livewire\Component;

class NotificationsPage extends Component
{
    public $notifications;

    public function mount()
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        $this->notifications = Notification::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function markAsRead($notificationId)
    {
        $notification = Notification::findOrFail($notificationId);
        if ($notification->user_id === auth()->id() && !$notification->is_read) {
            $notification->update(['is_read' => true, 'read_at' => now()]);
            $this->loadNotifications();
            $this->dispatch('notification-updated'); // رویداد برای اطلاع‌رسانی
        }
    }

    public function markAllAsRead()
    {
        Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);
        $this->loadNotifications();
        $this->dispatch('notification-updated'); // رویداد برای اطلاع‌رسانی
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

    public function render()
    {
        return view('livewire.notifications-page');
    }
}
