<div>
    <p class="login-box-msg fw-bold">{{ __('Reset Your Password') }}</p>

    <!-- Session Status -->
    @if (session('status'))
        <div class="alert alert-success text-center mb-3" role="alert">
            {{ session('status') }}
        </div>
    @endif

    <form wire:submit.prevent="resetPassword">
        <!-- Email Address -->
        <div class="input-group mb-3">
            <input wire:model="email" type="email" class="form-control @error('email') is-invalid @enderror"
                id="email" placeholder="{{ __('Email Address') }}" required autofocus>
            <div class="input-group-text">
                <i class="bi bi-envelope"></i>
            </div>
            @error('email')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <!-- Password -->
        <div class="input-group mb-3">
            <input wire:model="password" type="password" class="form-control @error('password') is-invalid @enderror"
                id="password" placeholder="{{ __('Password') }}" required autocomplete="new-password">
            <div class="input-group-text">
                <i class="bi bi-lock"></i>
            </div>
            @error('password')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="input-group mb-3">
            <input wire:model="password_confirmation" type="password" class="form-control @error('password_confirmation') is-invalid @enderror"
                id="password_confirmation" placeholder="{{ __('Confirm Password') }}" required autocomplete="new-password">
            <div class="input-group-text">
                <i class="bi bi-lock"></i>
            </div>
            @error('password_confirmation')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <!-- Submit Button -->
        <div class="row">
            <div class="col-12">
                <button type="submit" class="btn btn-primary w-100 mb-3">
                    {{ __('Reset Password') }}
                </button>
            </div>
        </div>
    </form>

    <p class="mb-0 mt-3 text-center">
        <a href="{{ route('login') }}">{{ __('Return to Login') }}</a>
    </p>
</div>
