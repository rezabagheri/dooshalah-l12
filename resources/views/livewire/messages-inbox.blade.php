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
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <img src="{{ $message->sender->profilePicture()?->media->path ? asset('storage/' . $message->sender->profilePicture()->media->path) : '/dist/assets/img/user2-160x160.jpg' }}"
                                     class="me-2" style="width: 40px; height: 40px;" alt="Sender Image">
                                <div>
                                    <strong>{{ $message->sender->display_name ?? 'Unknown' }}</strong>
                                    <p class="text-muted mb-0">{{ \Illuminate\Support\Str::limit($message->subject, 30) }}</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <small class="text-muted me-2">{{ $message->sent_at ? $message->sent_at->diffForHumans() : 'Draft' }}</small>
                                @if (!$message->read_at)
                                    <span class="badge bg-primary">New</span>
                                @endif
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</x-messages.layout>
