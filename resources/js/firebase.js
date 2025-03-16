import firebase from 'firebase/compat/app';
import 'firebase/compat/messaging';

const firebaseConfig = {
    apiKey: "AIzaSyAQmSPTFuocYjUuT-5tBr8ddn_lLEBc80M",
    authDomain: "dooshalahchat.firebaseapp.com",
    projectId: "dooshalahchat",
    storageBucket: "dooshalahchat.firebasestorage.app",
    messagingSenderId: "50335252342",
    appId: "1:50335252342:web:48a6b302dbf58aef7e4457"
};

if (!firebase.apps.length) {
    firebase.initializeApp(firebaseConfig);
}

const messaging = firebase.messaging();

export async function requestNotificationPermission() {
    if (!('Notification' in window) || !navigator.serviceWorker) {
        console.error('Notifications or Service Worker not supported.');
        return null;
    }

    try {
        const permission = await Notification.requestPermission();
        if (permission === 'granted') {
            console.log('Notification permission granted.');
            const token = await messaging.getToken({
                vapidKey: 'BDKtRtHzS63nUM5JPlKfU6BfcdrVYItt5_6RpGGor216yhsNFz1hwQ0a8RJdxmoOMdAgkZXkrEFjXV4MbTIa1Ag'
            });
            if (token) {
                const csrfTokenElement = document.querySelector('meta[name="csrf-token"]');
                if (!csrfTokenElement) {
                    console.error('CSRF token not found. Skipping token save.');
                    return token;
                }
                const csrfToken = csrfTokenElement.content;
                const response = await fetch('/chat/save-fcm-token', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ fcm_token: token }),
                });
                const data = await response.json();
                console.log('FCM Token saved:', data);
                return token;
            }
        } else {
            console.log('Notification permission denied.');
            return null;
        }
    } catch (error) {
        console.error('Error getting FCM token:', error);
        return null;
    }
}

export function onForegroundMessage(callback) {
    messaging.onMessage((payload) => {
        console.log('Foreground message received:', payload);
        callback(payload);
    });
}
