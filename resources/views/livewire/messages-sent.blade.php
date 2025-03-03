<?php

use App\Enums\MessageStatus;
use App\Models\Message;
use Livewire\Volt\Component;

new class extends Component {
    public $messages;

    public function loadMessages()
    {
        $this->messages = Message::where('sender_id', auth()->id())
            ->where('status', MessageStatus::Sent->value)
            ->where('is_deleted', false)
            ->orderBy('sent_at', 'desc')
            ->get();
    }

    public function redirectToRead($messageId)
    {
        $this->redirect(route('messages.read', $messageId));
    }

    public function mount()
    {
        $this->loadMessages();
    }
}; ?>

<x-messages.layout heading="Sent" subheading="Manage your sent messages">
    <div>
        @if ($messages->isEmpty())
            <p class="text-muted p-3">No sent messages yet.</p>
        @else
            <ul class="list-group list-group-flush">
                @foreach ($messages as $message)
                    <li class="list-group-item" wire:click="redirectToRead({{ $message->id }})" style="cursor: pointer;">
                        <div class="d-flex justify-content-between">
                            <div>
                                <strong>{{ $message->receiver->display_name ?? 'Unknown' }}</strong>
                                <p class="text-muted mb-0">{{ \Illuminate\Support\Str::limit($message->subject, 30) }}</p>
                            </div>
                            <small class="text-muted">{{ $message->sent_at->diffForHumans() }}</small>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</x-messages.layout>
