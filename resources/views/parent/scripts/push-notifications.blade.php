{{-- Browser push notifications: registers the service worker (public/sw.js)
     and lets the parent opt in/out via the bell button in the topbar
     (parent/partials/topbar.blade.php). Sends alongside every notification
     that also emails (see app/Notifications/*.php) — this just gives an
     instant, no-need-to-check-email version of the same event. --}}
<script>
var PHLCI_VAPID_KEY = (function () {
  var meta = document.querySelector('meta[name="vapid-public-key"]');
  return meta ? meta.getAttribute('content') : '';
})();

function urlBase64ToUint8Array(base64String) {
  var padding = '='.repeat((4 - base64String.length % 4) % 4);
  var base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
  var rawData = window.atob(base64);
  var outputArray = new Uint8Array(rawData.length);
  for (var i = 0; i < rawData.length; ++i) {
    outputArray[i] = rawData.charCodeAt(i);
  }
  return outputArray;
}

function updatePushButtonState(subscribed) {
  var icon = document.getElementById('pushNotifyIcon');
  var btn = document.getElementById('pushNotifyBtn');
  if (!icon || !btn) return;
  icon.className = subscribed ? 'bi bi-bell-fill' : 'bi bi-bell';
  btn.title = subscribed ? 'Notifications enabled — click to turn off' : 'Enable notifications';
  btn.classList.toggle('text-primary', subscribed);
}

function togglePushSubscription() {
  if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
    showToast('danger', 'Push notifications are not supported in this browser.');
    return;
  }
  if (!PHLCI_VAPID_KEY) {
    showToast('danger', 'Push notifications are not configured yet.');
    return;
  }

  navigator.serviceWorker.ready.then(function (registration) {
    registration.pushManager.getSubscription().then(function (existing) {
      if (existing) {
        var endpoint = existing.endpoint;
        existing.unsubscribe().then(function () {
          return fetch('{{ route("push.unsubscribe") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': getCsrfToken(), 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ endpoint: endpoint }),
          });
        }).then(function () {
          updatePushButtonState(false);
          showToast('success', 'Notifications turned off for this browser.');
        });
        return;
      }

      Notification.requestPermission().then(function (permission) {
        if (permission !== 'granted') {
          showToast('danger', 'Notification permission was not granted.');
          return;
        }

        registration.pushManager.subscribe({
          userVisibleOnly: true,
          applicationServerKey: urlBase64ToUint8Array(PHLCI_VAPID_KEY),
        }).then(function (subscription) {
          return fetch('{{ route("push.subscribe") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': getCsrfToken(), 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(subscription),
          });
        }).then(function () {
          updatePushButtonState(true);
          showToast('success', 'Notifications enabled for this browser.');
        }).catch(function (err) {
          console.error('Push subscribe failed:', err);
          showToast('danger', 'Could not enable notifications. Please try again.');
        });
      });
    });
  });
}

document.addEventListener('DOMContentLoaded', function () {
  if (!('serviceWorker' in navigator) || !('PushManager' in window)) return;

  navigator.serviceWorker.register('/sw.js').then(function (registration) {
    return registration.pushManager.getSubscription();
  }).then(function (existing) {
    updatePushButtonState(!!existing);
  }).catch(function (err) {
    console.error('Service worker registration failed:', err);
  });
});
</script>
