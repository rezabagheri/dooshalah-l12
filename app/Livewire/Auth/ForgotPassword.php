<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Password;
use Livewire\Component;

class ForgotPassword extends Component
{
    public string $email = '';
    public string $bodyClass = 'login-page bg-body-secondary';

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
        ];
    }

    public function sendPasswordResetLink(): void
    {
        $this->validate();

        $status = Password::sendResetLink($this->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            session()->flash('status', __('A reset link will be sent if the account exists.'));
        } else {
            $this->addError('email', __($status));
        }
    }

    public function toggleDarkMode(): void
    {
        $this->bodyClass = $this->bodyClass === 'login-page bg-body-secondary' ? 'login-page dark-mode' : 'login-page bg-body-secondary';
        $this->dispatch('updateBodyClass', $this->bodyClass);
    }

    public function render()
    {
        return view('auth.forgot-password')
            ->layout('components.layouts.auth', [
                'bodyClass' => $this->bodyClass,
            ]);
    }
}
