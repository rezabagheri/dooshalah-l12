<!-- resources/views/components/settings/layout.blade.php -->
<div class="row">
    <!-- Sidebar Navigation -->
    <div class="col-md-3">
        <div class="card card-primary card-outline">
            <div class="card-header text-center">
                <div class="text-center">
                    <img src="{{ Auth::user()->profilePicture()?->media->path ? asset('storage/' . Auth::user()->profilePicture()->media->path) : '/dist/assets/img/user2-160x160.jpg' }}"
                         class="profile-user-img img-fluid img-circle rounded-circle shadow mb-2 d-block mx-auto"
                         alt="User Image" style="width: 100px; height: 100px;">
                </div>
                <h5>{{ Auth::user()->display_name ?? 'User' }}</h5>
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
                        <a href="{{ route('settings.security') }}"
                           class="nav-link {{ request()->routeIs('settings.security') ? 'active' : '' }}"
                           wire:navigate>
                            <i class="nav-icon bi bi-shield-lock"></i> Security
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('settings.photos') }}"
                           class="nav-link {{ request()->routeIs('settings.photos') ? 'active' : '' }}"
                           wire:navigate>
                            <i class="nav-icon bi bi-camera"></i> Photos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('settings.appearance') }}"
                           class="nav-link {{ request()->routeIs('settings.appearance') ? 'active' : '' }}"
                           wire:navigate>
                            <i class="nav-icon bi bi-palette"></i> Appearance
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('settings.interests') }}"
                           class="nav-link {{ request()->routeIs('settings.interests') ? 'active' : '' }}"
                           wire:navigate>
                            <i class="nav-icon bi bi-heart"></i> Interests
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
