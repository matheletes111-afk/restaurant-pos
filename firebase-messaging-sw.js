importScripts('https://www.gstatic.com/firebasejs/9.23.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/9.23.0/firebase-messaging-compat.js');

firebase.initializeApp({
    apiKey: "AIzaSyDi4_VMx3ooJjJKZrPD46L59ChvI24kqkA",
    projectId: "restaurant-app-72ecd",
    messagingSenderId: "283392196162",
    appId: "1:283392196162:web:9f5f41f34a641319bb77db"
});

const messaging = firebase.messaging();

messaging.onBackgroundMessage(function(payload) {
    console.log('[SW] Background message received', payload);

    const title = payload.notification?.title || 'New Notification';
    const body = payload.notification?.body || '';

    self.registration.showNotification(title, { body });
});
