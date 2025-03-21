// toast.js
export function showToast(message, type) {
    const toast = new window.bootstrap.Toast(document.getElementById('livewire-toast'));
    document.querySelector('#livewire-toast .toast-body').textContent = message;
    document.querySelector('#livewire-toast').className = `toast bg-${type}`;
    toast.show();
}

export function initializeToastListeners() {
    // رویداد عمومی برای همه Toast‌ها
    Livewire.on('show-toast', (event) => {
        console.log('Show toast received:', event);
        const data = event[0]; // چون Livewire داده رو تو آرایه می‌فرسته
        showToast(data.message, data.type);
    });

    // رویدادهای خاص
    Livewire.on('profile-updated', (event) => {
        const data = event[0] || event; // پشتیبانی از هر دو فرمت
        showToast(`Profile updated successfully for ${data.name}`, 'success');
    });

    Livewire.on('status-updated', (event) => {
        const data = event[0] || event;
        showToast(`Status updated to "${data.status}" for ${data.name}`, 'success');
    });

    Livewire.on('error', (event) => {
        console.log('error event received:', event);
        const message = event[0] || event; // پشتیبانی از رشته مستقیم یا آرایه
        showToast(message, 'danger');
    });

    Livewire.on('photo-uploaded', () => {
        console.log('photo-uploaded event received');
        showToast('Photo uploaded successfully', 'success');
    });

    Livewire.on('photo-deleted', () => {
        console.log('photo-deleted event received');
        showToast('Photo deleted successfully', 'success');
    });

    Livewire.on('profile-picture-updated', () => {
        console.log('profile-picture-updated event received');
        showToast('Profile picture updated successfully', 'success');
    });

    Livewire.on('verification-link-sent', () => {
        showToast('A new verification link has been sent to your email address.', 'info');
    });

    Livewire.on('password-updated', () => {
        showToast('Password updated successfully', 'success');
    });

    Livewire.on('interests-updated', () => {
        showToast('Your interests have been updated successfully', 'success');
    });

    Livewire.on('messageReceived', () => {
        // خالی
    });
}
