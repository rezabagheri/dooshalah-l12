<div class="row">
    <!-- Sidebar Navigation -->
    <div class="col-md-3">
        <div class="card card-primary card-outline">
            <div class="card-header text-center">
                <h5>Messages</h5>
            </div>
            <div class="card-body p-0">
                <ul class="nav nav-pills flex-column">
                    <li class="nav-item">
                        <a href="{{ route('messages.compose') }}"
                           class="nav-link {{ request()->routeIs('messages.compose') ? 'active' : '' }}"
                           wire:navigate>
                            <i class="nav-icon bi bi-plus-square"></i> Compose
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('messages.inbox') }}"
                           class="nav-link {{ request()->routeIs('messages.inbox') ? 'active' : '' }}"
                           wire:navigate>
                            <i class="nav-icon bi bi-inbox"></i> Inbox
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('messages.sent') }}"
                           class="nav-link {{ request()->routeIs('messages.sent') ? 'active' : '' }}"
                           wire:navigate>
                            <i class="nav-icon bi bi-send"></i> Sent
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('messages.drafts') }}"
                           class="nav-link {{ request()->routeIs('messages.drafts') ? 'active' : '' }}"
                           wire:navigate>
                            <i class="nav-icon bi bi-pencil"></i> Drafts
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="col-md-9">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ $heading ?? '' }}</h3>
                <div class="card-tools">
                    <small class="text-muted">{{ $subheading ?? '' }}</small>
                </div>
            </div>
            <div class="card-body">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
