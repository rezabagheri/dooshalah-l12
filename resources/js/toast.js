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

    // رویدادهای خاص که نیاز به منطق اضافی دارن
    Livewire.on('profile-updated', (event) => {
        const data = event[0];
        showToast(`Profile updated successfully for ${data.name}`, 'success');
    });

    Livewire.on('status-updated', (event) => {
        const data = event[0];
        showToast(`Status updated to "${data.status}" for ${data.name}`, 'success');
    });
}
