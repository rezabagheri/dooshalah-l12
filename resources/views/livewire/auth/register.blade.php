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

/**
 * Class Register
 *
 * Handles user registration functionality including form validation, user creation,
 * authentication, and welcome email dispatch.
 *
 * @package App\Http\Livewire\Auth
 */
new #[Layout('components.layouts.auth')] class extends Component {
    /**
     * @var string User's first name
     */
    public string $first_name = '';

    /**
     * @var string User's middle name (optional)
     */
    public string $middle_name = '';

    /**
     * @var string User's last name
     */
    public string $last_name = '';

    /**
     * @var string User's display name (must be unique)
     */
    public string $display_name = '';

    /**
     * @var string User's gender (Male or Female)
     */
    public string $gender = Gender::Male->value;

    /**
     * @var string User's birth date (must be at least 18 years old)
     */
    public string $birth_date = '';

    /**
     * @var string User's email address (must be unique)
     */
    public string $email = '';

    /**
     * @var string User's phone number (must be unique, format: +[country code][number])
     */
    public string $phone_number = '';

    /**
     * @var string User's father's name (optional)
     */
    public string $father_name = '';

    /**
     * @var string User's mother's name (optional)
     */
    public string $mother_name = '';

    /**
     * @var int|null User's country of birth (optional, references countries table)
     */
    public ?int $born_country = null;

    /**
     * @var int|null User's country of residence (optional, references countries table)
     */
    public ?int $living_country = null;

    /**
     * @var string User's password
     */
    public string $password = '';

    /**
     * @var string Confirmation of user's password
     */
    public string $password_confirmation = '';

    public $agreement = false;

    /**
     * @var string CSS class for the body element
     */
    public string $bodyClass = 'login-page bg-body-secondary';

    /**
     * @var string CSS class for the register box container
     */
    public string $boxClass = 'register-box';

    /**
     * Registers a new user.
     *
     * Validates the input data, creates a new user, logs them in, sends a welcome email,
     * and redirects to the profile settings page.
     *
     * @return void
     * @throws \Illuminate\Validation\ValidationException If validation fails
     */
    public function register(): void
    {
        \Log::info('Starting register method');

        try {
            $validated = $this->validate(
                [
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
                    'agreement' => ['required', 'accepted'], // Must be checked
                ],
                [
                    'birth_date.before_or_equal' => 'You must be at least 18 years old to register.',
                    'agreement.required' => 'You must agree to the Terms and Conditions.',
                    'agreement.accepted' => 'You must agree to the Terms and Conditions.',
                ],
            );

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

    /**
     * Retrieves the list of countries for dropdown options.
     *
     * @return array Associative array of country IDs and names
     */
    public function getCountriesProperty(): array
    {
        return Country::all()->pluck('name', 'id')->toArray();
    }

    /**
     * Provides gender options for the dropdown.
     *
     * @return array Associative array of gender values and their translated labels
     */
    public function getGenderOptionsProperty(): array
    {
        return [
            Gender::Male->value => __('Male'),
            Gender::Female->value => __('Female'),
        ];
    }

    /**
     * Toggles the dark mode for the page (currently disabled).
     *
     * Switches between light and dark mode by updating the body class.
     *
     * @return void
     */
    /*
    public function toggleDarkMode(): void
    {
        $this->bodyClass = $this->bodyClass === 'login-page bg-body-secondary' ? 'login-page dark-mode' : 'login-page bg-body-secondary';
        $this->dispatch('updateBodyClass', $this->bodyClass);
    }
    */

    /**
     * Renders the registration view.
     *
     * @return View The rendered view instance
     */
    public function render(): View
    {
        return view('auth.register')->layout('components.layouts.auth', [
            'bodyClass' => $this->bodyClass,
            'boxClass' => $this->boxClass,
        ]);
    }
}; ?>
