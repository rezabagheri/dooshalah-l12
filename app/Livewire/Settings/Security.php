<?php

namespace App\Livewire\Settings;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Security extends Component
{
    public string $current_password = '';
    public string $new_password = '';
    public string $new_password_confirmation = '';

    public bool $showCurrentPassword = false;
    public bool $showNewPassword = false;
    public bool $showNewPasswordConfirmation = false;

    public function updatePassword(): void
    {
        $user = Auth::user();

        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', function ($attribute, $value, $fail) use ($user) {
                    if (!Hash::check($value, $user->password)) {
                        $fail('The current password is incorrect.');
                    }
                }],
                'new_password' => ['required', 'string', 'min:8', 'confirmed'],
                'new_password_confirmation' => ['required', 'string'],
            ]);

            $user->password = Hash::make($validated['new_password']);
            $user->save();

            $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
            $this->reset(['showCurrentPassword', 'showNewPassword', 'showNewPasswordConfirmation']);
            $this->dispatch('password-updated');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->validator->errors()->all();
            foreach ($errors as $error) {
                $this->dispatch('error', $error);
            }
            return;
        }
    }

    public function togglePassword($field): void
    {
        if ($field === 'current') {
            $this->showCurrentPassword = !$this->showCurrentPassword;
        } elseif ($field === 'new') {
            $this->showNewPassword = !$this->showNewPassword;
        } elseif ($field === 'confirmation') {
            $this->showNewPasswordConfirmation = !$this->showNewPasswordConfirmation;
        }
    }

    public function render()
    {
        return view('livewire.settings.security');
    }
}
