<div class="card">
    <div class="card-header">Dashboard</div>
    <div class="card-body">
        Welcome to the Dashboard, {{ auth()->user()->display_name ?? 'User' }}!
    </div>
</div>
