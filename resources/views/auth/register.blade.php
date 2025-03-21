<div class="register-box">
    <p class="login-box-msg fw-bold">Create a new account</p>

    @if (session('status'))
        <div class="alert alert-success text-center mb-3">
            {{ session('status') }}
        </div>
    @endif

    <form wire:submit="register">
        <!-- سطر ۱: اطلاعات نام -->
        <div class="row mb-3">
            <div class="col-md-4">
                <div class="input-group">
                    <input type="text" name="first_name" wire:model="first_name" class="form-control @error('first_name') is-invalid @enderror" id="first_name" placeholder="First name" required autofocus>
                    <div class="input-group-text"><i class="bi bi-person"></i></div>
                    @error('first_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <small class="form-text text-muted fs-8">Required, max 255 characters.</small>
            </div>
            <div class="col-md-4">
                <div class="input-group">
                    <input type="text" name="middle_name" wire:model="middle_name" class="form-control @error('middle_name') is-invalid @enderror" id="middle_name" placeholder="Middle name (optional)">
                    <div class="input-group-text"><i class="bi bi-person"></i></div>
                    @error('middle_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <small class="form-text text-muted fs-8">Optional, max 255 characters.</small>
            </div>
            <div class="col-md-4">
                <div class="input-group">
                    <input type="text" name="last_name" wire:model="last_name" class="form-control @error('last_name') is-invalid @enderror" id="last_name" placeholder="Last name" required>
                    <div class="input-group-text"><i class="bi bi-person"></i></div>
                    @error('last_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <small class="form-text text-muted fs-8">Required, max 255 characters.</small>
            </div>
        </div>

        <!-- سطر ۲: نام نمایشی و جنسیت -->
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="input-group">
                    <input type="text" name="display_name" wire:model="display_name" class="form-control @error('display_name') is-invalid @enderror" id="display_name" placeholder="Display name" required>
                    <div class="input-group-text"><i class="bi bi-person-badge"></i></div>
                    @error('display_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <small class="form-text text-muted fs-8">Required, max 255 characters, must be unique.</small>
            </div>
            <div class="col-md-6">
                <div class="input-group">
                    <select name="gender" wire:model="gender" class="form-control @error('gender') is-invalid @enderror" id="gender" required>
                        <option value="" disabled selected>{{ __('Select gender') }}</option>
                        @foreach ($this->genderOptions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    <div class="input-group-text"><i class="bi bi-gender-ambiguous"></i></div>
                    @error('gender') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <small class="form-text text-muted fs-8">Required, select Male or Female.</small>
            </div>
        </div>

        <!-- سطر ۳: تاریخ تولد و ایمیل -->
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="input-group">
                    <input type="date" name="birth_date" wire:model="birth_date" class="form-control @error('birth_date') is-invalid @enderror" id="birth_date" placeholder="Birth date" required max="{{ now()->subYears(18)->toDateString() }}">
                    <div class="input-group-text"><i class="bi bi-calendar"></i></div>
                    @error('birth_date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <small class="form-text text-muted fs-8">Required, must be at least 18 years old.</small>
            </div>
            <div class="col-md-6">
                <div class="input-group">
                    <input type="email" name="email" wire:model="email" class="form-control @error('email') is-invalid @enderror" id="email" placeholder="Email" required>
                    <div class="input-group-text"><i class="bi bi-envelope"></i></div>
                    @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <small class="form-text text-muted fs-8">Required, valid email, max 255 characters, must be unique.</small>
            </div>
        </div>

        <!-- سطر ۴: شماره تلفن و کشور تولد -->
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="input-group">
                    <input type="tel" name="phone_number" wire:model="phone_number" class="form-control @error('phone_number') is-invalid @enderror" id="phone_number" placeholder="+1234567890123" required>
                    <div class="input-group-text"><i class="bi bi-telephone"></i></div>
                    @error('phone_number') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <small class="form-text text-muted fs-8">Required, format: +[country code][number], max 20 characters, must be unique.</small>
            </div>
            <div class="col-md-6">
                <div class="input-group">
                    <select name="born_country" wire:model="born_country" class="form-control @error('born_country') is-invalid @enderror" id="born_country">
                        <option value="" selected>{{ __('Select a country (optional)') }}</option>
                        @foreach ($this->countries as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    <div class="input-group-text"><i class="bi bi-geo-alt"></i></div>
                    @error('born_country') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <small class="form-text text-muted fs-8">Optional, select a valid country.</small>
            </div>
        </div>

        <!-- سطر ۵: کشور اقامت و نام والدین -->
        <div class="row mb-3">
            <div class="col-md-4">
                <div class="input-group">
                    <select name="living_country" wire:model="living_country" class="form-control @error('living_country') is-invalid @enderror" id="living_country">
                        <option value="" selected>{{ __('Select a country (optional)') }}</option>
                        @foreach ($this->countries as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    <div class="input-group-text"><i class="bi bi-geo-alt"></i></div>
                    @error('living_country') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <small class="form-text text-muted fs-8">Optional, select a valid country.</small>
            </div>
            <div class="col-md-4">
                <div class="input-group">
                    <input type="text" name="father_name" wire:model="father_name" class="form-control @error('father_name') is-invalid @enderror" id="father_name" placeholder="Father's name (optional)">
                    <div class="input-group-text"><i class="bi bi-person"></i></div>
                    @error('father_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <small class="form-text text-muted fs-8">Optional, max 128 characters.</small>
            </div>
            <div class="col-md-4">
                <div class="input-group">
                    <input type="text" name="mother_name" wire:model="mother_name" class="form-control @error('mother_name') is-invalid @enderror" id="mother_name" placeholder="Mother's name (optional)">
                    <div class="input-group-text"><i class="bi bi-person"></i></div>
                    @error('mother_name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <small class="form-text text-muted fs-8">Optional, max 128 characters.</small>
            </div>
        </div>

        <!-- سطر ۶: رمز عبور -->
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="input-group">
                    <input type="{{ $showPassword ? 'text' : 'password' }}" name="password" wire:model="password" class="form-control @error('password') is-invalid @enderror" id="password" placeholder="Password" required>
                    <div class="input-group-text">
                        <i class="{{ $showPassword ? 'bi bi-eye-slash' : 'bi bi-eye' }}" wire:click="togglePassword" style="cursor: pointer;"></i>
                    </div>
                    <div class="input-group-text"><i class="bi bi-lock"></i></div>
                    @error('password') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <small class="form-text text-muted fs-8">Required, must match confirmation, follow password rules.</small>
            </div>
            <div class="col-md-6">
                <div class="input-group">
                    <input type="{{ $showPassword ? 'text' : 'password' }}" name="password_confirmation" wire:model="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" id="password_confirmation" placeholder="Confirm password" required>
                    <div class="input-group-text">
                        <i class="{{ $showPassword ? 'bi bi-eye-slash' : 'bi bi-eye' }}" wire:click="togglePassword" style="cursor: pointer;"></i>
                    </div>
                    <div class="input-group-text"><i class="bi bi-lock"></i></div>
                    @error('password_confirmation') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
                <small class="form-text text-muted fs-8">Required, must match password.</small>
            </div>
        </div>

        <!-- چک‌باکس Agreement -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="form-check">
                    <input type="checkbox" name="agreement" wire:model="agreement" class="form-check-input @error('agreement') is-invalid @enderror" id="agreement" required>
                    <label class="form-check-label" for="agreement">
                        I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#agreementModal">Terms and Conditions</a>
                    </label>
                    @error('agreement') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- دکمه ثبت‌نام -->
        <div class="row">
            <div class="col-12">
                <button type="submit" class="btn btn-primary w-100 mb-3">{{ __('Create account') }}</button>
            </div>
        </div>
    </form>

    <!-- لینک ورود -->
    <div class="row">
        <div class="col-12 text-center">
            @if (Route::has('login'))
                <p class="mb-0">
                    <a href="{{ route('login') }}">{{ __('Already have an account? Log in') }}</a>
                </p>
            @endif
        </div>
    </div>

    <!-- Bootstrap Modal برای Agreement -->
    <div class="modal fade" id="agreementModal" tabindex="-1" aria-labelledby="agreementModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="agreementModalLabel">Terms and Conditions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h2>Welcome to Our Platform!</h2>
                    <p>By registering, you agree to the following terms:</p>
                    <ul>
                        <li><strong>Eligibility:</strong> You must be at least 18 years old to use this service.</li>
                        <li><em>Privacy:</em> We respect your privacy and will protect your data as outlined in our <a href="#">Privacy Policy</a>.</li>
                        <li><strong>Conduct:</strong> You agree not to use this platform for illegal activities.</li>
                        <li><em>Content:</em> Any content you upload must comply with our community guidelines.</li>
                    </ul>
                    <h3>Additional Notes</h3>
                    <p>This is a <b>sample agreement</b> and may span multiple pages. You are responsible for reading and understanding all terms before agreeing.</p>
                    <p>For more details, contact us at <a href="mailto:support@example.com">support@example.com</a>.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const setupBirthDateValidation = () => {
                const birthDateInput = document.getElementById('birth_date');
                if (birthDateInput) {
                    birthDateInput.addEventListener('invalid', function (e) {
                        this.setCustomValidity('You must be at least 18 years old to register.');
                    });

                    birthDateInput.addEventListener('input', function () {
                        this.setCustomValidity('');
                    });

                    birthDateInput.addEventListener('change', function () {
                        @this.set('birth_date', this.value);
                    });
                } else {
                    console.warn('birth_date element not found yet, retrying...');
                    setTimeout(setupBirthDateValidation, 100);
                }
            };

            setupBirthDateValidation();
        });
    </script>
</div>
