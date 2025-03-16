<?php

namespace App\Livewire;

use App\Enums\ChatStatus;
use App\Models\ChatMessage;
use App\Models\User;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Chat extends Component
{
    public $selectedUserId = null;
    public $message = '';
    public $messages = [];
    public $users = [];
    public $isTyping = false;
    public $searchQuery = '';

    protected $firebaseService;

    public function mount(): void
    {
        try {
            $this->firebaseService = app(FirebaseService::class);
            Log::info('FirebaseService mounted successfully');
        } catch (\Exception $e) {
            Log::error('Failed to mount FirebaseService', ['error' => $e->getMessage()]);
            $this->firebaseService = null;
        }
        $this->loadUsers();
        $this->updateDeliveredStatus();
    }

    public function loadUsers(): void
    {
        $query = User::where('id', '!=', Auth::id());

        if (!empty($this->searchQuery)) {
            $query->where('display_name', 'like', '%' . $this->searchQuery . '%');
        }

        $this->users = Cache::remember('users_' . Auth::id() . '_' . md5($this->searchQuery), 60, function () use ($query) {
            $users = $query->get(['id', 'display_name', 'last_seen'])->map(function ($user) {
                $user->is_online = $user->last_seen && now()->diffInMinutes($user->last_seen) <= 5; // آنلاین اگه تو ۵ دقیقه اخیر دیده شده
                return $user;
            })->toArray();

            return $users;
        });
    }

    public function selectUser(int $userId): void
    {
        $this->selectedUserId = $userId;
        $this->loadMessages();
        $this->markAsRead();
    }

    public function loadMessages(): void
    {
        if ($this->selectedUserId) {
            $this->messages = ChatMessage::where(function ($query) {
                $query->where('sender_id', Auth::id())
                      ->where('receiver_id', $this->selectedUserId);
            })->orWhere(function ($query) {
                $query->where('sender_id', $this->selectedUserId)
                      ->where('receiver_id', Auth::id());
            })->orderBy('created_at', 'asc')->get()->toArray();
        }
    }

    public function sendMessage(): void
    {
        if (!$this->selectedUserId || empty(trim($this->message))) {
            return;
        }

        $chatMessage = ChatMessage::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $this->selectedUserId,
            'content' => $this->message,
            'status' => ChatStatus::Sent,
            'delivered_at' => null,
        ]);

        $this->message = '';
        $this->loadMessages();
        $this->dispatchFirebaseNotification($chatMessage);
    }

    private function dispatchFirebaseNotification(ChatMessage $chatMessage): void
    {
        $receiver = User::find($chatMessage->receiver_id);

        if ($receiver && $receiver->fcm_token) {
            if (!$this->firebaseService) {
                Log::error('FirebaseService is null in dispatchFirebaseNotification');
                try {
                    $this->firebaseService = app(FirebaseService::class);
                    Log::info('FirebaseService re-initialized in dispatchFirebaseNotification');
                } catch (\Exception $e) {
                    Log::error('Failed to re-initialize FirebaseService', ['error' => $e->getMessage()]);
                    return;
                }
            }

            try {
                $this->firebaseService->sendNotification(
                    $receiver->fcm_token,
                    'New Message from ' . Auth::user()->display_name,
                    $chatMessage->content
                );
                $chatMessage->update([
                    'status' => ChatStatus::Delivered,
                    'delivered_at' => now(),
                ]);
                $this->loadMessages();
            } catch (\Exception $e) {
                Log::error('Failed to send Firebase notification', ['error' => $e->getMessage()]);
            }
        }
    }

    public function markAsRead(): void
    {
        if ($this->selectedUserId) {
            ChatMessage::where('receiver_id', Auth::id())
                ->where('sender_id', $this->selectedUserId)
                ->where('status', ChatStatus::Delivered)
                ->update(['status' => ChatStatus::Read, 'read_at' => now()]);
            $this->loadMessages();
        }
    }

    public function updateDeliveredStatus(): void
    {
        ChatMessage::where('receiver_id', Auth::id())
            ->where('status', ChatStatus::Sent)
            ->update(['status' => ChatStatus::Delivered, 'delivered_at' => now()]);
    }

    public function typing(): void
    {
        $this->isTyping = true;
        Cache::put('typing_status_' . Auth::id(), true, 5);
    }

    public function render()
    {
        $this->loadUsers(); // برای آپدیت لیست با جستجو
        Auth::user()->update(['last_seen' => now()]);
        return view('livewire.chat')
            ->with(['page_title' => 'Chat'])
            ->layout('components.layouts.app');
    }
}
