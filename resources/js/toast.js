export function showToast(message, type) {
    const toast = new window.bootstrap.Toast(document.getElementById('livewire-toast'));
    document.querySelector('#livewire-toast .toast-body').textContent = message;
    document.querySelector('#livewire-toast').className = `toast bg-${type}`;
    toast.show();
}

export function initializeToastListeners() {
    Livewire.on('friendship-request-sent', () => {
        showToast('Friendship request sent successfully', 'success');
    });

    Livewire.on('friendship-accepted', () => {
        showToast('Friendship accepted', 'success');
    });

    Livewire.on('friendship-rejected', () => {
        showToast('Friendship rejected', 'warning');
    });

    Livewire.on('friendship-cancelled', () => {
        showToast('Friendship request cancelled', 'info');
    });

    Livewire.on('friendship-removed', () => {
        showToast('Friend removed', 'info');
    });

    Livewire.on('user-blocked', () => {
        showToast('User blocked', 'warning');
    });

    Livewire.on('user-unblocked', () => {
        showToast('User unblocked', 'success');
    });

    Livewire.on('user-reported', () => {
        showToast('User reported', 'danger');
    });

    Livewire.on('show-delete-modal', () => {
        console.log('Show delete modal triggered');
        const modal = new window.bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    });

    Livewire.on('close-delete-modal', () => {
        console.log('Close delete modal triggered');
        const modalElement = document.getElementById('deleteModal');
        const modal = window.bootstrap.Modal.getInstance(modalElement);
        if (modal) {
            modal.hide();
        }
    });

    Livewire.on('photo-uploaded', () => {
        showToast('Photo uploaded successfully', 'success');
    });

    Livewire.on('photo-deleted', () => {
        showToast('Photo deleted successfully', 'success');
    });

    Livewire.on('profile-picture-updated', () => {
        showToast('Profile picture updated successfully', 'success');
    });

    Livewire.on('profile-updated', (event) => {
        showToast(`Profile updated successfully for ${event.name}`, 'success');
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

    Livewire.on('error', (message) => {
        showToast(message, 'danger');
    });

    Livewire.on('messageReceived', () => {
        // می‌تونیم اینجا چیزی اضافه کنیم، ولی فعلاً خالی می‌ذاریم چون تو app.js نشون داده می‌شه
    });
}
