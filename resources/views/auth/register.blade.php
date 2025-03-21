<div class="register-box">
    <p class="login-box-msg">Create a new account</p>

    @if (session('status'))
        <div class="alert alert-success text-center mb-3">
            {{ session('status') }}
        </div>
    @endif

    <form wire:submit="register">
        <!-- سطر ۱: اطلاعات نام -->
        <div class="row mb-3">
            <div class="col-md-4">
                <div class="form-floating">
                    <input type="text" name="first_name" wire:model="first_name" class="form-control @error('first_name') is-invalid @enderror" id="first_name" placeholder="First name" required autofocus>
                    <label for="first_name">{{ __('First name') }}</label>
                    @error('first_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <small class="form-text text-muted">Required, max 255 characters.</small>
            </div>
            <div class="col-md-4">
                <div class="form-floating">
                    <input type="text" name="middle_name" wire:model="middle_name" class="form-control @error('middle_name') is-invalid @enderror" id="middle_name" placeholder="Middle name (optional)">
                    <label for="middle_name">{{ __('Middle name (optional)') }}</label>
                    @error('middle_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <small class="form-text text-muted">Optional, max 255 characters.</small>
            </div>
            <div class="col-md-4">
                <div class="form-floating">
                    <input type="text" name="last_name" wire:model="last_name" class="form-control @error('last_name') is-invalid @enderror" id="last_name" placeholder="Last name" required>
                    <label for="last_name">{{ __('Last name') }}</label>
                    @error('last_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <small class="form-text text-muted">Required, max 255 characters.</small>
            </div>
        </div>

        <!-- سطر ۲: نام نمایشی و جنسیت -->
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="form-floating">
                    <input type="text" name="display_name" wire:model="display_name" class="form-control @error('display_name') is-invalid @enderror" id="display_name" placeholder="Display name" required>
                    <label for="display_name">{{ __('Display name') }}</label>
                    @error('display_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <small class="form-text text-muted">Required, max 255 characters, must be unique.</small>
            </div>
            <div class="col-md-6">
                <div class="form-floating">
                    <select name="gender" wire:model="gender" class="form-control @error('gender') is-invalid @enderror" id="gender" required>
                        <option value="" disabled selected>{{ __('Select gender') }}</option>
                        @foreach ($this->genderOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    <label for="gender">{{ __('Gender') }}</label>
                    @error('gender') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <small class="form-text text-muted">Required, select Male or Female.</small>
            </div>
        </div>

        <!-- سطر ۳: تاریخ تولد و ایمیل -->
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="form-floating">
                    <input type="date" name="birth_date" wire:model="birth_date" class="form-control @error('birth_date') is-invalid @enderror" id="birth_date" placeholder="Birth date" required>
                    <label for="birth_date">{{ __('Birth date') }}</label>
                    @error('birth_date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <small class="form-text text-muted">Required, must be a date before today.</small>
            </div>
            <div class="col-md-6">
                <div class="form-floating">
                    <input type="email" name="email" wire:model="email" class="form-control @error('email') is-invalid @enderror" id="email" placeholder="Email" required>
                    <label for="email">{{ __('Email') }}</label>
                    @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <small class="form-text text-muted">Required, valid email, max 255 characters, must be unique.</small>
            </div>
        </div>

        <!-- سطر ۴: شماره تلفن و کشور تولد -->
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="form-floating">
                    <input type="tel" name="phone_number" wire:model="phone_number" class="form-control @error('phone_number') is-invalid @enderror" id="phone_number" placeholder="+1234567890123" required>
                    <label for="phone_number">{{ __('Phone number') }}</label>
                    @error('phone_number') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <small class="form-text text-muted">Required, format: +[country code][number], max 20 characters, must be unique.</small>
            </div>
            <div class="col-md-6">
                <div class="form-floating">
                    <select name="born_country" wire:model="born_country" class="form-control @error('born_country') is-invalid @enderror" id="born_country">
                        <option value="" selected>{{ __('Select a country (optional)') }}</option>
                        @foreach ($this->countries as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    <label for="born_country">{{ __('Country of birth (optional)') }}</label>
                    @error('born_country') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <small class="form-text text-muted">Optional, select a valid country.</small>
            </div>
        </div>

        <!-- سطر ۵: کشور اقامت و نام والدین -->
        <div class="row mb-3">
            <div class="col-md-4">
                <div class="form-floating">
                    <select name="living_country" wire:model="living_country" class="form-control @error('living_country') is-invalid @enderror" id="living_country">
                        <option value="" selected>{{ __('Select a country (optional)') }}</option>
                        @foreach ($this->countries as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    <label for="living_country">{{ __('Country of residence (optional)') }}</label>
                    @error('living_country') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <small class="form-text text-muted">Optional, select a valid country.</small>
            </div>
            <div class="col-md-4">
                <div class="form-floating">
                    <input type="text" name="father_name" wire:model="father_name" class="form-control @error('father_name') is-invalid @enderror" id="father_name" placeholder="Father's name (optional)">
                    <label for="father_name">{{ __('Father\'s name (optional)') }}</label>
                    @error('father_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <small class="form-text text-muted">Optional, max 128 characters.</small>
            </div>
            <div class="col-md-4">
                <div class="form-floating">
                    <input type="text" name="mother_name" wire:model="mother_name" class="form-control @error('mother_name') is-invalid @enderror" id="mother_name" placeholder="Mother's name (optional)">
                    <label for="mother_name">{{ __('Mother\'s name (optional)') }}</label>
                    @error('mother_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <small class="form-text text-muted">Optional, max 128 characters.</small>
            </div>
        </div>

        <!-- سطر ۶: رمز عبور -->
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="form-floating">
                    <input type="password" name="password" wire:model="password" class="form-control @error('password') is-invalid @enderror" id="password" placeholder="Password" required>
                    <label for="password">{{ __('Password') }}</label>
                    @error('password') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <small class="form-text text-muted">Required, must match confirmation, follow password rules.</small>
            </div>
            <div class="col-md-6">
                <div class="form-floating">
                    <input type="password" name="password_confirmation" wire:model="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" id="password_confirmation" placeholder="Confirm password" required>
                    <label for="password_confirmation">{{ __('Confirm password') }}</label>
                    @error('password_confirmation') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <small class="form-text text-muted">Required, must match password.</small>
            </div>
        </div>

        <!-- دکمه ثبت‌نام -->
        <div class="row">
            <div class="col-12">
                <button type="submit" class="btn btn-primary btn-block mb-3">{{ __('Create account') }}</button>
            </div>
        </div>
    </form>

    <!-- لینک ورود و دارک مود -->
    <div class="row">
        <div class="col-6">
            @if (Route::has('login'))
                <p class="mb-0">
                    <a href="{{ route('login') }}" class="text-center">{{ __('Already have an account? Log in') }}</a>
                </p>
            @endif
        </div>
        <div class="col-6 text-right">
            <button wire:click="toggleDarkMode" class="btn btn-secondary btn-sm">{{ __('Toggle Dark Mode') }}</button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('updateBodyClass', (bodyClass) => {
            document.body.className = bodyClass;
        });
    });
</script>
