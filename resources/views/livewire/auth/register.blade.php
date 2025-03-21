<?php

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
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Illuminate\Contracts\View\View;

new #[Layout('components.layouts.auth')] class extends Component {
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

    public string $bodyClass = 'login-page bg-body-secondary';
    public string $boxClass = 'register-box';

    public function register(): void
    {
        \Log::info('Starting register method'); // لاگ شروع متد

        $validated = $this->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'display_name' => ['required', 'string', 'max:255', 'unique:' . User::class],
            'gender' => ['required', 'in:' . implode(',', [Gender::Male->value, Gender::Female->value])],
            'birth_date' => ['required', 'date', 'before:today'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'phone_number' => ['required', 'string', 'max:20', 'unique:' . User::class, 'regex:/^\+[1-9]\d{1,14}$/'],
            'father_name' => ['nullable', 'string', 'max:128'],
            'mother_name' => ['nullable', 'string', 'max:128'],
            'born_country' => ['nullable', 'exists:countries,id'],
            'living_country' => ['nullable', 'exists:countries,id'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        \Log::info('Validation passed', $validated); // لاگ بعد از اعتبارسنجی

        $validated['password'] = Hash::make($validated['password']);
        $validated['role'] = UserRole::Normal->value;
        $validated['status'] = UserStatus::Pending->value;

        \Log::info('Creating user'); // لاگ قبل از ایجاد کاربر
        event(new Registered(($user = User::create($validated))));

        \Log::info('User created', ['user_id' => $user->id]); // لاگ بعد از ایجاد کاربر

        Auth::login($user);
        \Log::info('User logged in', ['user_id' => $user->id]); // لاگ بعد از لاگین

        // ارسال ایمیل با روش Mail::to()
        \Log::info('Sending welcome email to: ' . $user->email); // لاگ قبل از ارسال ایمیل
        try {
           Mail::to($user->email)->send(new WelcomeMail($user));
            \Log::info('Welcome email sent to: ' . $user->email);
        } catch (\Exception $e) {
            \Log::error('Failed to send welcome email: ' . $e->getMessage());
        }
        //Mail::to($user->email)->send(new WelcomeMail($user));


        $this->redirect(route('settings.profile', absolute: false), navigate: true);
    }

    public function getCountriesProperty()
    {
        return Country::all()->pluck('name', 'id')->toArray();
    }

    public function getGenderOptionsProperty()
    {
        return [
            Gender::Male->value => __('Male'),
            Gender::Female->value => __('Female'),
        ];
    }

    /*
    public function toggleDarkMode(): void
    {
        $this->bodyClass = $this->bodyClass === 'login-page bg-body-secondary' ? 'login-page dark-mode' : 'login-page bg-body-secondary';
        $this->dispatch('updateBodyClass', $this->bodyClass);
    }
    */
    public function render(): View
    {
        return view('auth.register')->layout('components.layouts.auth', [
            'bodyClass' => $this->bodyClass,
            'boxClass' => $this->boxClass,
        ]);
    }
}; ?>
