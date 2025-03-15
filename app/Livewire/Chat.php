<?php

namespace App\Livewire;

use App\Enums\ChatStatus;
use App\Models\ChatMessage;
use App\Models\User;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

/**
 * Class Chat
 *
 * A Livewire component to manage real-time chat functionality between users.
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
    /**
     * The ID of the user selected for chatting.
     *
     * @var int|null
     */
    public $selectedUserId = null;

    /**
     * The content of the message to be sent.
     *
     * @var string
     */
    public $message = '';

    /**
     * The list of messages in the current conversation.
     *
     * @var array
     */
    public $messages = [];

    /**
     * The list of users available for chatting.
     *
     * @var array
     */
    public $users = [];

    /**
     * Indicates if the selected user is typing.
     *
     * @var bool
     */
    public $isTyping = false;

    /**
     * The Firebase service instance for sending notifications.
     *
     * @var FirebaseService
     */
    protected $firebaseService;

    /**
     * Chat constructor.
     *
     * @param FirebaseService $firebaseService The Firebase service instance.
     */
    public function __construct($id = null)
    {
        parent::__construct($id);
        $this->firebaseService = app(FirebaseService::class);
    }

    /**
     * Mount the component and load initial data.
     *
     * @return void
     */
    public function mount(): void
    {
        $this->loadUsers();
    }

    /**
     * Load the list of users available for chatting with online status.
     *
     * @return void
     */
    public function loadUsers(): void
    {
        $currentUser = Auth::user();

        // Get friends and chat partners
        $friends = $currentUser->friends()->pluck('target_id');
        $chatPartners = ChatMessage::where('sender_id', $currentUser->id)->orWhere('receiver_id', $currentUser->id)->pluck('receiver_id', 'sender_id')->unique();

        // Get blocked users
        $blocked = $currentUser->blocks()->pluck('target_id');

        // Load users with online status
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

    /**
     * Select a user and load their messages.
     *
     * @param int $userId The ID of the user to select.
     * @return void
     */
    public function selectUser(int $userId): void
    {
        $this->selectedUserId = $userId;
        $this->loadMessages();
        $this->markAsRead();
        $this->isTyping = false; // Reset typing indicator
    }

    /**
     * Load messages between the authenticated user and the selected user.
     *
     * @return void
     */
    public function loadMessages(): void
    {
        if ($this->selectedUserId) {
            $this->messages = ChatMessage::between(Auth::id(), $this->selectedUserId)->orderBy('created_at', 'asc')->get()->toArray();
        }
    }

    /**
     * Send a new message to the selected user.
     *
     * @return void
     */
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
            'delivered_at' => null, // Will be set when delivered
        ]);

        $this->message = ''; // Clear the input
        $this->loadMessages(); // Refresh messages
        $this->dispatchFirebaseNotification($chatMessage);
    }

    /**
     * Mark unread messages from the selected user as read.
     *
     * @return void
     */
    public function markAsRead(): void
    {
        if ($this->selectedUserId) {
            ChatMessage::withStatus($this->selectedUserId, ChatStatus::Delivered)
                ->where('receiver_id', Auth::id())
                ->update([
                    'status' => ChatStatus::Read,
                    'updated_at' => now(), // For showing read time
                ]);
            $this->loadMessages();
        }
    }

    /**
     * Handle typing event from the client.
     *
     * @return void
     */
    public function typing(): void
    {
        if ($this->selectedUserId) {
            $this->dispatch('userTyping', ['userId' => Auth::id(), 'toUserId' => $this->selectedUserId]);
        }
    }

    /**
     * Handle incoming typing events from other users.
     *
     * @param array $event The typing event data.
     * @return void
     */
    #[On('userTyping')]
    public function onUserTyping(array $event): void
    {
        if ($this->selectedUserId && $event['userId'] == $this->selectedUserId && $event['toUserId'] == Auth::id()) {
            $this->isTyping = true;
            // Reset after a few seconds
            $this->dispatch('resetTyping')->delay(3000);
        }
    }

    /**
     * Reset the typing indicator.
     *
     * @return void
     */
    #[On('resetTyping')]
    public function resetTyping(): void
    {
        $this->isTyping = false;
    }

    /**
     * Send a Firebase notification for the new message and mark as delivered.
     *
     * @param ChatMessage $chatMessage The message to notify about.
     * @return void
     */
    private function dispatchFirebaseNotification(ChatMessage $chatMessage): void
    {
        $receiver = User::find($chatMessage->receiver_id);

        if ($receiver && $receiver->fcm_token) {
            try {
                $this->firebaseService->sendNotification($receiver->fcm_token, 'New Message from ' . Auth::user()->display_name, $chatMessage->content);
                // Mark as delivered after successful notification
                $chatMessage->update([
                    'status' => ChatStatus::Delivered,
                    'delivered_at' => now(),
                ]);
                $this->loadMessages();
            } catch (\Exception $e) {
                // Handle notification failure (e.g., log it)
            }
        }
    }

    /**
     * Render the chat component view.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        Auth::user()->update(['last_seen' => now()]);
        return view('livewire.chat')
            ->with(['page_title' => 'Chat'])
            ->layout('components.layouts.app');
    }
}
