<?php
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    public function login(): void
    {
        $this->validate();

        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            \Log::info('Login failed', ['email' => $this->email]);
            throw ValidationException::withMessages(['email' => __('auth.failed')]);
        }

        \Log::info('Login successful', ['email' => $this->email]);
        Session::regenerate();
        $this->redirect('/dashboard', navigate: true);
    }
}; ?>

<div>
    <p class="login-box-msg">Sign in to start your session</p>

    @if (session('status'))
        <div class="alert alert-success text-center mb-3">
            {{ session('status') }}
        </div>
    @endif

    <form wire:submit="login">
        <div class="input-group mb-3">
            <input type="email" name="email" wire:model="email" class="form-control @error('email') is-invalid @enderror" placeholder="Email" required autofocus>
            <div class="input-group-text">
                <span class="fas fa-envelope"></span>
            </div>
            @error('email')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
        <div class="input-group mb-3">
            <input type="password" name="password" wire:model="password" class="form-control @error('password') is-invalid @enderror" placeholder="Password" required>
            <div class="input-group-text">
                <span class="fas fa-lock"></span>
            </div>
            @error('password')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
        <div class="row mb-3">
            <div class="col-8">
                <div class="icheck-primary">
                    <input type="checkbox" name="remember" wire:model="remember" id="remember">
                    <label for="remember">{{ __('Remember me') }}</label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <button type="submit" class="btn btn-primary btn-block">{{ __('Log in') }}</button>
            </div>
        </div>
    </form>

    <p class="mb-1">
        @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}">{{ __('Forgot your password?') }}</a>
        @endif
    </p>
    <p class="mb-0">
        @if (Route::has('register'))
            <a href="{{ route('register') }}" class="text-center">{{ __('Register a new membership') }}</a>
        @endif
    </p>
</div>
