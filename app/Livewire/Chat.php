<?php

namespace App\Livewire;

use App\Enums\ChatStatus;
use App\Models\ChatMessage;
use App\Models\User;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

/**
 * Class Chat
 *
 * A Livewire component to manage chat functionality between users with polling.
 *
 * @package App\Livewire
 * @property int|null $selectedUserId The ID of the user currently selected for chatting.
 * @property string $message The message content to be sent.
 * @property array $messages The list of messages in the current conversation.
 * @property array $users The list of users available for chatting.
 * @property bool $isTyping Indicates if the selected user is typing.
 */
class Chat extends Component
{
    public $selectedUserId = null;
    public $message = '';
    public $messages = [];
    public $users = [];
    public $isTyping = false;

    protected $firebaseService;

    public function __construct($id = null)
    {
        parent::__construct($id);
        $this->firebaseService = app(FirebaseService::class);
    }

    public function mount(): void
    {
        $this->loadUsers();
    }

    public function loadUsers(): void
    {
        $currentUser = Auth::user();

        $friends = $currentUser->friends()->pluck('target_id');
        $chatPartners = ChatMessage::where('sender_id', $currentUser->id)->orWhere('receiver_id', $currentUser->id)->pluck('receiver_id', 'sender_id')->unique();
        $blocked = $currentUser->blocks()->pluck('target_id');

        $this->users = User::whereIn('id', $friends->merge($chatPartners))
            ->whereNotIn('id', $blocked)
            ->where('gender', '!=', $currentUser->gender)
            ->whereRaw('ABS(YEAR(birth_date) - YEAR(?)) <= 10', [$currentUser->birth_date])
            ->where('id', '!=', $currentUser->id)
            ->select('id', 'display_name', 'last_seen')
            ->get()
            ->map(function ($user) {
                $user->is_online = $user->last_seen && now()->diffInMinutes($user->last_seen) < 5;
                return $user;
            })
            ->toArray();
    }

    public function selectUser(int $userId): void
    {
        $this->selectedUserId = $userId;
        $this->loadMessages();
        $this->markAsRead();
        $this->isTyping = false;
    }

    public function loadMessages(): void
    {
        if ($this->selectedUserId) {
            $this->messages = ChatMessage::between(Auth::id(), $this->selectedUserId)->orderBy('created_at', 'asc')->get()->toArray();
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

    public function markAsRead(): void
    {
        if ($this->selectedUserId) {
            ChatMessage::withStatus($this->selectedUserId, ChatStatus::Delivered)
                ->where('receiver_id', Auth::id())
                ->update([
                    'status' => ChatStatus::Read,
                    'updated_at' => now(),
                ]);
            $this->loadMessages();
        }
    }

    public function typing(): void
    {
        if ($this->selectedUserId) {
            // به جای dispatch، تو کش می‌ذاریم
            Cache::put("typing_" . Auth::id() . "_to_{$this->selectedUserId}", true, 3); // 3 ثانیه
        }
    }

    public function checkTyping(): void
    {
        if ($this->selectedUserId) {
            $this->isTyping = Cache::get("typing_{$this->selectedUserId}_to_" . Auth::id(), false);
        }
    }

    public function resetTyping(): void
    {
        $this->isTyping = false;
    }

    private function dispatchFirebaseNotification(ChatMessage $chatMessage): void
    {
        $receiver = User::find($chatMessage->receiver_id);

        if ($receiver && $receiver->fcm_token) {
            try {
                $this->firebaseService->sendNotification($receiver->fcm_token, 'New Message from ' . Auth::user()->display_name, $chatMessage->content);
                $chatMessage->update([
                    'status' => ChatStatus::Delivered,
                    'delivered_at' => now(),
                ]);
                $this->loadMessages();
            } catch (\Exception $e) {
                // Handle notification failure
            }
        }
    }

    public function render()
    {
        Auth::user()->update(['last_seen' => now()]);
        return view('livewire.chat')
            ->with(['page_title' => 'Chat'])
            ->layout('components.layouts.app');
    }
}
