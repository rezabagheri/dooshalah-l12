<?php

use App\Enums\MessageStatus;
use App\Models\Message;
use Livewire\Volt\Component;

new class extends Component {
    public $subject = '';
    public $message = '';
    public $receiverId;

    public function sendMessage(): void
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

        $this->reset(['subject', 'message', 'receiverId']);
        session()->flash('success', 'Message sent successfully!');
    }
}; ?>

<x-messages.layout heading="Compose New Message" subheading="Send a new message to someone">
    <div>
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <form wire:submit="sendMessage" class="form-horizontal">
            <div class="row mb-3">
                <label for="receiverId" class="col-sm-3 col-form-label">To</label>
                <div class="col-sm-9">
                    <select class="form-control" id="receiverId" wire:model="receiverId" required>
                        <option value="">Select a recipient</option>
                        @foreach (\App\Models\User::where('id', '!=', auth()->id())->get() as $user)
                            <option value="{{ $user->id }}">{{ $user->display_name }}</option>
                        @endforeach
                    </select>
                    @error('receiverId') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="row mb-3">
                <label for="subject" class="col-sm-3 col-form-label">Subject</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" id="subject" wire:model="subject" required>
                    @error('subject') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="row mb-3">
                <label for="message" class="col-sm-3 col-form-label">Message</label>
                <div class="col-sm-9">
                    <textarea class="form-control" id="message" rows="6" wire:model="message" required></textarea>
                    @error('message') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="row">
                <div class="col-sm-9 offset-sm-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send me-2"></i> Send Message
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-messages.layout>
