<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <div class="sidebar-brand">
        <img src="/dist/assets/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image opacity-75 shadow" />
        <a href="{{ url('/') }}" class="brand-link">
            <span class="brand-text font-weight-light">My App</span>
        </a>
    </div>

    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">
                @foreach ($menuItems as $item)
                    <li class="nav-item">
                        <a href="{{ $item['route'] === '#' ? '#' : route($item['route']) }}"
                           class="nav-link {{ request()->routeIs($item['route']) ? 'active' : '' }}">
                            <i class="nav-icon {{ $item['icon'] }}"></i>
                            <p>{{ $item['label'] }}</p>
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>
    </div>
</aside>
