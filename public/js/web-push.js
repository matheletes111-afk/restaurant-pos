// Firebase configuration
const firebaseConfig = {
    apiKey: "AIzaSyDi4_VMx3ooJjJKZrPD46L59ChvI24kqkA",
    authDomain: "restaurant-app-72ecd.firebasestorage.app",
    projectId: "restaurant-app-72ecd",
    storageBucket: "restaurant-app-72ecd.firebasestorage.app",
    messagingSenderId: "283392196162",
    appId: "1:283392196162:web:9f5f41f34a641319bb77db",
    measurementId: "G-EV93P94RSY"
};

const vapidKey = "BCc2ERIHyDK8MK_C3WC-j3xb3DMUWSdV9ckhe8fratNY_2OXwqkePnJcaOo3A42-qTjGbuhFOslFINTrsW8AnRw";

// Simple Firebase Web Push
class SimpleWebPush {
    static csrfToken = null;
    static registerUrl = null;

    // Initialize
    static initialize(routes = {}) {
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        this.registerUrl = routes.registerTokenUrl;
        console.log('SimpleWebPush initialized with URL:', this.registerUrl);
    }

    // Request permission and get token
    static async requestPermissionAndGetToken() {
        try {
            // Check browser support
            if (!('Notification' in window)) {
                alert('Your browser does not support notifications');
                return false;
            }

            // Check if already granted
            if (Notification.permission === 'granted') {
                console.log('Permission already granted');
                return await this.getAndRegisterToken();
            }

            // Request permission
            const permission = await Notification.requestPermission();
            console.log('Permission result:', permission);

            if (permission === 'granted') {
                alert('✅ Notifications enabled!');
                return await this.getAndRegisterToken();
            } else {
                alert('Notifications not enabled');
                return false;
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error: ' + error.message);
            return false;
        }
    }

    // Get and register token
    static async getAndRegisterToken() {
        try {
            // Initialize Firebase
            if (!firebase.apps.length) {
                firebase.initializeApp(firebaseConfig);
                console.log('Firebase initialized');
            }

            // Check if messaging is supported
            if (!firebase.messaging.isSupported()) {
                alert('Firebase messaging not supported');
                return false;
            }

            const messaging = firebase.messaging();
            
            // Try to get token
            let token = null;
            try {
                token = await messaging.getToken({ vapidKey: vapidKey });
                console.log('Got token without service worker');
            } catch (error1) {
                console.log('First attempt failed:', error1.message);
                
                // Try with service worker
                try {
                    if ('serviceWorker' in navigator) {
                        const registration = await navigator.serviceWorker.register('/firebase-messaging-sw.js');
                        console.log('Service worker registered');
                        
                        token = await messaging.getToken({ 
                            vapidKey: vapidKey,
                            serviceWorkerRegistration: registration
                        });
                        console.log('Got token with service worker');
                    }
                } catch (error2) {
                    console.error('Service worker error:', error2);
                }
            }

            if (!token) {
                console.error('Could not get FCM token');
                alert('Could not get notification token');
                return false;
            }

            console.log('FCM Token:', token.substring(0, 30) + '...');
            
            // Register token with server
            const registered = await this.registerToken(token);
            if (registered) {
                // Listen for messages
                messaging.onMessage((payload) => {
                    console.log('Message received:', payload);
                    this.showNotification(
                        payload.notification?.title || 'New Order',
                        payload.notification?.body || 'You have a new order',
                        payload.data
                    );
                });
                return true;
            }
            return false;

        } catch (error) {
            console.error('Error getting token:', error);
            alert('Error: ' + error.message);
            return false;
        }
    }

    // Register token with Laravel backend
    static async registerToken(token) {
        try {
            if (!this.csrfToken || !this.registerUrl) {
                console.error('Missing CSRF token or register URL');
                return false;
            }

            const response = await fetch(this.registerUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    token: token,
                    device_type: 'web'
                })
            });

            const data = await response.json();
            console.log('Server response:', data);

            if (data.success) {
                console.log('✅ Token registered successfully');
                return true;
            } else {
                console.error('Server error:', data.message);
                return false;
            }
        } catch (error) {
            console.error('Error registering token:', error);
            return false;
        }
    }

    // Show notification
    static showNotification(title, body, data = {}) {
        if (Notification.permission === 'granted') {
            const options = {
                body: body,
                icon: '/images/logo.png',
                badge: '/images/badge.png',
                data: data
            };

            const notification = new Notification(title, options);

            notification.onclick = () => {
                notification.close();
                if (data.order_id) {
                    window.open(`/orders/${data.order_id}`, '_blank');
                }
            };
        }
    }

    // Debug function
    static async debug() {
        console.log('=== DEBUG INFO ===');
        console.log('Notification support:', 'Notification' in window);
        console.log('Current permission:', Notification.permission);
        console.log('Firebase loaded:', typeof firebase !== 'undefined');
        console.log('Firebase messaging:', firebase.messaging?.isSupported());
        console.log('CSRF Token:', this.csrfToken ? 'Exists' : 'Missing');
        console.log('Register URL:', this.registerUrl);
        
        if (typeof firebase !== 'undefined' && firebase.messaging) {
            try {
                const messaging = firebase.messaging();
                const token = await messaging.getToken({ vapidKey: vapidKey });
                console.log('FCM Token:', token ? token.substring(0, 30) + '...' : 'None');
            } catch (error) {
                console.log('Error getting token:', error.message);
            }
        }
        console.log('==================');
    }
}

// Make available globally
window.SimpleWebPush = SimpleWebPush;