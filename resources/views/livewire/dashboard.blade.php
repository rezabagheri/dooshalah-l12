<div class="card">
    <div class="card-header">Dashboard</div>
    <div class="card-body">
        Welcome to the Dashboard, {{ auth()->user()->display_name ?? 'User' }}!

        @if (auth()->check())
            <button onclick="requestNotificationPermission()">Enable Notifications</button>
        @else
            <p>Please log in to enable notifications.</p>
        @endif
    </div>
</div>
