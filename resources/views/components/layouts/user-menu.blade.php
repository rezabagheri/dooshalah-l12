<li class="nav-item dropdown user-menu">
    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
        <img src="{{ $profilePicture }}" class="user-image rounded-circle shadow" alt="User Image">
        <span class="d-none d-md-inline">{{ $displayName }}</span>
    </a>
    <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
        <li class="user-header text-bg-primary">
            <img src="{{ $profilePicture }}" class="rounded-circle shadow" alt="User Image">
            <p>
                {{ $displayName }} - Web Developer
                <small>Member since {{ $joinDate }}</small>
            </p>
        </li>
        <li class="user-body">
            <div class="row">
                <div class="col-4 text-center"><a href="#">Followers</a></div>
                <div class="col-4 text-center"><a href="#">Sales</a></div>
                <div class="col-4 text-center"><a href="#">Friends</a></div>
            </div>
        </li>
        <li class="user-footer">
            <a href="{{ route('settings.profile') }}" class="btn btn-default btn-flat">Profile</a>
            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-default btn-flat float-end">Logout</button>
            </form>
        </li>
    </ul>
</li>
