<?php

namespace App\Livewire\Auth;

use App\Enums\Gender;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Mail\WelcomeMail;
use App\Models\Country;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Livewire\Component;

class Register extends Component
{
    public string $first_name = '';
    public string $middle_name = '';
    public string $last_name = '';
    public string $display_name = '';
    public string $gender = Gender::Male->value;
    public string $birth_date = '';
    public string $email = '';
    public string $phone_number = '';
    public string $father_name = '';
    public string $mother_name = '';
    public ?int $born_country = null;
    public ?int $living_country = null;
    public string $password = '';
    public string $password_confirmation = '';
    public bool $agreement = false;
    public string $bodyClass = 'login-page bg-body-secondary';
    public string $boxClass = 'register-box';
    public bool $showPassword = false; // برای نمایش رمز

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'display_name' => ['required', 'string', 'max:255', 'unique:' . User::class],
            'gender' => ['required', 'in:' . implode(',', [Gender::Male->value, Gender::Female->value])],
            'birth_date' => ['required', 'date', 'before_or_equal:' . now()->subYears(18)->toDateString()],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'phone_number' => ['required', 'string', 'max:20', 'unique:' . User::class, 'regex:/^\+[1-9]\d{1,14}$/'],
            'father_name' => ['nullable', 'string', 'max:128'],
            'mother_name' => ['nullable', 'string', 'max:128'],
            'born_country' => ['nullable', 'exists:countries,id'],
            'living_country' => ['nullable', 'exists:countries,id'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
            'agreement' => ['required', 'accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'birth_date.before_or_equal' => 'You must be at least 18 years old to register.',
            'agreement.required' => 'You must agree to the Terms and Conditions.',
            'agreement.accepted' => 'You must agree to the Terms and Conditions.',
        ];
    }

    public function register(): void
    {
        \Log::info('Starting register method');

        try {
            $validated = $this->validate();

            \Log::info('Validation passed', $validated);

            $validated['password'] = Hash::make($validated['password']);
            $validated['role'] = UserRole::Normal->value;
            $validated['status'] = UserStatus::Pending->value;

            \Log::info('Creating user');
            event(new Registered(($user = User::create($validated))));

            \Log::info('User created', ['user_id' => $user->id]);

            Auth::login($user);
            \Log::info('User logged in', ['user_id' => $user->id]);

            \Log::info('Sending welcome email to: ' . $user->email);
            Mail::to($user->email)->send(new WelcomeMail($user));
            \Log::info('Welcome email sent to: ' . $user->email);

            $this->redirect(route('settings.profile', absolute: false), navigate: true);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed', ['errors' => $e->errors()]);
            foreach ($e->errors() as $field => $messages) {
                foreach ($messages as $message) {
                    $this->addError($field, $message);
                }
            }
            $this->dispatch('show-toast', ['message' => 'Please check your inputs: ' . implode(', ', $e->errors()[array_key_first($e->errors())]), 'type' => 'danger']);
        } catch (\Exception $e) {
            \Log::error('Registration failed', ['message' => $e->getMessage()]);
            $this->dispatch('show-toast', ['message' => 'An error occurred: ' . $e->getMessage(), 'type' => 'danger']);
        }
    }

    public function getCountriesProperty(): array
    {
        return Country::all()->pluck('name', 'id')->toArray();
    }

    public function getGenderOptionsProperty(): array
    {
        return [
            Gender::Male->value => __('Male'),
            Gender::Female->value => __('Female'),
        ];
    }

    public function togglePassword(): void
    {
        $this->showPassword = !$this->showPassword;
    }

    public function render()
    {
        return view('auth.register')
            ->layout('components.layouts.auth', [
                'bodyClass' => $this->bodyClass,
                'boxClass' => $this->boxClass,
            ]);
    }
}
