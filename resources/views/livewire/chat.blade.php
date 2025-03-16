<div class="chat-container" wire:poll.5000ms="loadMessages">
    <!-- لیست کاربران -->
    <div class="users-list">
        <div class="search-box">
            <input type="text" wire:model.debounce.500ms="searchQuery" placeholder="Search friends...">
        </div>
        @foreach ($users as $user)
            <div wire:click="selectUser({{ $user['id'] }})" class="{{ $selectedUserId == $user['id'] ? 'selected' : '' }}">
                {{ $user['display_name'] }} {{ $user['is_online'] ? '(Online)' : '' }}
            </div>
        @endforeach
    </div>

    <!-- بخش چت -->
    @if ($selectedUserId)
        <div class="chat-box">
            <div>
                @if ($isTyping)
                    <div class="typing-indicator">Typing...</div>
                @endif
            </div>

            <div class="messages">
                @foreach ($messages as $message)
                    <div class="{{ $message['sender_id'] == Auth::id() ? 'sent' : 'received' }}">
                        {{ $message['content'] }}
                        <span class="status">
                            @if ($message['status'] == 'sent') ✓ @endif
                            @if ($message['status'] == 'delivered') ✓✓ @endif
                            @if ($message['status'] == 'read') ✓✓ (read) @endif
                        </span>
                    </div>
                @endforeach
            </div>

            <div class="message-input">
                <input type="text" wire:model="message" wire:keydown="typing" placeholder="Type a message...">
                <button wire:click="sendMessage">Send</button>
            </div>
        </div>
    @endif
</div>
