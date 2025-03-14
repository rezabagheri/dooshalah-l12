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

export function initializeFirebase() {
    if ('Notification' in window && navigator.serviceWorker) {
        Notification.requestPermission().then(permission => {
            if (permission === 'granted') {
                getToken(messaging, {
                    vapidKey: 'BDKtRtHzS63nUM5JPlKfU6BfcdrVYItt5_6RpGGor216yhsNFz1hwQ0a8RJdxmoOMdAgkZXkrEFjXV4MbTIa1Ag'
                }).then((currentToken) => {
                    if (currentToken) {
                        fetch('/save-fcm-token', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({ fcm_token: currentToken })
                        }).then(response => response.json()).then(data => {
                            console.log('FCM Token saved:', data);
                        }).catch(err => {
                            console.error('Error saving FCM token:', err);
                        });
                    }
                }).catch((err) => {
                    console.error('Error getting FCM token:', err);
                });

                onMessage(messaging, (payload) => {
                    toastr.success(payload.notification.body);
                    Livewire.dispatch('messageReceived');
                });
            }
        }).catch(err => {
            console.error('Notification permission denied:', err);
        });
    }
}
