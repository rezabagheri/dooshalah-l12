<?php

namespace App\Livewire;

use App\Enums\MessageStatus;
use App\Models\Message;
use Livewire\Attributes\Layout;
use Livewire\Component;

class MessagesInbox extends Component
{
    public $selectedMessageId;

    public function loadMessages()
    {
        $this->messages = Message::where('receiver_id', auth()->id())
            ->where('is_deleted', false)
            ->orderBy('sent_at', 'desc')
            ->get();
    }

    public function selectMessage($messageId)
    {
        $this->selectedMessageId = $messageId;
        $message = Message::find($messageId);
        if ($message && !$message->read_at && $message->receiver_id === auth()->id()) {
            $message->update(['read_at' => now()]);
        }
        $this->loadMessages();
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        $this->loadMessages();
        return view('livewire.messages-inbox', [
            'messages' => $this->messages,
            'selectedMessage' => $this->selectedMessageId ? Message::find($this->selectedMessageId) : null,
            'heading' => 'Inbox',
            'subheading' => 'Manage your received messages',
        ])->layout('components.messages.layout');
    }
}
