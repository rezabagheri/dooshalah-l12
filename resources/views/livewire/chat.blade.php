<div class="chat-container" wire:poll.5000ms="loadMessages">
    <!-- لیست کاربران -->
    <div class="users-list">
        <div class="search-box">
            <input type="text" wire:model.debounce.500ms="searchQuery" placeholder="Search friends...">
        </div>
        @foreach ($users as $user)
            <div wire:click="selectUser({{ $user['id'] }})" class="{{ $selectedUserId == $user['id'] ? 'selected' : '' }}">
                @if ($user['profile_photo_path'])
                    <img src="{{ $user['profile_photo_path'] }}" alt="{{ $user['display_name'] }}" class="user-avatar">
                @else
                    <img src="{{ asset('images/default-avatar.png') }}" alt="{{ $user['display_name'] }}" class="user-avatar">
                @endif
                <div class="user-info">
                    <span class="user-name">{{ $user['display_name'] }}</span>
                    <div class="user-status">
                        <span class="status-indicator {{ $user['is_online'] ? 'online' : 'offline' }}"></span>
                        <span class="last-seen">{{ $user['last_seen_text'] }}</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- بخش چت -->
    @if ($selectedUserId && $selectedUser)
        <div class="chat-box">
            <div class="chat-header">
                @if ($selectedUser['profile_photo_path'])
                    <img src="{{ $selectedUser['profile_photo_path'] }}" alt="{{ $selectedUser['display_name'] }}" class="chat-avatar">
                @else
                    <img src="{{ asset('images/default-avatar.png') }}" alt="{{ $selectedUser['display_name'] }}" class="chat-avatar">
                @endif
                <div class="chat-user-info">
                    <span class="chat-user-name">{{ $selectedUser['display_name'] }}</span>
                    <div class="chat-user-status">
                        <span class="status-indicator {{ $selectedUser['is_online'] ? 'online' : 'offline' }}"></span>
                        <span class="chat-last-seen">{{ $selectedUser['last_seen_text'] }}</span>
                    </div>
                </div>
            </div>

            <div>
                @if ($isTyping)
                    <div class="typing-indicator">Typing...</div>
                @endif
            </div>

            <div class="messages" id="chat-messages">
                @foreach ($messages as $message)
                    <div class="message {{ $message['sender_id'] == Auth::id() ? 'sent' : 'received' }}">
                        @if ($message['profile_photo_path'])
                            <img src="{{ $message['profile_photo_path'] }}" alt="Avatar" class="message-avatar">
                        @else
                            <img src="{{ asset('images/default-avatar.png') }}" alt="Avatar" class="message-avatar">
                        @endif
                        <div class="message-content">
                            <div class="message-text">{!! nl2br(e($message['content'])) !!}</div>
                            <div class="message-meta">
                                <span class="time">{{ $message['created_at'] }}</span>
                                @if ($message['sender_id'] == Auth::id())
                                    <span class="status">
                                        @if ($message['status'] == 'sent') ✓ @endif
                                        @if ($message['status'] == 'delivered') ✓✓ @endif
                                        @if ($message['status'] == 'read') ✓✓ (read) @endif
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="message-input">
                <button wire:click="toggleStickerPopup" class="sticker-button">😊</button>
                <textarea
                    wire:model.debounce.500ms="message"
                    wire:keydown.debounce.500ms="typing"
                    wire:keydown.enter="sendMessage"
                    placeholder="Type a message..."
                    id="message-input"
                    rows="1"
                    class="message-textarea"
                ></textarea>
                <button wire:click="sendMessage">Send</button>

                @if ($showStickerPopup)
                    <div class="sticker-popup">
                        @foreach (get_emojis() as $emoji)
                            <span wire:click="sendSticker('{{ $emoji['unicode'] }}')" class="sticker">{{ $emoji['unicode'] }}</span>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>

@script
<script>
    document.addEventListener('livewire:init', () => {
        console.log('Livewire initialized');
    });

    $wire.on('scrollToBottom', () => {
        console.log('Scroll to bottom triggered');
        setTimeout(() => {
            const messages = document.getElementById('chat-messages');
            if (messages) {
                messages.scrollTop = messages.scrollHeight;
                console.log('Scrolled to bottom');
            } else {
                console.error('Chat messages element not found');
            }
        }, 100);
    });

    $wire.on('messageSent', () => {
        console.log('Message sent event received');
        const input = document.getElementById('message-input');
        if (input) {
            input.value = '';
            console.log('Input cleared');
        }
    });
</script>
@endscript
