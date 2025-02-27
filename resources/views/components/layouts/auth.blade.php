<?php
$bodyClass = 'login-page bg-body-secondary';
?>

<x-layouts.clean body-class="{{ $bodyClass }}">
    <div class="login-box">
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <a href="{{ url('/') }}" class="h1"><b>My</b>App</a>
            </div>
            <div class="card-body">
                {{ $slot }}
            </div>
        </div>
    </div>
</x-layouts.clean>
