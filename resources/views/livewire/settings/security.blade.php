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
    }
}; ?>

<x-settings.layout heading="Security" subheading="Manage your account security settings">
    <form wire:submit="updatePassword" class="form-horizontal">
        <div class="row mb-3">
            <label for="current_password" class="col-sm-3 col-form-label">{{ __('Current Password') }}</label>
            <div class="col-sm-9">
                <input wire:model="current_password" type="password" class="form-control" id="current_password" required>
                @error('current_password') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="row mb-3">
            <label for="new_password" class="col-sm-3 col-form-label">{{ __('New Password') }}</label>
            <div class="col-sm-9">
                <input wire:model="new_password" type="password" class="form-control" id="new_password" required>
                @error('new_password') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="row mb-3">
            <label for="new_password_confirmation" class="col-sm-3 col-form-label">{{ __('Confirm Password') }}</label>
            <div class="col-sm-9">
                <input wire:model="new_password_confirmation" type="password" class="form-control" id="new_password_confirmation" required>
                @error('new_password_confirmation') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="row">
            <div class="col-sm-9 offset-sm-3">
                <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                <x-action-message class="ms-3" on="password-updated">
                    {{ __('Password updated successfully.') }}
                </x-action-message>
            </div>
        </div>
    </form>
</x-settings.layout>
