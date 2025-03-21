<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class ConfirmPassword extends Component
{
    public string $password = '';
    public bool $showPassword = false; // برای نمایش رمز

    public function rules(): array
    {
        return [
            'password' => ['required', 'string'],
        ];
    }

    public function confirmPassword(): void
    {
        $this->validate();

        if (! Auth::guard('web')->validate([
            'email' => Auth::user()->email,
            'password' => $this->password,
        ])) {
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        session(['auth.password_confirmed_at' => time()]);

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }

    public function togglePassword(): void
    {
        $this->showPassword = !$this->showPassword;
    }

    public function render()
    {
        return view('auth.confirm-password')
            ->layout('components.layouts.auth');
    }
}
