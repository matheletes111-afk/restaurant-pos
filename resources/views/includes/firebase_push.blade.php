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
  var storeTokenUrl = "/device-token/store";

  if (!('serviceWorker' in navigator) || !('Notification' in window)) {
    console.warn('[FCM] Browser does not support Service Workers or Notifications.');
    return;
  }
  if (!firebaseConfig.apiKey || !firebaseConfig.projectId) {
    console.warn('[FCM] Firebase configuration is missing in .env');
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

    var swUrl = "/firebase-messaging-sw.js" + swQuery;

    navigator.serviceWorker.register(swUrl)
      .then(function(registration) {
        registration.update();

        function obtainAndSaveToken() {
          messaging.getToken({ serviceWorkerRegistration: registration, vapidKey: vapidKey })
            .then(function(currentToken) {
              if (currentToken) {
                console.log('[FCM] Device token obtained:', currentToken.substring(0, 20) + '...');
                saveDeviceToken(currentToken);
              } else {
                console.warn('[FCM] No registration token available from FCM.');
              }
            })
            .catch(function(err) {
              console.error('[FCM] Token retrieval failed:', err);
            });
        }

        if (Notification.permission === 'granted') {
          // If already granted, fetch token directly without asking again
          obtainAndSaveToken();
        } else if (Notification.permission === 'default') {
          // Show subtle prompt banner to invite user to enable web push
          showPermissionBanner(function() {
            Notification.requestPermission().then(function(perm) {
              if (perm === 'granted') {
                hidePermissionBanner();
                obtainAndSaveToken();
                if (typeof toastr !== 'undefined') {
                  toastr.success('Web notifications enabled successfully!', 'Notifications');
                }
              } else if (perm === 'denied') {
                hidePermissionBanner();
              }
            });
          });
        }
      })
      .catch(function(err) {
        console.error('[FCM] ServiceWorker registration failed:', err);
      });

    function showPermissionBanner(onAllowClick) {
      if (document.getElementById('fcm-permission-prompt')) return;
      var banner = document.createElement('div');
      banner.id = 'fcm-permission-prompt';
      banner.style.cssText = 'position:fixed; bottom:24px; right:24px; z-index:999999; background:#0f172a; color:#fff; padding:16px 20px; border-radius:14px; box-shadow:0 12px 35px rgba(0,0,0,0.4); border:1px solid #ff5e14; max-width:380px; font-family:inherit; animation:slideInUp 0.3s ease;';
      banner.innerHTML = '<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:6px;">' +
        '<strong style="font-size:0.95rem; display:flex; align-items:center; gap:8px;"><span style="color:#ff5e14; font-size:1.1rem;">🔔</span> Enable Web Push Alerts</strong>' +
        '<button id="fcm-banner-close" style="background:none; border:none; color:#94a3b8; font-size:1.2rem; cursor:pointer; line-height:1;">&times;</button>' +
        '</div>' +
        '<p style="margin:0 0 12px; font-size:0.82rem; color:#94a3b8; line-height:1.4;">Allow browser notifications to receive instant audio & desktop alerts when customers call a waiter or place orders.</p>' +
        '<div style="display:flex; justify-content:flex-end; gap:10px;">' +
        '<button id="fcm-banner-dismiss" style="background:transparent; border:none; color:#94a3b8; font-size:0.82rem; cursor:pointer; padding:6px 12px;">Later</button>' +
        '<button id="fcm-banner-allow" style="background:#ff5e14; border:none; color:#fff; font-size:0.82rem; font-weight:600; padding:6px 16px; border-radius:8px; cursor:pointer;">Enable Notifications</button>' +
        '</div>';
      document.body.appendChild(banner);

      document.getElementById('fcm-banner-allow').onclick = onAllowClick;
      document.getElementById('fcm-banner-dismiss').onclick = hidePermissionBanner;
      document.getElementById('fcm-banner-close').onclick = hidePermissionBanner;
    }

    function hidePermissionBanner() {
      var el = document.getElementById('fcm-permission-prompt');
      if (el) el.remove();
    }

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
      credentials: 'same-origin',
      body: JSON.stringify({ token: token, device_type: 'web' })
    })
    .then(function(res) {
      return res.json();
    })
    .then(function(data) {
      console.log('[FCM] Token stored on server successfully:', data);
    })
    .catch(function(err) {
      console.error('[FCM] Store token error:', err);
    });
  }
})();
</script>
@endif
