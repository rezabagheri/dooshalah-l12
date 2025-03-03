<?php

namespace App\Livewire;

use App\Enums\MessageStatus;
use App\Models\Message;
use Livewire\Attributes\Layout;
use Livewire\Component;

class ComposeMessage extends Component
{
    public $subject = '';
    public $message = '';
    public $receiverId;

    public function sendMessage()
    {
        $this->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'receiverId' => 'required|exists:users,id',
        ]);

        Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $this->receiverId,
            'subject' => $this->subject,
            'message' => $this->message,
            'sent_at' => now(),
            'status' => MessageStatus::Sent->value,
        ]);

        $this->subject = '';
        $this->message = '';
        $this->receiverId = null;

        session()->flash('success', 'Message sent successfully!');
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('livewire.compose-message', [
            'users' => \App\Models\User::where('id', '!=', auth()->id())->get(),
            'heading' => 'Compose New Message',
            'subheading' => 'Send a new message to someone',
        ])->layout('components.messages.layout');
    }
}
