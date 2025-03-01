<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Volt\Component;

new class extends Component {
    public string $current_password = '';
    public string $new_password = '';
    public string $new_password_confirmation = '';

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
            $this->dispatch('password-updated');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->validator->errors()->all();
            foreach ($errors as $error) {
                $this->dispatch('error', $error);
            }
            return;
        }
    }
}; ?>

<x-settings.layout heading="Security" subheading="Manage your account security settings">
    <div class="card card-primary card-outline card-hover-effect">
        <div class="card-header">
            <h5 class="card-title">{{ __('Update Password') }}</h5>
        </div>
        <div class="card-body">
            <form wire:submit="updatePassword" class="form-horizontal">
                <div class="row mb-3">
                    <label for="current_password" class="col-sm-3 col-form-label">{{ __('Current Password') }}</label>
                    <div class="col-sm-9">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input wire:model="current_password" type="password" class="form-control" id="current_password" required>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="new_password" class="col-sm-3 col-form-label">{{ __('New Password') }}</label>
                    <div class="col-sm-9">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                            <input wire:model="new_password" type="password" class="form-control" id="new_password" required>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="new_password_confirmation" class="col-sm-3 col-form-label">{{ __('Confirm Password') }}</label>
                    <div class="col-sm-9">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                            <input wire:model="new_password_confirmation" type="password" class="form-control" id="new_password_confirmation" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-9 offset-sm-3">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i> {{ __('Save') }}
                            </button>
                            <button type="button" class="btn btn-secondary" wire:click="$refresh">
                                <i class="bi bi-arrow-repeat me-1"></i> {{ __('Reset') }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-settings.layout>
