import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import 'admin-lte/dist/js/adminlte';

const bootstrap = window.bootstrap;

document.addEventListener('DOMContentLoaded', () => {
    console.log('Bootstrap and AdminLTE loaded');

    const dropdownElementList = document.querySelectorAll('.dropdown-toggle');
    dropdownElementList.forEach(dropdown => new bootstrap.Dropdown(dropdown));
});
document.addEventListener('DOMContentLoaded', () => {
    console.log('AdminLTE loaded');
});
