<?php
$bodyClass = 'layout-fixed sidebar-expand-lg sidebar-mini bg-body-tertiary';
?>

<x-layouts.clean :body-class="$bodyClass">
    <div class="app-wrapper">
        <!-- Navbar -->
        @include('partials.navbar')
        <!-- Sidebar -->
        <livewire:layouts.sidebar />

        <!-- Main Content -->
        <main class="app-main">
            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-0">{{ $page_title ?? 'Dashboard' }}</h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Sidebar Mini</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="app-content">
                <div class="container-fluid">
                    {{ $slot }}
                </div>
            </div>
        </main>

        <!-- Footer -->
        @include('partials.footer')
    </div>
</x-layouts.clean>
