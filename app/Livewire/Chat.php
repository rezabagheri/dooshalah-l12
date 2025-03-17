<?php

namespace App\Livewire;

use App\Enums\ChatStatus;
use App\Models\ChatMessage;
use App\Models\User;
use App\Services\FirebaseService;
use App\Traits\HasFeatureAccess;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

/**
 * Class Chat
 *
 * A Livewire component for handling real-time chat functionality between users.
 *
 * @package App\Livewire
 */
class Chat extends Component
{
    use HasFeatureAccess;

    /** @var int|null The ID of the selected user to chat with */
    public $selectedUserId = null;

    /** @var string The message content to be sent */
    public $message = '';

    /** @var array The list of chat messages between the current user and the selected user */
    public $messages = [];

    /** @var array The list of users available to chat with */
    public $users = [];

    /** @var bool Indicates if the selected user is typing */
    public $isTyping = false;

    /** @var string The search query for filtering users */
    public $searchQuery = '';

    /** @var array|null Information about the selected user */
    public $selectedUser = null;

    /** @var bool Controls the visibility of the sticker popup */
    public $showStickerPopup = false;

    /** @var bool Indicates if the current user has access to chat features */
    public $hasChatAccess = false;

    /** @var int The total count of unread messages for the current user */
    public $unreadMessagesCount = 0;

    /** @var FirebaseService|null The Firebase service instance for sending notifications */
    protected $firebaseService;

    /**
     * Mount the component and initialize necessary services.
     *
     * @return void
     */
    public function mount(): void
    {
        try {
            $this->firebaseService = app(FirebaseService::class);
            Log::info('FirebaseService mounted successfully');
        } catch (\Exception $e) {
            Log::error('Failed to mount FirebaseService', ['error' => $e->getMessage()]);
            $this->firebaseService = null;
        }

        $this->hasChatAccess = $this->hasFeatureAccess('use_chat');
        $this->loadUsers();
        $this->updateUnreadMessagesCount();

        if ($this->hasChatAccess) {
            $this->updateDeliveredStatus();
        }
    }

    /**
     * Load the list of users available for chatting.
     *
     * @return void
     */
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
            return $query->get(['id', 'display_name', 'last_seen'])->map(function ($user) {
                $isOnline = $user->last_seen && now()->diffInMinutes($user->last_seen) <= 5;
                $profilePicture = $user->profilePicture();
                $lastSeenText = $isOnline ? 'Online' : ($user->last_seen ? 'Left ' . $user->last_seen->diffForHumans() : 'Never seen');

                return [
                    'id' => $user->id,
                    'display_name' => $user->display_name,
                    'is_online' => $isOnline,
                    'profile_photo_path' => $profilePicture && $profilePicture->media ? asset('storage/' . $profilePicture->media->path) : null,
                    'last_seen_text' => $lastSeenText,
                    'unread_messages' => ChatMessage::where('sender_id', $user->id)
                        ->where('receiver_id', Auth::id())
                        ->where('status', ChatStatus::Delivered->value)
                        ->count(),
                ];
            })->toArray();
        });
    }

    /**
     * Select a user to start chatting with.
     *
     * @param int $userId The ID of the user to select
     * @return void
     */
    public function selectUser(int $userId): void
    {
        $this->selectedUserId = $userId;
        $this->loadSelectedUser();
        if ($this->hasChatAccess) {
            $this->loadMessages();
            $this->markAsRead();
        }
        $this->updateUnreadMessagesCount();
    }

    /**
     * Load details of the selected user.
     *
     * @return void
     */
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

    /**
     * Load chat messages between the current user and the selected user.
     *
     * @return void
     */
    public function loadMessages(): void
    {
        if ($this->selectedUserId && $this->hasChatAccess) {
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

    /**
     * Send a text message to the selected user.
     *
     * @return void
     */
    public function sendMessage(): void
    {
        if (!$this->hasChatAccess) {
            return; // No redirect; UI handles the restriction
        }

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
        $this->updateUnreadMessagesCount();
    }

    /**
     * Send a sticker message to the selected user.
     *
     * @param string $sticker The sticker content to send
     * @return void
     */
    public function sendSticker(string $sticker): void
    {
        if (!$this->hasChatAccess) {
            return; // No redirect; UI handles the restriction
        }

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
        $this->updateUnreadMessagesCount();
    }

    /**
     * Toggle the visibility of the sticker popup.
     *
     * @return void
     */
    public function toggleStickerPopup(): void
    {
        if ($this->hasChatAccess) {
            $this->showStickerPopup = !$this->showStickerPopup;
        }
    }

    /**
     * Dispatch a Firebase notification for the sent message.
     *
     * @param ChatMessage $chatMessage The message to notify about
     * @return void
     */
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

    /**
     * Mark messages from the selected user as read.
     *
     * @return void
     */
    public function markAsRead(): void
    {
        if ($this->selectedUserId && $this->hasChatAccess) {
            ChatMessage::withStatus(Auth::id(), ChatStatus::Delivered)
                ->where('sender_id', $this->selectedUserId)
                ->update(['status' => ChatStatus::Read, 'read_at' => now()]);
            $this->loadMessages();
            $this->updateUnreadMessagesCount();
        }
    }

    /**
     * Update the status of sent messages to delivered.
     *
     * @return void
     */
    public function updateDeliveredStatus(): void
    {
        if ($this->hasChatAccess) {
            ChatMessage::withStatus(Auth::id(), ChatStatus::Sent)
                ->update(['status' => ChatStatus::Delivered, 'delivered_at' => now()]);
            $this->updateUnreadMessagesCount();
        }
    }

    /**
     * Indicate that the current user is typing.
     *
     * @return void
     */
    public function typing(): void
    {
        if ($this->hasChatAccess) {
            $this->isTyping = true;
            Cache::put('typing_status_' . Auth::id(), true, 5);
        }
    }

    /**
     * Update the count of unread messages for the current user.
     *
     * @return void
     */
    private function updateUnreadMessagesCount(): void
    {
        $this->unreadMessagesCount = ChatMessage::where('receiver_id', Auth::id())
            ->where('status', ChatStatus::Delivered->value)
            ->count();
    }

    /**
     * Render the chat component view.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        $this->loadUsers();
        if ($this->selectedUserId) {
            $this->loadSelectedUser();
        }
        $this->updateUnreadMessagesCount();
        Auth::user()->update(['last_seen' => now()]);
        return view('livewire.chat')
            ->with(['page_title' => 'Chat'])
            ->layout('components.layouts.app');
    }
}
