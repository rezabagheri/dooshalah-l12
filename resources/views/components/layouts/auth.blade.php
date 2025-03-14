<x-layouts.clean :body-class="$bodyClass">
    <div class="login-box">
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <a href="{{ route('login') }}" class="h1"><b>{{ config('app.name') }}</b></a>
            </div>
            <div class="card-body">
                {{ $slot }}
            </div>
        </div>
    </div>
</x-layouts.clean>
