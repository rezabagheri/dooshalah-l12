<div>
    <p class="login-box-msg">{{ __('Forgot Your Password?') }}</p>

    <!-- Session Status -->
    @if (session('status'))
        <div class="alert alert-success text-center" role="alert">
            {{ session('status') }}
        </div>
    @endif

    <form wire:submit.prevent="sendPasswordResetLink">
        <div class="input-group mb-3">
            <input wire:model="email" type="email" class="form-control @error('email') is-invalid @enderror"
                placeholder="{{ __('Email Address') }}" required autofocus>
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-envelope"></span>
                </div>
            </div>
            @error('email')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div class="row">
            <div class="col-12">
                <button type="submit" class="btn btn-primary btn-block">
                    {{ __('Email Password Reset Link') }}
                </button>
            </div>
        </div>
    </form>

    <p class="mb-0 mt-3 text-center">
        <a href="{{ route('login') }}" class="text-center">
            {{ __('Return to Login') }}
        </a>
    </p>

</div>

@push('scripts')
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('updateBodyClass', (bodyClass) => {
                document.body.className = bodyClass;
            });
        });
    </script>
@endpush
