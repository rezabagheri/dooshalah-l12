<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="light">
    <!--begin::Sidebar Brand-->
    <div class="sidebar-brand">
        <a class="brand-link" href="{{ url('/') }}">
            <img src="{{ asset('dist/assets/img/logo.svg') }}" alt="{{ config('app.name') }}" class="brand-image opacity-75 shadow">
        </a>
    </div>
    <!--end::Sidebar Brand-->

    <!--begin::Sidebar Wrapper-->
    <div class="sidebar-wrapper" data-overlayscrollbars="host">
        <div class="os-size-observer">
            <div class="os-size-observer-listener"></div>
        </div>
        <nav class="mt-2">
            <!--begin::Sidebar Menu-->
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">
                @foreach ($menuItems as $item)
                    @php
                        // چک کردن اینکه آیا یکی از زیرمنوها فعال است یا نه
                        $hasActiveChild = $item->children->contains(fn ($child) => Route::currentRouteName() === $child->route);
                    @endphp
                    <li class="nav-item {{ $hasActiveChild ? 'menu-open' : '' }}">
                        <a href="{{ $item->route === '#' ? '#' : route($item->route) }}"
                           class="nav-link {{ Route::currentRouteName() === $item->route ? 'active' : '' }}">
                            <i class="nav-icon {{ $item->icon }}"></i>
                            <p>
                                {{ $item->label }}
                                @if ($item->children->isNotEmpty())
                                    <i class="nav-arrow bi bi-chevron-right"></i>
                                @endif
                            </p>
                        </a>
                        @if ($item->children->isNotEmpty())
                            <ul class="nav nav-treeview">
                                @foreach ($item->children as $child)
                                    <li class="nav-item">
                                        <a href="{{ $child->route === '#' ? '#' : route($child->route) }}"
                                           class="nav-link {{ Route::currentRouteName() === $child->route ? 'active' : '' }}">
                                            <i class="nav-icon {{ $child->icon }}"></i>
                                            <p>{{ $child->label }}</p>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                    @if ($item->has_divider)
                        <li class="nav-header">MAIN MENU</li>
                    @endif
                @endforeach
            </ul>
            <!--end::Sidebar Menu-->
        </nav>
    </div>
    <!--end::Sidebar Wrapper-->
</aside>
