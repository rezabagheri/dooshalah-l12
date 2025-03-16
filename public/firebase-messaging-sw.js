importScripts('https://www.gstatic.com/firebasejs/9.23.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/9.23.0/firebase-messaging-compat.js');

firebase.initializeApp({
    apiKey: "AIzaSyAQmSPTFuocYjUuT-5tBr8ddn_lLEBc80M",
    authDomain: "dooshalahchat.firebaseapp.com",
    projectId: "dooshalahchat",
    storageBucket: "dooshalahchat.firebasestorage.app",
    messagingSenderId: "50335252342",
    appId: "1:50335252342:web:48a6b302dbf58aef7e4457"
});

const messaging = firebase.messaging();

messaging.onBackgroundMessage((payload) => {
    console.log('[firebase-messaging-sw.js] Received background message:', payload);
    self.registration.showNotification(payload.notification.title, {
        body: payload.notification.body,
        icon: '/favicon.ico'
    });
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    event.waitUntil(clients.openWindow('/chat'));
});
