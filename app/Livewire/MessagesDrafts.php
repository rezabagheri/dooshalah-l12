<?php

namespace App\Livewire;

use App\Enums\MessageStatus;
use App\Models\Message;
use Livewire\Attributes\Layout;
use Livewire\Component;

class MessagesDrafts extends Component
{
    public $selectedMessageId;

    public function loadMessages()
    {
        $this->messages = Message::where('sender_id', auth()->id())
            ->where('status', MessageStatus::Draft->value)
            ->where('is_deleted', false)
            ->orderBy('created_at', 'desc')
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
        return view('livewire.messages-drafts', [
            'messages' => $this->messages,
            'selectedMessage' => $this->selectedMessageId ? Message::find($this->selectedMessageId) : null,
            'heading' => 'Drafts',
            'subheading' => 'Manage your draft messages',
        ])->layout('components.messages.layout');
    }
}
