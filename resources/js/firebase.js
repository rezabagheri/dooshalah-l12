import { initializeApp } from 'firebase/app';
import { getMessaging, getToken, onMessage } from 'firebase/messaging';

// تنظیمات فایربیس از پروژه‌ات
const firebaseConfig = {
    apiKey: "AIzaSyAQmSPTFuocYjUuT-5tBr8ddn_lLEBc80M",
    authDomain: "dooshalahchat.firebaseapp.com",
    projectId: "dooshalahchat",
    storageBucket: "dooshalahchat.firebasestorage.app",
    messagingSenderId: "50335252342",
    appId: "1:50335252342:web:48a6b302dbf58aef7e4457"
};

// راه‌اندازی فایربیس
const app = initializeApp(firebaseConfig);
const messaging = getMessaging(app);

/**
 * درخواست اجازه برای نوتیفیکیشن و گرفتن توکن FCM
 * @returns {Promise<string|null>} توکن FCM یا null در صورت خطا
 */
export async function requestNotificationPermission() {
    if (!('Notification' in window) || !navigator.serviceWorker) {
        console.error('Notifications or Service Worker not supported.');
        return null;
    }

    try {
        const permission = await Notification.requestPermission();
        if (permission === 'granted') {
            console.log('Notification permission granted.');
            const token = await getToken(messaging, {
                vapidKey: 'BDKtRtHzS63nUM5JPlKfU6BfcdrVYItt5_6RpGGor216yhsNFz1hwQ0a8RJdxmoOMdAgkZXkrEFjXV4MbTIa1Ag'
            });
            if (token) {
                const response = await fetch('/chat/save-fcm-token', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ fcm_token: token }),
                });
                const data = await response.json();
                console.log('FCM Token saved:', data);
                return token;
            }
        } else {
            console.log('Notification permission denied.');
        }
    } catch (error) {
        console.error('Error getting FCM token:', error);
    }
    return null;
}

/**
 * دریافت پیام‌های foreground از فایربیس
 * @param {Function} callback تابعی که پیام دریافت‌شده را مدیریت می‌کند
 */
export function onForegroundMessage(callback) {
    onMessage(messaging, (payload) => {
        console.log('Foreground message received:', payload);
        callback(payload);
    });
}
