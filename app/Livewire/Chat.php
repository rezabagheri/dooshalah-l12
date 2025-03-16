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
    public $selectedUser = null;
    public $showStickerPopup = false;

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
        $query = User::where('id', '!=', Auth::id())
            ->with(['media' => function ($query) {
                $query->where('is_profile', true);
            }]);

        if (!empty($this->searchQuery)) {
            $query->where('display_name', 'like', '%' . $this->searchQuery . '%');
        }

        $this->users = Cache::remember('users_' . Auth::id() . '_' . md5($this->searchQuery), 60, function () use ($query) {
            $users = $query->get(['id', 'display_name', 'last_seen'])->map(function ($user) {
                $isOnline = $user->last_seen && now()->diffInMinutes($user->last_seen) <= 5;
                $profilePicture = $user->profilePicture();
                $lastSeenText = $isOnline ? 'Online' : ($user->last_seen ? 'Left ' . $user->last_seen->diffForHumans() : 'Never seen');

                return [
                    'id' => $user->id,
                    'display_name' => $user->display_name,
                    'is_online' => $isOnline,
                    'profile_photo_path' => $profilePicture && $profilePicture->media ? asset('storage/' . $profilePicture->media->path) : null,
                    'last_seen_text' => $lastSeenText,
                ];
            })->toArray();

            return $users;
        });
    }

    public function selectUser(int $userId): void
    {
        $this->selectedUserId = $userId;
        $this->loadSelectedUser();
        $this->loadMessages();
        $this->markAsRead();
    }

    public function loadSelectedUser(): void
    {
        if ($this->selectedUserId) {
            $user = User::with(['media' => function ($query) {
                $query->where('is_profile', true);
            }])->find($this->selectedUserId, ['id', 'display_name', 'last_seen']);

            if ($user) {
                $isOnline = $user->last_seen && now()->diffInMinutes($user->last_seen) <= 5;
                $profilePicture = $user->profilePicture();
                $this->selectedUser = [
                    'display_name' => $user->display_name,
                    'is_online' => $isOnline,
                    'profile_photo_path' => $profilePicture && $profilePicture->media ? asset('storage/' . $profilePicture->media->path) : null,
                    'last_seen_text' => $isOnline ? 'Online' : ($user->last_seen ? 'Left ' . $user->last_seen->diffForHumans() : 'Never seen'),
                ];
            }
        }
    }

    public function loadMessages(): void
    {
        if ($this->selectedUserId) {
            $this->messages = ChatMessage::between(Auth::id(), $this->selectedUserId)
                ->with(['sender.media' => function ($query) {
                    $query->where('is_profile', true);
                }])
                ->orderBy('created_at', 'asc')
                ->get()
                ->map(function ($message) {
                    $profilePicture = $message->sender->profilePicture();
                    return [
                        'sender_id' => $message->sender_id,
                        'content' => $message->content,
                        'status' => $message->status->value,
                        'created_at' => $message->created_at->format('H:i'),
                        'profile_photo_path' => $profilePicture && $profilePicture->media ? asset('storage/' . $profilePicture->media->path) : null,
                    ];
                })->toArray();
            $this->dispatch('scrollToBottom');
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

        Log::info('Message sent, resetting input', ['message' => $this->message]);
        $this->message = '';
        $this->reset('message');
        $this->dispatch('messageSent');
        $this->loadMessages();
        $this->dispatchFirebaseNotification($chatMessage);
    }

    public function sendSticker(string $sticker): void
    {
        if (!$this->selectedUserId || empty($sticker)) {
            return;
        }

        $chatMessage = ChatMessage::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $this->selectedUserId,
            'content' => $sticker,
            'status' => ChatStatus::Sent,
            'delivered_at' => null,
        ]);

        $this->showStickerPopup = false;
        $this->message = '';
        $this->reset('message');
        $this->dispatch('messageSent');
        $this->loadMessages();
        $this->dispatchFirebaseNotification($chatMessage);
    }

    public function toggleStickerPopup(): void
    {
        $this->showStickerPopup = !$this->showStickerPopup;
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
            ChatMessage::withStatus(Auth::id(), ChatStatus::Delivered)
                ->where('sender_id', $this->selectedUserId)
                ->update(['status' => ChatStatus::Read, 'read_at' => now()]);
            $this->loadMessages();
        }
    }

    public function updateDeliveredStatus(): void
    {
        ChatMessage::withStatus(Auth::id(), ChatStatus::Sent)
            ->update(['status' => ChatStatus::Delivered, 'delivered_at' => now()]);
    }

    public function typing(): void
    {
        $this->isTyping = true;
        Cache::put('typing_status_' . Auth::id(), true, 5);
    }

    public function render()
    {
        $this->loadUsers();
        if ($this->selectedUserId) {
            $this->loadSelectedUser();
        }
        Auth::user()->update(['last_seen' => now()]);
        return view('livewire.chat')
            ->with(['page_title' => 'Chat'])
            ->layout('components.layouts.app');
    }
}
