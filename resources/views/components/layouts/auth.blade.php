<x-layouts.clean :body-class="$bodyClass ?? 'login-page bg-body-secondary'">
    <div class="{{ $boxClass ?? 'login-box' }}">
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <a href="{{ route('login') }}">
                    <img src="{{ asset('dist/assets/img/logo.svg') }}" alt="{{ config('app.name') }}" class="img-fluid" style="max-height: 50px;">
                </a>
            </div>
            <div class="card-body">
                {{ $slot }}
            </div>
        </div>
    </div>
</x-layouts.clean>
