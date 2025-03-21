<div class="register-box">
    <p class="login-box-msg fw-bold">{{ __('Email Verification') }}</p>

    <!-- پیام اصلی -->
    <div class="text-center mb-3">
        <p class="text-muted">
            {{ __('Please verify your email address by clicking on the link we just emailed to you.') }}
        </p>
    </div>

    <!-- پیام موفقیت ارسال مجدد -->
    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success text-center mb-3">
            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>
    @endif

    <!-- دکمه‌ها -->
    <div class="row mb-3">
        <div class="col-12">
            <button wire:click="sendVerification" class="btn btn-primary w-100 mb-3">
                {{ __('Resend verification email') }}
            </button>
        </div>
        <div class="col-12 text-center">
            <button wire:click="logout" class="btn btn-link text-muted">
                {{ __('Log out') }}
            </button>
        </div>
    </div>
</div>
