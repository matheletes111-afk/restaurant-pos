<script src="{{asset('public/admin_template/js/plugins/popper.min.js')}}"></script>
<script src="{{asset('public/admin_template/js/plugins/simplebar.min.js')}}"></script>
<script src="{{asset('public/admin_template/js/plugins/bootstrap.min.js')}}"></script>
<script src="{{asset('public/admin_template/js/fonts/custom-font.js')}}"></script>
<script src="{{asset('public/admin_template/js/pcoded.js')}}"></script>
<script src="{{asset('public/admin_template/js/plugins/feather.min.js')}}"></script>
<script defer src="https://use.fontawesome.com/releases/v5.15.4/js/all.js"
        integrity="sha384-rOA1PnstxnOBLzCLMcre8ybwbTmemjzdNlILg8O7z1lUkLXozs4DHonlDtnE7fpc"
        crossorigin="anonymous"></script>
<script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-messaging-compat.js"></script>

<script>
const firebaseConfig = {
    apiKey: "AIzaSyDi4_VMx3ooJjJKZrPD46L59ChvI24kqkA",
    authDomain: "restaurant-app-72ecd.firebaseapp.com",
    projectId: "restaurant-app-72ecd",
    storageBucket: "restaurant-app-72ecd.firebasestorage.app",
    messagingSenderId: "283392196162",
    appId: "1:283392196162:web:9f5f41f34a641319bb77db"
};

firebase.initializeApp(firebaseConfig);
const messaging = firebase.messaging();

// ✅ Register Service Worker
navigator.serviceWorker.register('/restaurant/firebase-messaging-sw.js')
.then((registration) => {
    console.log('✅ Service Worker registered');

    // Foreground messages
    messaging.onMessage((payload) => {
        console.log('Foreground message:', payload);

        const title = payload.notification?.title || 'Notification';
        const body = payload.notification?.body || '';

        if (Notification.permission === 'granted') {
            new Notification(title, { body });
        }
    });
})
.catch(err => console.error('❌ SW registration error:', err));

// Button click to enable notifications
document.getElementById('enable-notifications-btn')?.addEventListener('click', async () => {
    const permission = await Notification.requestPermission();
    if (permission !== 'granted') {
        alert('Notification permission denied');
        return;
    }

    const token = await messaging.getToken({
        vapidKey: "{{ env('FIREBASE_VAPID_KEY') }}"
    });

    if (token) {
        console.log('✅ FCM Token:', token);

        await fetch("{{ route('save.fcm.token') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ fcm_token: token })
        });
    }
});
</script>



