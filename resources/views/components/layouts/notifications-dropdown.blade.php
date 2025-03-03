<li class="nav-item dropdown">
    <a class="nav-link" data-bs-toggle="dropdown" href="#">
        <i class="bi bi-bell-fill"></i>
        @if ($notificationCount > 0)
            <span class="navbar-badge badge text-bg-warning">{{ $notificationCount }}</span>
        @endif
    </a>
    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
        <span class="dropdown-item dropdown-header">{{ $notificationCount }} Unread Notifications</span>
        @if (empty($notifications))
            <div class="dropdown-item text-center text-muted">No unread notifications</div>
        @else
            @foreach ($notifications as $index => $notification)
                <div class="dropdown-divider"></div>
                <a href="{{ $notification['action_url'] ?? '#' }}" class="dropdown-item notification-item">
                    <i class="{{ $notification['icon'] }} me-2"></i> {{ $notification['text'] }}
                    <span class="float-end text-secondary fs-7">{{ $notification['time'] }}</span>
                </a>
            @endforeach
        @endif
        <div class="dropdown-divider"></div>
        <a href="{{ route('notifications') }}" class="dropdown-item dropdown-footer">See All Notifications</a>
    </div>

    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('notification-updated', () => {
                // آپدیت UI اگه نیاز باشه
                console.log('Notifications updated');
            });
        });
    </script>
</li>
