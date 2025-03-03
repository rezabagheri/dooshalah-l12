<?php

use App\Enums\MessageStatus;
use App\Models\Message;
use Livewire\Volt\Component;

new class extends Component {
    public $messages;

    public function loadMessages()
    {
        $this->messages = Message::where('receiver_id', auth()->id())
            ->where('is_deleted', false)
            ->orderBy('sent_at', 'desc')
            ->get();
    }

    public function markAsReadAndRedirect($messageId)
    {
        $message = Message::find($messageId);
        if ($message && !$message->read_at && $message->receiver_id === auth()->id()) {
            $message->update(['read_at' => now()]);
        }
        $this->redirect(route('messages.read', $messageId));
    }

    public function mount()
    {
        $this->loadMessages();
    }
}; ?>

<x-messages.layout heading="Inbox" subheading="Manage your received messages">
    <div>
        @if ($messages->isEmpty())
            <p class="text-muted p-3">No inbox messages yet.</p>
        @else
            <ul class="list-group list-group-flush">
                @foreach ($messages as $message)
                    <li class="list-group-item" wire:click="markAsReadAndRedirect({{ $message->id }})" style="cursor: pointer;">
                        <div class="d-flex justify-content-between">
                            <div>
                                <strong>{{ $message->sender->display_name ?? 'Unknown' }}</strong>
                                <p class="text-muted mb-0">{{ \Illuminate\Support\Str::limit($message->subject, 30) }}</p>
                            </div>
                            <small class="text-muted">{{ $message->sent_at ? $message->sent_at->diffForHumans() : 'Draft' }}</small>
                        </div>
                        @if (!$message->read_at)
                            <span class="badge bg-primary position-absolute top-0 end-0 mt-2 me-2">New</span>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</x-messages.layout>
