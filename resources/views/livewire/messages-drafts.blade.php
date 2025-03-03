<?php

use App\Enums\MessageStatus;
use App\Models\Message;
use Livewire\Volt\Component;

new class extends Component {
    public $selectedMessageId;

    public function loadMessages()
    {
        $this->messages = Message::where('sender_id', auth()->id())
            ->where('status', MessageStatus::Draft->value)
            ->where('is_deleted', false)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function selectMessage($messageId): void
    {
        $this->selectedMessageId = $messageId;
        $this->loadMessages();
    }

    public function mount()
    {
        $this->loadMessages();
    }
}; ?>

<x-messages.layout heading="Drafts" subheading="Manage your draft messages">
    <div>
        @if ($messages->isEmpty())
            <p class="text-muted p-3">No draft messages yet.</p>
        @else
            <ul class="list-group list-group-flush">
                @foreach ($messages as $message)
                    <li class="list-group-item {{ $selectedMessageId == $message->id ? 'active' : '' }}" wire:click="selectMessage({{ $message->id }})" style="cursor: pointer;">
                        <div class="d-flex justify-content-between">
                            <div>
                                <strong>{{ $message->receiver->display_name ?? 'Unknown' }}</strong>
                                <p class="text-muted mb-0">{{ \Illuminate\Support\Str::limit($message->subject, 30) }}</p>
                            </div>
                            <small class="text-muted">Draft</small>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif

        @if ($selectedMessage)
            <div class="mt-3">
                <h5>{{ $selectedMessage->subject }}</h5>
                <p class="text-muted">
                    To: {{ $selectedMessage->receiver->display_name ?? 'Unknown' }}
                    - Draft
                </p>
                <hr>
                <p>{{ $selectedMessage->message }}</p>
            </div>
        @endif
    </div>
</x-messages.layout>
