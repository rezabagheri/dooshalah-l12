<?php

namespace App\Livewire\Settings;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Profile extends Component
{
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

        try {
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
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->validator->errors()->all();
            foreach ($errors as $error) {
                $this->dispatch('error', $error);
            }
            return;
        }
    }

    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));
            return;
        }

        $user->sendEmailVerificationNotification();

        $this->dispatch('verification-link-sent');
    }

    public function render()
    {
        return view('livewire.settings.profile');
    }
}
