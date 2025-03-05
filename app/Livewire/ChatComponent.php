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

    public function mount($user)
    {
        $this->receiverId = $user;
        $this->loadMessages();
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
        \Log::info("Sending FCM notification to user {$receiver->id} with token: {$fcmToken}");
        $this->sendFcmNotification($fcmToken, $sender->display_name, $this->messageContent, $message);
    } else {
        \Log::warning("No FCM token found for user {$receiver->id}");
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
                ->withData([
                    'message_id' => (string) $message->id,
                    'sender_id' => (string) $message->sender_id,
                    'receiver_id' => (string) $message->receiver_id,
                ]);

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
