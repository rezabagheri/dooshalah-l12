<div class="chat-container" wire:poll.5000ms="loadMessages">
    <div class="users-list">
        <h3>Users</h3>
        <ul>
            @foreach ($users as $user)
                <li wire:click="selectUser({{ $user['id'] }})" class="{{ $selectedUserId == $user['id'] ? 'active' : '' }}">
                    {{ $user['display_name'] }}
                    <span class="status {{ $user['is_online'] ? 'online' : 'offline' }}"></span>
                </li>
            @endforeach
        </ul>
    </div>

    <div class="chat-box">
        @if ($selectedUserId)
            <div class="messages">
                @foreach ($messages as $message)
                    <div class="message {{ $message['sender_id'] == auth()->id() ? 'sent' : 'received' }}">
                        <p>{{ $message['content'] }}</p>
                        <span class="time">{{ \Carbon\Carbon::parse($message['created_at'])->format('H:i') }}</span>
                        @if ($message['sender_id'] == auth()->id())
                            <span class="status">
                                @if ($message['status'] == 'sent')
                                    <span class="tick single grey">✓</span>
                                @elseif ($message['status'] == 'delivered')
                                    <span class="tick double grey">✓✓</span>
                                @elseif ($message['status'] == 'read')
                                    <span class="tick double green">✓✓</span>
                                @endif
                            </span>
                        @endif
                    </div>
                @endforeach
            </div>

            @if ($isTyping)
                <div class="typing-indicator">Typing...</div>
            @endif

            <div class="message-input">
                <input type="text" wire:model.live="message" wire:keydown="typing" placeholder="Type a message..." />
                <button wire:click="sendMessage">Send</button>
            </div>
        @else
            <p>Select a user to start chatting.</p>
        @endif
    </div>
</div>

<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('userTyping', (event) => {
            // Typing event handled in component
        });
        Livewire.on('resetTyping', () => {
            // Reset handled in component
        });
    });
</script>
