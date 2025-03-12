import { initializeApp } from 'firebase/app';
import { getMessaging, getToken, onMessage } from 'firebase/messaging';

const firebaseConfig = {
    apiKey: "AIzaSyAQmSPTFuocYjUuT-5tBr8ddn_lLEBc80M",
    authDomain: "dooshalahchat.firebaseapp.com",
    projectId: "dooshalahchat",
    storageBucket: "dooshalahchat.firebasestorage.app",
    messagingSenderId: "50335252342",
    appId: "1:50335252342:web:48a6b302dbf58aef7e4457"
};

const app = initializeApp(firebaseConfig);
const messaging = getMessaging(app);

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

export async function requestNotificationPermission() {
    try {
        const registration = await navigator.serviceWorker.register('/firebase-messaging-sw.js');
        console.log('Service Worker registered:', registration);

        const permission = await Notification.requestPermission();
        if (permission === 'granted') {
            console.log('Notification permission granted.');
            try {
                const currentToken = await getToken(messaging, {
                    vapidKey: 'BDKtRtHzS63nUM5JPlKfU6BfcdrVYItt5_6RpGGor216yhsNFz1hwQ0a8RJdxmoOMdAgkZXkrEFjXV4MbTIa1Ag',
                    serviceWorkerRegistration: registration
                });
                if (currentToken) {
                    console.log('FCM Token:', currentToken);
                    const token = await getSanctumToken();
                    if (token) {
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
                    }
                } else {
                    console.log('No registration token available.');
                }
            } catch (err) {
                console.error('Failed to get token:', err);
                console.error('Error name:', err.name);
                console.error('Error message:', err.message);
                console.error('Error code:', err.code);
            }

            onMessage(messaging, (payload) => {
                console.log('Message received:', payload);
                const notification = new Notification(payload.notification.title, {
                    body: payload.notification.body,
                });
            });
        } else {
            console.log('Notification permission denied.');
        }
    } catch (err) {
        console.error('Service Worker registration failed:', err);
    }
}
