import * as bootstrap from 'bootstrap/dist/js/bootstrap.bundle.min.js';
import 'admin-lte/dist/js/adminlte';
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
    console.log('Bootstrap and AdminLTE loaded');
    const dropdownElementList = document.querySelectorAll('.dropdown-toggle');
    dropdownElementList.forEach(dropdown => new window.bootstrap.Dropdown(dropdown));

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
                // اگه از قبل اجازه داده شده، توکن رو بگیر و ذخیره کن
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
});
