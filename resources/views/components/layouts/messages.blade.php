<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <!-- Topbar و Sidebar اصلی از app.blade.php -->
        @include('layouts.partials.navbar')
        @include('layouts.partials.sidebar')

        <!-- محتوای اصلی با سایدبار دوم -->
        <div class="content-wrapper">
            <div class="container-fluid mt-4">
                <div class="row">
                    <!-- سایدبار دوم -->
                    <div class="col-md-2">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Mailbox</h3>
                            </div>
                            <div class="card-body p-0">
                                <ul class="nav nav-pills flex-column">
                                    <li class="nav-item">
                                        <a href="{{ route('messages.compose') }}" class="nav-link {{ request()->routeIs('messages.compose') ? 'active' : '' }}">
                                            <i class="bi bi-plus-square me-2"></i> Compose
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('messages.inbox') }}" class="nav-link {{ request()->routeIs('messages.inbox') && request()->query('mode', 'inbox') == 'inbox' ? 'active' : '' }}" wire:click="$set('viewMode', 'inbox')">
                                            <i class="bi bi-inbox me-2"></i> Inbox
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('messages.inbox', ['mode' => 'sent']) }}" class="nav-link {{ request()->query('mode') == 'sent' ? 'active' : '' }}" wire:click="$set('viewMode', 'sent')">
                                            <i class="bi bi-send me-2"></i> Sent
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('messages.inbox', ['mode' => 'draft']) }}" class="nav-link {{ request()->query('mode') == 'draft' ? 'active' : '' }}" wire:click="$set('viewMode', 'draft')">
                                            <i class="bi bi-pencil me-2"></i> Draft
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- محتوای اصلی -->
                    <div class="col-md-10">
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>

        @include('layouts.partials.footer')
    </div>

    @livewireScripts
</body>
</html>
