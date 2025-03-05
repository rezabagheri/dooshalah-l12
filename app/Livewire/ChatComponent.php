<?php
namespace App\Livewire;

use App\Models\ChatMessage;
use App\Models\User;
use Livewire\Component;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;

class ChatComponent extends Component
{
    public $receiverId;
    public $messageContent = '';
    public $messages = [];
    public $lastMessageId = 0;

    public function mount($user)
    {
        $this->receiverId = $user;
        $this->loadMessages();
        $this->lastMessageId = ChatMessage::where(function ($query) {
            $query->where('sender_id', auth()->id())
                  ->where('receiver_id', $this->receiverId);
        })->orWhere(function ($query) {
            $query->where('sender_id', $this->receiverId)
                  ->where('receiver_id', auth()->id());
        })->max('id') ?? 0;

        \Log::info("Mounted ChatComponent for user {$this->receiverId}, lastMessageId = {$this->lastMessageId}");
    }

    public function loadMessages()
    {
        $messages = ChatMessage::where(function ($query) {
            $query->where('sender_id', auth()->id())
                  ->where('receiver_id', $this->receiverId);
        })->orWhere(function ($query) {
            $query->where('sender_id', $this->receiverId)
                  ->where('receiver_id', auth()->id());
        })->orderBy('created_at', 'asc')->with('sender')->get();

        \Log::info("Loaded messages for user {$this->receiverId}: " . $messages->count() . " messages");

        $this->messages = $messages->map(function ($message) {
            return [
                'content' => $message->content,
                'sender_display_name' => $message->sender->display_name,
                'created_at' => $message->created_at->diffForHumans(),
            ];
        })->toArray();
    }

    public function checkForNewMessages()
    {
        $newMessageId = ChatMessage::where(function ($query) {
            $query->where('sender_id', auth()->id())
                  ->where('receiver_id', $this->receiverId);
        })->orWhere(function ($query) {
            $query->where('sender_id', $this->receiverId)
                  ->where('receiver_id', auth()->id());
        })->max('id') ?? 0;

        \Log::info("Checking for new messages: lastMessageId = {$this->lastMessageId}, newMessageId = {$newMessageId}");

        if ($newMessageId > $this->lastMessageId) {
            \Log::info("New message detected, loading messages...");
            $this->lastMessageId = $newMessageId;
            $this->loadMessages();
        }
    }

    public function sendMessage()
    {
        $this->validate([
            'messageContent' => 'required|string|max:1000',
        ]);

        $message = ChatMessage::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $this->receiverId,
            'content' => $this->messageContent,
        ]);

        // ارسال نوتیفیکیشن به کاربر گیرنده
        $receiver = User::find($this->receiverId);
        $sender = auth()->user();
        $fcmToken = $receiver->fcmTokens()->first()?->token;

        if ($fcmToken) {
            $this->sendFcmNotification($fcmToken, $sender->display_name, $this->messageContent, $message);
        }

        $this->messageContent = '';
        $this->loadMessages();
    }

    private function sendFcmNotification($fcmToken, $senderName, $messageContent, $message)
    {
        try {
            $factory = (new Factory)->withServiceAccount(config('firebase.credentials'));
            $messaging = $factory->createMessaging();

            $message = CloudMessage::withTarget('token', $fcmToken)
                ->withNotification([
                    'title' => "New Message from $senderName",
                    'body' => $messageContent,
                ])
                ->withData(['message_id' => (string) $message->id]);

            $messaging->send($message);

            \Log::info('FCM notification sent successfully');
        } catch (\Exception $e) {
            \Log::error('Failed to send FCM notification: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.chat-component');
    }
}
