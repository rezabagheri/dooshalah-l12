import * as bootstrap from 'bootstrap/dist/js/bootstrap.bundle.min.js';
import 'admin-lte/dist/js/adminlte';
import { initializeApp } from 'firebase/app';
import { getMessaging, getToken, onMessage } from 'firebase/messaging';

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
});

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

function showToast(message, type) {
    const toast = new window.bootstrap.Toast(document.getElementById('livewire-toast'));
    document.querySelector('#livewire-toast .toast-body').textContent = message;
    document.querySelector('#livewire-toast').className = `toast bg-${type}`;
    toast.show();
}



//firebase
const firebaseConfig = {
    apiKey: "AIzaSyAQmSPTFuocYjUuT-5tBr8ddn_lLEBc80M",
    authDomain: "dooshalahchat.firebaseapp.com",
    projectId: "dooshalahchat",
    storageBucket: "dooshalahchat.firebasestorage.app",
    messagingSenderId: "50335252342",
    appId: "1:50335252342:web:48a6b302dbf58aef7e4457"
};

// مقداردهی اولیه Firebase
const app = initializeApp(firebaseConfig);
const messaging = getMessaging(app);

window.requestNotificationPermission = async function() {
    const token = await getSanctumToken();
    if (!token) {
        console.error('Could not fetch Sanctum token');
        return;
    }

    Notification.requestPermission().then((permission) => {
        if (permission === 'granted') {
            console.log('Notification permission granted.');
            getToken(messaging, { vapidKey: 'BDKtRtHzS63nUM5JPlKfU6BfcdrVYItt5_6RpGGor216yhsNFz1hwQ0a8RJdxmoOMdAgkZXkrEFjXV4MbTIa1Ag' }).then((currentToken) => {
                if (currentToken) {
                    console.log('FCM Token:', currentToken);
                    fetch('/api/save-fcm-token', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'Authorization': `Bearer ${token}`,
                        },
                        body: JSON.stringify({ token: currentToken }),
                    }).then(response => response.json())
                      .then(data => console.log('FCM Token saved:', data))
                      .catch(error => console.error('Error saving FCM token:', error));
                } else {
                    console.log('No registration token available.');
                }
            }).catch((err) => {
                console.log('An error occurred while retrieving token.', err);
            });

            // دریافت نوتیfiکیشن‌ها در foreground
            onMessage(messaging, (payload) => {
                console.log('Message received:', payload);
                const notification = new Notification(payload.notification.title, {
                    body: payload.notification.body,
                });
            });
        } else {
            console.log('Notification permission denied.');
        }
    });
};// دریافت پیام‌های foreground (وقتی کاربر توی صفحه فعاله)



async function getSanctumToken() {
    try {
        const response = await fetch('/api/get-token', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
        });
        const data = await response.json();
        return data.token;
    } catch (error) {
        console.error('Error fetching Sanctum token:', error);
        return null;
    }
}



async function getStoredToken() {
    let token = localStorage.getItem('sanctum_token');
    if (!token) {
        token = await getSanctumToken();
    }
    return token;
}
