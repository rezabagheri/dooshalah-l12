<div>
    <h2>Chat with {{ \App\Models\User::find($receiverId)->display_name }}</h2>

    <div>
        <button wire:click="loadMessages">Refresh Messages</button>
    </div>

    <div wire:poll.15s="checkForNewMessages" class="messages" style="height: 300px; overflow-y: scroll; border: 1px solid #ccc; padding: 10px;">
        @foreach ($messages as $message)
            <div style="margin-bottom: 10px;">
                <strong>{{ $message['sender_display_name'] }}:</strong>
                <span>{{ $message['content'] }}</span>
                <small>{{ $message['created_at'] }}</small>
            </div>
        @endforeach
    </div>

    <div class="send-message" style="margin-top: 20px;">
        <form wire:submit.prevent="sendMessage">
            <textarea wire:model="messageContent" placeholder="Type your message..." style="width: 100%; height: 60px;"></textarea>
            @error('messageContent') <span class="error">{{ $message }}</span> @enderror
            <button type="submit">Send</button>
        </form>
    </div>
</div>
