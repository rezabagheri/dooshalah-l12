<?php
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth as AuthFacade;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;
use Illuminate\Contracts\View\View;

new #[Layout('components.layouts.auth')] class extends Component {
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    public string $bodyClass = 'login-page bg-body-secondary';

    public function login(): void
    {
        $this->validate();

        if (! AuthFacade::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            \Log::info('Login failed', ['email' => $this->email]);
            throw ValidationException::withMessages(['email' => __('auth.failed')]);
        }

        \Log::info('Login successful', ['email' => $this->email]);
        Session::regenerate();
        $this->redirect('/dashboard', navigate: true);
    }

    public function toggleDarkMode(): void
    {
        $this->bodyClass = $this->bodyClass === 'login-page bg-body-secondary' ? 'login-page dark-mode' : 'login-page bg-body-secondary';
        $this->dispatch('updateBodyClass', $this->bodyClass);
    }

    public function render(): View
    {
        return view('auth.login')->layout('components.layouts.auth', [
            'bodyClass' => $this->bodyClass,
        ]);
    }
}; ?>
