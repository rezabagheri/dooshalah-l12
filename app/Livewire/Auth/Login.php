<?php

namespace App\Livewire\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Login extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;
    public string $bodyClass = 'login-page bg-body-secondary';
    public bool $showPassword = false; // متغیر برای نمایش رمز

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    public function login(): void
    {
        $this->validate();

        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            \Log::info('Login failed', ['email' => $this->email]);
            throw ValidationException::withMessages(['email' => __('auth.failed')]);
        }

        \Log::info('Login successful', ['email' => $this->email]);
        Session::regenerate();
        $this->redirect('/dashboard');
    }

    public function togglePassword(): void
    {
        $this->showPassword = !$this->showPassword;
    }

    public function render()
    {
        return view('auth.login')
            ->layout('components.layouts.auth', ['bodyClass' => $this->bodyClass]);
    }
}
