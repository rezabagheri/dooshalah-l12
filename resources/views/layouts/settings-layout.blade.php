<div class="container-fluid">
    <div class="row">
        <!-- Sidebar Navigation -->
        <div class="col-md-3">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Settings</h3>
                </div>
                <div class="card-body p-0">
                    <ul class="nav nav-pills flex-column">
                        <li class="nav-item">
                            <a href="{{ route('settings.profile') }}"
                               class="nav-link {{ request()->routeIs('settings.profile') ? 'active' : '' }}"
                               wire:navigate>
                                <i class="nav-icon bi bi-person"></i> Profile
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('settings.password') }}"
                               class="nav-link {{ request()->routeIs('settings.password') ? 'active' : '' }}"
                               wire:navigate>
                                <i class="nav-icon bi bi-lock"></i> Password
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('settings.appearance') }}"
                               class="nav-link {{ request()->routeIs('settings.appearance') ? 'active' : '' }}"
                               wire:navigate>
                                <i class="nav-icon bi bi-palette"></i> Appearance
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
</div>
