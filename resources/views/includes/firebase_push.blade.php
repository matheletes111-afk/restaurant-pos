@if(auth()->check())
<!-- Firebase App & Messaging SDKs -->
<script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-messaging-compat.js"></script>

<script>
(function() {
  var firebaseConfig = {
    apiKey: "{{ config('services.firebase.api_key') }}",
    authDomain: "{{ config('services.firebase.auth_domain') }}",
    projectId: "{{ config('services.firebase.project_id') }}",
    storageBucket: "{{ config('services.firebase.storage_bucket') }}",
    messagingSenderId: "{{ config('services.firebase.messaging_sender_id') }}",
    appId: "{{ config('services.firebase.app_id') }}"
  };

  var vapidKey = "{{ config('services.firebase.vapid_key') }}";
  var csrfToken = "{{ csrf_token() }}";
  var storeTokenUrl = "{{ route('device.token.store') }}";

  if (!('serviceWorker' in navigator) || !('Notification' in window)) {
    return;
  }
  if (!firebaseConfig.apiKey || !firebaseConfig.projectId) {
    return;
  }

  try {
    if (!firebase.apps.length) {
      firebase.initializeApp(firebaseConfig);
    }
    var messaging = firebase.messaging();

    var swQuery = "?apiKey=" + encodeURIComponent(firebaseConfig.apiKey) + 
                  "&projectId=" + encodeURIComponent(firebaseConfig.projectId) + 
                  "&messagingSenderId=" + encodeURIComponent(firebaseConfig.messagingSenderId) + 
                  "&appId=" + encodeURIComponent(firebaseConfig.appId);

    navigator.serviceWorker.register("{{ url('/firebase-messaging-sw.js') }}" + swQuery)
      .then(function(registration) {
        registration.update();
        
        function requestPermissionAndToken() {
          Notification.requestPermission().then(function(permission) {
            if (permission === 'granted') {
              messaging.getToken({ serviceWorkerRegistration: registration, vapidKey: vapidKey })
                .then(function(currentToken) {
                  if (currentToken) {
                    saveDeviceToken(currentToken);
                  }
                })
                .catch(function(err) {
                  console.error('[FCM] Token retrieval failed:', err);
                });
            }
          });
        }

        if (Notification.permission === 'granted') {
          requestPermissionAndToken();
        } else if (Notification.permission !== 'denied') {
          setTimeout(requestPermissionAndToken, 1500);
        }
      });

    // Foreground Push Listener (When the user has the browser tab active)
    messaging.onMessage(function(payload) {
      console.log('[FCM] Foreground push message received:', payload);
      var title = (payload.notification && payload.notification.title) || (payload.data && payload.data.title) || 'Notification';
      var body = (payload.notification && payload.notification.body) || (payload.data && payload.data.body) || '';
      var targetUrl = (payload.data && (payload.data.target_url || payload.data.click_action)) || '/';

      // Play audio chime
      try {
        var audio = new Audio('/sounds/notification.mp3');
        audio.play().catch(function(){});
      } catch(e) {}

      // Display native OS desktop banner
      if (Notification.permission === 'granted' && navigator.serviceWorker) {
        navigator.serviceWorker.ready.then(function(reg) {
          reg.showNotification(title, {
            body: body,
            icon: '/logo.png',
            badge: '/logo.png',
            data: { target_url: targetUrl },
            requireInteraction: true,
            vibrate: [200, 100, 200]
          });
        }).catch(function() {
          try {
            new Notification(title, { body: body, icon: '/logo.png' });
          } catch(e) {}
        });
      }

      if (typeof toastr !== 'undefined') {
        toastr.info(body, title, {
          closeButton: true,
          progressBar: true,
          timeOut: 8000,
          onclick: function() {
            if (targetUrl) window.location.href = targetUrl;
          }
        });
      }
    });

  } catch (e) {
    console.warn('[FCM Web Push Init Error]', e);
  }

  function saveDeviceToken(token) {
    fetch(storeTokenUrl, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json'
      },
      body: JSON.stringify({ token: token, device_type: 'web' })
    });
  }
})();
</script>
@endif
