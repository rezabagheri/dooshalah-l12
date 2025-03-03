<?php

namespace App\Livewire;

use App\Enums\MessageStatus;
use App\Models\Message;
use Livewire\Attributes\Layout;
use Livewire\Component;

class MessagesSent extends Component
{
    public $selectedMessageId;

    public function loadMessages()
    {
        $this->messages = Message::where('sender_id', auth()->id())
            ->where('status', MessageStatus::Sent->value)
            ->where('is_deleted', false)
            ->orderBy('sent_at', 'desc')
            ->get();
    }

    public function selectMessage($messageId)
    {
        $this->selectedMessageId = $messageId;
        $this->loadMessages();
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        $this->loadMessages();
        return view('livewire.messages-sent', [
            'messages' => $this->messages,
            'selectedMessage' => $this->selectedMessageId ? Message::find($this->selectedMessageId) : null,
            'heading' => 'Sent',
            'subheading' => 'Manage your sent messages',
        ])->layout('components.messages.layout');
    }
}
