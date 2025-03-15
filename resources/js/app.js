import * as bootstrap from 'bootstrap/dist/js/bootstrap.bundle.min.js';
import 'admin-lte/dist/js/adminlte';
import { requestNotificationPermission, onForegroundMessage } from './firebase.js';
import { initializeToastListeners, showToast } from './toast.js'; // showToast رو هم ایمپورت می‌کنیم

window.bootstrap = bootstrap;

document.addEventListener('DOMContentLoaded', () => {
    console.log('Bootstrap and AdminLTE loaded');
    const dropdownElementList = document.querySelectorAll('.dropdown-toggle');
    dropdownElementList.forEach(dropdown => new window.bootstrap.Dropdown(dropdown));

    // درخواست اجازه نوتیفیکیشن و گرفتن توکن
    requestNotificationPermission();
});

document.addEventListener('livewire:init', () => {
    console.log('Livewire initialized');
    initializeToastListeners();

    // نمایش نوتیفیکیشن‌ها وقتی صفحه بازه
    onForegroundMessage((payload) => {
        showToast(payload.notification.body, 'success'); // استفاده از showToast به جای toastr
        Livewire.dispatch('messageReceived');
    });
});
