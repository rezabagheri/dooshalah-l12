import * as bootstrap from 'bootstrap/dist/js/bootstrap.bundle.min.js';
import 'admin-lte/dist/js/adminlte';

window.bootstrap = bootstrap;

document.addEventListener('DOMContentLoaded', () => {
    console.log('Bootstrap and AdminLTE loaded');

    const dropdownElementList = document.querySelectorAll('.dropdown-toggle');
    dropdownElementList.forEach(dropdown => new window.bootstrap.Dropdown(dropdown));
});

document.addEventListener('livewire:init', () => {
    console.log('Livewire initialized');

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

    Livewire.on('error', (message) => {
        showToast(message, 'danger');
    });
});

document.addEventListener('DOMContentLoaded', () => {
    console.log('AdminLTE loaded');
});

function showToast(message, type) {
    const toast = new window.bootstrap.Toast(document.getElementById('livewire-toast'));
    document.querySelector('#livewire-toast .toast-body').textContent = message;
    document.querySelector('#livewire-toast').className = `toast bg-${type}`;
    toast.show();
}
