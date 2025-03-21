<div class="flex flex-col gap-6">
    <x-auth-header
        title="Confirm password"
        description="This is a secure area of the application. Please confirm your password before continuing."
    />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit="confirmPassword" class="flex flex-col gap-6">
        <!-- Password -->
        <div class="relative">
            <flux:input
                wire:model="password"
                id="password"
                label="{{ __('Password') }}"
                type="{{ $showPassword ? 'text' : 'password' }}"
                name="password"
                required
                autocomplete="new-password"
                placeholder="Password"
            />
            <span class="absolute right-3 top-1/2 transform -translate-y-1/2 cursor-pointer" wire:click="togglePassword">
                <i class="{{ $showPassword ? 'bi bi-eye-slash' : 'bi bi-eye' }}"></i>
            </span>
        </div>

        <flux:button variant="primary" type="submit" class="w-full">{{ __('Confirm') }}</flux:button>
    </form>
</div>
