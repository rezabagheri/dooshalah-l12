// resources/js/app.js
import * as bootstrap from 'bootstrap/dist/js/bootstrap.bundle.min.js';
import 'admin-lte/dist/js/adminlte';
import { requestNotificationPermission } from './firebase.js';
import { initializeToastListeners } from './toast.js';

window.bootstrap = bootstrap;
window.requestNotificationPermission = requestNotificationPermission;

document.addEventListener('DOMContentLoaded', () => {
    console.log('Bootstrap and AdminLTE loaded');
    const dropdownElementList = document.querySelectorAll('.dropdown-toggle');
    dropdownElementList.forEach(dropdown => new window.bootstrap.Dropdown(dropdown));
});

document.addEventListener('livewire:init', () => {
    console.log('Livewire initialized');
    initializeToastListeners();
});
