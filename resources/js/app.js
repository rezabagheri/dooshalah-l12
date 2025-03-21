import * as bootstrap from 'bootstrap/dist/js/bootstrap.bundle.min.js';
import 'admin-lte/dist/js/adminlte';
import { OverlayScrollbars } from 'overlayscrollbars';
import { requestNotificationPermission, onForegroundMessage } from './firebase.js';
import { initializeToastListeners, showToast } from './toast.js';

window.bootstrap = bootstrap;

const excludedPaths = [
    '/login',
    '/register',
    '/password/reset',
    '/password/email',
    '/password/confirm'
];

document.addEventListener('DOMContentLoaded', () => {
    console.log('Bootstrap, AdminLTE, and OverlayScrollbars loaded');
    const dropdownElementList = document.querySelectorAll('.dropdown-toggle');
    dropdownElementList.forEach(dropdown => new window.bootstrap.Dropdown(dropdown));

    const sidebar = document.querySelector('.sidebar-wrapper');
    if (sidebar) {
        OverlayScrollbars(sidebar, {
            scrollbars: {
                autoHide: 'scroll',
            },
        });
    }

    if (!excludedPaths.includes(window.location.pathname)) {
        setTimeout(() => {
            if (Notification.permission === 'default') {
                requestNotificationPermission().then((token) => {
                    if (token) {
                        console.log('Notifications enabled successfully.');
                    } else {
                        showToast('Please enable notifications in your browser settings and refresh the page.', 'info');
                    }
                }).catch((error) => {
                    console.error('Failed to request notification permission:', error);
                    showToast('Unable to enable notifications. Check your browser settings and refresh the page.', 'warning');
                });
            } else if (Notification.permission === 'granted') {
                requestNotificationPermission().then((token) => {
                    if (token) {
                        console.log('Token refreshed:', token);
                    }
                });
            } else {
                console.log('Notification permission already set:', Notification.permission);
                if (Notification.permission === 'denied') {
                    showToast('Notifications are blocked. Enable them in your browser settings and refresh the page.', 'info');
                }
            }
        }, 1000);
    } else {
        console.log('Skipping notification request on excluded page:', window.location.pathname);
    }
});

document.addEventListener('livewire:init', () => {
    console.log('Livewire initialized');
    initializeToastListeners();

    onForegroundMessage((payload) => {
        showToast(payload.notification.body, 'success');
    });

    Livewire.on('open-modal', () => {
        const modal = new bootstrap.Modal(document.getElementById('resetPasswordModal'));
        modal.show();
    });

    Livewire.on('close-modal', () => {
        const modal = bootstrap.Modal.getInstance(document.getElementById('resetPasswordModal'));
        if (modal) modal.hide();
    });

    Livewire.on('open-contact-modal', () => {
        const modal = new bootstrap.Modal(document.getElementById('contactModal'));
        modal.show();
    });

    Livewire.on('close-contact-modal', () => {
        const modal = bootstrap.Modal.getInstance(document.getElementById('contactModal'));
        if (modal) modal.hide();
    });

    Livewire.on('show-image-modal', () => {
        console.log('Show image modal event received');
        const modalElement = document.getElementById('imageModal');
        if (modalElement) {
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
            console.log('Modal opened successfully');
        } else {
            console.error('Modal element not found');
        }
    });
});
