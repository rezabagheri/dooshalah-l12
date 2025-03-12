<div>
    <p class="login-box-msg">Register a new membership</p>

    @if (session('status'))
        <div class="alert alert-success text-center mb-3">
            {{ session('status') }}
        </div>
    @endif

    <form wire:submit="register">
        <!-- First Name -->
        <div class="form-floating mb-3">
            <input type="text" name="first_name" wire:model="first_name" class="form-control @error('first_name') is-invalid @enderror" id="first_name" placeholder="First name" required autofocus>
            <label for="first_name">{{ __('First name') }}</label>
            @error('first_name')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <!-- Middle Name -->
        <div class="form-floating mb-3">
            <input type="text" name="middle_name" wire:model="middle_name" class="form-control @error('middle_name') is-invalid @enderror" id="middle_name" placeholder="Middle name (optional)">
            <label for="middle_name">{{ __('Middle name (optional)') }}</label>
            @error('middle_name')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <!-- Last Name -->
        <div class="form-floating mb-3">
            <input type="text" name="last_name" wire:model="last_name" class="form-control @error('last_name') is-invalid @enderror" id="last_name" placeholder="Last name" required>
            <label for="last_name">{{ __('Last name') }}</label>
            @error('last_name')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <!-- Display Name -->
        <div class="form-floating mb-3">
            <input type="text" name="display_name" wire:model="display_name" class="form-control @error('display_name') is-invalid @enderror" id="display_name" placeholder="Display name" required>
            <label for="display_name">{{ __('Display name') }}</label>
            @error('display_name')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <!-- Gender -->
        <div class="form-floating mb-3">
            <select name="gender" wire:model="gender" class="form-control @error('gender') is-invalid @enderror" id="gender" required>
                @foreach ($this->genderOptions as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
            <label for="gender">{{ __('Gender') }}</label>
            @error('gender')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <!-- Birth Date -->
        <div class="form-floating mb-3">
            <input type="date" name="birth_date" wire:model="birth_date" class="form-control @error('birth_date') is-invalid @enderror" id="birth_date" placeholder="Birth date" required>
            <label for="birth_date">{{ __('Birth date') }}</label>
            @error('birth_date')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <!-- Email -->
        <div class="form-floating mb-3">
            <input type="email" name="email" wire:model="email" class="form-control @error('email') is-invalid @enderror" id="email" placeholder="Email" required>
            <label for="email">{{ __('Email') }}</label>
            @error('email')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <!-- Phone Number -->
        <div class="form-floating mb-3">
            <input type="tel" name="phone_number" wire:model="phone_number" class="form-control @error('phone_number') is-invalid @enderror" id="phone_number" placeholder="+1234567890123" required>
            <label for="phone_number">{{ __('Phone number') }}</label>
            @error('phone_number')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <!-- Father's Name -->
        <div class="form-floating mb-3">
            <input type="text" name="father_name" wire:model="father_name" class="form-control @error('father_name') is-invalid @enderror" id="father_name" placeholder="Father's name (optional)">
            <label for="father_name">{{ __('Father\'s name (optional)') }}</label>
            @error('father_name')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <!-- Mother's Name -->
        <div class="form-floating mb-3">
            <input type="text" name="mother_name" wire:model="mother_name" class="form-control @error('mother_name') is-invalid @enderror" id="mother_name" placeholder="Mother's name (optional)">
            <label for="mother_name">{{ __('Mother\'s name (optional)') }}</label>
            @error('mother_name')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <!-- Born Country -->
        <div class="form-floating mb-3">
            <select name="born_country" wire:model="born_country" class="form-control @error('born_country') is-invalid @enderror" id="born_country">
                <option value="">Select a country (optional)</option>
                @foreach ($this->countries as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
            <label for="born_country">{{ __('Country of birth (optional)') }}</label>
            @error('born_country')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <!-- Living Country -->
        <div class="form-floating mb-3">
            <select name="living_country" wire:model="living_country" class="form-control @error('living_country') is-invalid @enderror" id="living_country">
                <option value="">Select a country (optional)</option>
                @foreach ($this->countries as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
            <label for="living_country">{{ __('Country of residence (optional)') }}</label>
            @error('living_country')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <!-- Password -->
        <div class="form-floating mb-3">
            <input type="password" name="password" wire:model="password" class="form-control @error('password') is-invalid @enderror" id="password" placeholder="Password" required>
            <label for="password">{{ __('Password') }}</label>
            @error('password')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="form-floating mb-3">
            <input type="password" name="password_confirmation" wire:model="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" id="password_confirmation" placeholder="Confirm password" required>
            <label for="password_confirmation">{{ __('Confirm password') }}</label>
            @error('password_confirmation')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div class="row">
            <div class="col-12">
                <button type="submit" class="btn btn-primary btn-block">{{ __('Create account') }}</button>
            </div>
        </div>
    </form>

    <p class="mb-0 mt-3">
        @if (Route::has('login'))
            <a href="{{ route('login') }}" class="text-center">{{ __('Already have an account? Log in') }}</a>
        @endif
    </p>

    <button wire:click="toggleDarkMode" class="btn btn-secondary mt-3">Toggle Dark Mode</button>
</div>

<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('updateBodyClass', (bodyClass) => {
            document.body.className = bodyClass;
        });
    });
</script>
