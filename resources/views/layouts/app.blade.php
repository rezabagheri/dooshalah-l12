@extends('layouts.clean')

@section('body-class', 'hold-transition sidebar-mini layout-fixed')

@section('content')
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-lte-toggle="sidebar-full" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="nav-link btn btn-link">Logout</button>
                    </form>
                </li>
            </ul>
        </nav>

        <!-- Sidebar -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <a href="{{ route('admin.dashboard') }}" class="brand-link">
                <span class="brand-text font-weight-light">My App</span>
            </a>
            <div class="sidebar">
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-lte-toggle="treeview" role="menu">
                        <li class="nav-item">
                            <a href="{{ route('admin.dashboard') }}" class="nav-link @if(Route::is('admin.dashboard')) active @endif">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                        @can('manage', \App\Models\Plan::class)
                            <li class="nav-item">
                                <a href="{{ route('admin.plans') }}" class="nav-link @if(Route::is('admin.plans')) active @endif">
                                    <i class="nav-icon fas fa-list"></i>
                                    <p>Plans</p>
                                </a>
                            </li>
                        @endcan
                        @can('manage', \App\Models\User::class)
                            <li class="nav-item">
                                <a href="{{ route('admin.users') }}" class="nav-link @if(Route::is('admin.users')) active @endif">
                                    <i class="nav-icon fas fa-users"></i>
                                    <p>Users</p>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <h1 class="m-0">{{ $page_title ?? '' }}</h1>
                </div>
            </div>
            <div class="content">
                <div class="container-fluid">
                    @yield('app-content')
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="main-footer">
            <strong>My App © {{ date('Y') }}</strong>
        </footer>
    </div>
@endsection
