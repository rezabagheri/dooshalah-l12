<?php

use App\Enums\MessageStatus;
use App\Enums\NotificationType;
use App\Models\Message;
use App\Models\Notification;
use App\Traits\HasFeatureAccess;
use Livewire\Volt\Component;

new class extends Component {
    use HasFeatureAccess;

    public $message;
    public $replySubject = '';
    public $replyMessage = '';

    public function mount($id)
    {
        // چک کردن دسترسی به خواندن پیام
        if (!$this->hasFeatureAccess('read_message')) {
            redirect()->route('plans.upgrade')->with('error', 'Upgrade your plan to read messages.');
            return;
        }

        $this->message = Message::findOrFail($id);
        if ($this->message->receiver_id === auth()->id() && !$this->message->read_at) {
            $this->message->update(['read_at' => now()]);
        }
    }

    public function addEmoji($emojiUnicode)
    {
        \Log::info('Adding emoji to reply message:', ['emojiUnicode' => $emojiUnicode]);
        $this->replyMessage = $this->replyMessage . ($this->replyMessage ? ' ' : '') . $emojiUnicode;
    }

    public function reply()
    {
        // چک کردن دسترسی به ارسال پیام
        if (!$this->hasFeatureAccess('send_message')) {
            redirect()->route('plans.upgrade')->with('error', 'Upgrade your plan to send messages.');
            return;
        }

        $this->validate([
            'replySubject' => 'required|string|max:255',
            'replyMessage' => 'required|string',
        ]);

        $reply = Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $this->message->sender_id,
            'subject' => $this->replySubject,
            'message' => $this->replyMessage,
            'sent_at' => now(),
            'status' => MessageStatus::Sent->value,
            'parent_id' => $this->message->id,
        ]);

        // ارسال اعلان به گیرنده
        Notification::create([
            'user_id' => $this->message->sender_id,
            'sender_id' => auth()->id(),
            'type' => NotificationType::NewMessage->value,
            'title' => \Illuminate\Support\Str::limit(auth()->user()->display_name . ' sent you a new message', 100, '...'),
            'content' => 'Click to view the message.',
            'action_url' => route('messages.read', ['id' => $reply->id]),
            'related_id' => $reply->id,
            'related_type' => Message::class,
            'priority' => 2,
            'is_read' => false,
            'read_at' => null,
            'data' => [
                'sender_name' => auth()->user()->display_name ?? 'Unknown',
                'subject' => $this->replySubject,
            ],
        ]);

        $this->replySubject = '';
        $this->replyMessage = '';
        session()->flash('success', 'Reply sent successfully!');
    }

    public function delete()
    {
        $this->message->update(['is_deleted' => true]);
        redirect()->route('messages.inbox')->with('success', 'Message deleted successfully!');
    }
}; ?>

<x-messages.layout heading="Read Message" subheading="View and manage your message">
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h5 class="card-title">{{ $message->subject }}</h5>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" wire:click="delete" onclick="return confirm('Are you sure you want to delete this message?')">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="mb-3">
                <p class="text-muted">
                    From: {{ $message->sender->display_name ?? 'Unknown' }}
                    - {{ $message->sent_at ? $message->sent_at->format('Y-m-d H:i') : 'Draft' }}
                </p>
                <p class="text-muted">
                    To: {{ $message->receiver->display_name ?? 'Unknown' }}
                </p>
                <hr>
                <p>{{ $message->message }}</p>
            </div>

            <!-- فرم Reply -->
            <form wire:submit="reply" class="form-horizontal">
                <div class="row mb-3">
                    <label for="replySubject" class="col-sm-2 col-form-label">Subject</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="replySubject" wire:model="replySubject" value="Re: {{ $message->subject }}" required>
                        @error('replySubject') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="replyMessage" class="col-sm-2 col-form-label">Message</label>
                    <div class="col-sm-10">
                        <textarea class="form-control" id="replyMessage" rows="4" wire:model="replyMessage" required></textarea>
                        @error('replyMessage') <span class="text-danger">{{ $message }}</span> @enderror

                        <!-- بخش ایموجی‌ها -->
                        <div class="mt-2 d-flex flex-wrap gap-2">
                            @foreach (get_emojis() as $emoji)
                                <span class="emoji" wire:click="addEmoji('{{ $emoji['unicode'] }}')"
                                      style="font-size: 24px; cursor: pointer; transition: transform 0.2s ease-in-out;"
                                      title="Add {{ $emoji['name'] }} emoji">
                                    {{ $emoji['unicode'] }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-10 offset-sm-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-reply me-2"></i> Reply
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-messages.layout>
