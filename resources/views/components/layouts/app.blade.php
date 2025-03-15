<?php
$bodyClass = 'layout-fixed sidebar-expand-lg sidebar-mini bg-body-tertiary';
?>

<x-layouts.clean :body-class="$bodyClass">
    <div class="app-wrapper">
        @include('partials.navbar')
        <livewire:layouts.sidebar />
        @include('partials.main-content')
        @include('partials.toast')
        @include('partials.footer')
    </div>
</x-layouts.clean>

{{-- <x-layouts.clean :body-class="$bodyClass">
    <div class="app-wrapper">
        @include('partials.navbar')
        <livewire:layouts.sidebar />
        <main class="content-wrapper">
            {{ $slot }}
        </main>
        @include('partials.toast')
        @include('partials.footer')
    </div>
</x-layouts.clean> --}}
