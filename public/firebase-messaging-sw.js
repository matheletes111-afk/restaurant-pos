// Firebase Cloud Messaging Service Worker
importScripts('https://www.gstatic.com/firebasejs/9.23.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/9.23.0/firebase-messaging-compat.js');

const urlParams = new URLSearchParams(self.location.search);
const firebaseConfig = {
    apiKey: urlParams.get('apiKey') || 'AIzaSyDvvlzXbF8jsHIStQClZynU_N_bPShxtsM',
    authDomain: urlParams.get('authDomain') || 'bill-bite.firebaseapp.com',
    projectId: urlParams.get('projectId') || 'bill-bite',
    storageBucket: urlParams.get('storageBucket') || 'bill-bite.firebasestorage.app',
    messagingSenderId: urlParams.get('messagingSenderId') || '80573033264',
    appId: urlParams.get('appId') || '1:80573033264:web:450f5cbbab6a8a4e0a76f5',
};

if (firebaseConfig.apiKey && firebaseConfig.projectId) {
    try {
        if (!firebase.apps.length) {
            firebase.initializeApp(firebaseConfig);
        }
        const messaging = firebase.messaging();

        // Handle background push messages
        messaging.onBackgroundMessage(function(payload) {
            console.log('[firebase-messaging-sw.js] Received background message:', payload);
            const notificationTitle = (payload.notification && payload.notification.title) || (payload.data && payload.data.title) || 'Notification';
            const notificationOptions = {
                body: (payload.notification && payload.notification.body) || (payload.data && payload.data.body) || 'You have received an update.',
                icon: (payload.notification && payload.notification.icon) || '/logo.png',
                badge: '/logo.png',
                data: payload.data || {},
                requireInteraction: true,
                vibrate: [200, 100, 200],
                tag: 'push-notification-' + Date.now(),
            };

            return self.registration.showNotification(notificationTitle, notificationOptions);
        });
    } catch (e) {
        console.warn('Firebase Service Worker init error:', e);
    }
}

// Open or focus application tab on notification click
self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    const targetUrl = (event.notification.data && (event.notification.data.click_action || event.notification.data.target_url)) || '/';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function(windowClients) {
            for (let i = 0; i < windowClients.length; i++) {
                let client = windowClients[i];
                if (client.url.includes(targetUrl) && 'focus' in client) {
                    return client.focus();
                }
            }
            if (clients.openWindow) {
                return clients.openWindow(targetUrl);
            }
        })
    );
});
