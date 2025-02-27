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
                    <li class="nav-item {{ $item->children->isNotEmpty() ? 'menu-open' : '' }}">
                        <a href="{{ $item->route === '#' ? '#' : route($item->route) }}"
                           class="nav-link {{ request()->routeIs($item->route) ? 'active' : '' }}">
                            <i class="nav-icon {{ $item->icon }}"></i>
                            <p>
                                {{ $item->label }}
                                @if ($item->children->isNotEmpty())
                                    <i class="bi bi-chevron-down right"></i>
                                @endif
                            </p>
                        </a>
                        @if ($item->children->isNotEmpty())
                            <ul class="nav nav-treeview">
                                @foreach ($item->children as $child)
                                    <li class="nav-item">
                                        <a href="{{ $child->route === '#' ? '#' : route($child->route) }}"
                                           class="nav-link {{ request()->routeIs($child->route) ? 'active' : '' }}">
                                            <i class="nav-icon {{ $child->icon }}"></i>
                                            <p>{{ $child->label }}</p>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                    @if ($item->has_divider)
                        <li class="nav-item"><div class="dropdown-divider"></div></li>
                    @endif
                @endforeach
            </ul>
        </nav>
    </div>
</aside>
