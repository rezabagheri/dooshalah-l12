<li class="nav-item dropdown">
    <a class="nav-link" data-bs-toggle="dropdown" href="#">
        <i class="bi bi-bell-fill"></i>
        <span class="navbar-badge badge text-bg-warning">{{ $notificationCount }}</span>
    </a>
    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
        <span class="dropdown-item dropdown-header">{{ $notificationCount }} Notifications</span>
        @foreach ($notifications as $notification)
            <div class="dropdown-divider"></div>
            <a href="#" class="dropdown-item">
                <i class="{{ $notification['icon'] }} me-2"></i> {{ $notification['text'] }}
                <span class="float-end text-secondary fs-7">{{ $notification['time'] }}</span>
            </a>
        @endforeach
        <div class="dropdown-divider"></div>
        <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
    </div>
</li>
