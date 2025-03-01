<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component {
    public string $first_name = '';
    public ?string $middle_name = null;
    public string $last_name = '';
    public string $display_name = '';
    public string $email = '';
    public string $phone_number = '';
    public string $birth_date = '';

    public function mount(): void
    {
        $user = Auth::user();
        $this->first_name = $user->first_name;
        $this->middle_name = $user->middle_name;
        $this->last_name = $user->last_name;
        $this->display_name = $user->display_name;
        $this->email = $user->email;
        $this->phone_number = $user->phone_number;
        $this->birth_date = $user->birth_date->format('Y-m-d');
    }

    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'display_name' => ['required', 'string', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($user->id),
            ],
            'phone_number' => [
                'required',
                'string',
                'max:20',
                Rule::unique(User::class)->ignore($user->id),
            ],
            'birth_date' => ['required', 'date', 'before:today'],
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->display_name);
    }

    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));
            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }
}; ?>

<x-settings.layout heading="Profile" subheading="Update your personal information">
    <form wire:submit="updateProfileInformation" class="form-horizontal">
        <div class="row mb-3">
            <label for="first_name" class="col-sm-3 col-form-label">{{ __('First Name') }}</label>
            <div class="col-sm-9">
                <input wire:model="first_name" type="text" class="form-control" id="first_name" required autofocus autocomplete="given-name">
                @error('first_name') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="row mb-3">
            <label for="middle_name" class="col-sm-3 col-form-label">{{ __('Middle Name') }}</label>
            <div class="col-sm-9">
                <input wire:model="middle_name" type="text" class="form-control" id="middle_name" autocomplete="additional-name">
                @error('middle_name') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="row mb-3">
            <label for="last_name" class="col-sm-3 col-form-label">{{ __('Last Name') }}</label>
            <div class="col-sm-9">
                <input wire:model="last_name" type="text" class="form-control" id="last_name" required autocomplete="family-name">
                @error('last_name') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="row mb-3">
            <label for="display_name" class="col-sm-3 col-form-label">{{ __('Display Name') }}</label>
            <div class="col-sm-9">
                <input wire:model="display_name" type="text" class="form-control" id="display_name" required autocomplete="nickname">
                @error('display_name') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="row mb-3">
            <label for="email" class="col-sm-3 col-form-label">{{ __('Email') }}</label>
            <div class="col-sm-9">
                <input wire:model="email" type="email" class="form-control" id="email" required autocomplete="email">
                @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !auth()->user()->hasVerifiedEmail())
                    <p class="text-sm text-gray-600 mt-2">
                        {{ __('Your email address is unverified.') }}
                        <button wire:click.prevent="resendVerificationNotification"
                                class="btn btn-link p-0 text-sm">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>
                    @if (session('status') === 'verification-link-sent')
                        <p class="text-sm text-success mt-2">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                @endif
            </div>
        </div>
        <div class="row mb-3">
            <label for="phone_number" class="col-sm-3 col-form-label">{{ __('Phone Number') }}</label>
            <div class="col-sm-9">
                <input wire:model="phone_number" type="tel" class="form-control" id="phone_number" required autocomplete="tel">
                @error('phone_number') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="row mb-3">
            <label for="birth_date" class="col-sm-3 col-form-label">{{ __('Birth Date') }}</label>
            <div class="col-sm-9">
                <input wire:model="birth_date" type="date" class="form-control" id="birth_date" required>
                @error('birth_date') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="row">
            <div class="col-sm-9 offset-sm-3">
                <button type="submit" class="btn btn-primary">
                    {{ __('Save') }}
                </button>
                <x-action-message class="ms-3" on="profile-updated">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>
        </div>
    </form>
</x-settings.layout>
