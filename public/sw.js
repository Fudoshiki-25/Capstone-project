// PHLCI parent-portal service worker — handles incoming Web Push messages
// (see app/Notifications/*.php + resources/views/parent/scripts/push-notifications.blade.php)
// and taps on the resulting notification. Nothing else (no offline caching)
// is done here on purpose — this is push-only, not a full PWA.

self.addEventListener('push', function (event) {
  var payload = {};
  try {
    payload = event.data ? event.data.json() : {};
  } catch (e) {
    payload = { title: 'PHLCI', body: event.data ? event.data.text() : '' };
  }

  var title = payload.title || 'PHLCI';
  var options = {
    body: payload.body || '',
    icon: payload.icon || '/photo/logo.png',
    badge: payload.badge || '/photo/logo.png',
    tag: payload.tag || undefined,
    requireInteraction: !!payload.requireInteraction,
    data: payload.data || {},
  };

  event.waitUntil(self.registration.showNotification(title, options));
});

self.addEventListener('notificationclick', function (event) {
  event.notification.close();
  var url = (event.notification.data && event.notification.data.url) || '/parent';

  event.waitUntil(
    clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function (windowClients) {
      for (var i = 0; i < windowClients.length; i++) {
        var client = windowClients[i];
        if (client.url.indexOf(url) !== -1 && 'focus' in client) {
          return client.focus();
        }
      }
      if (clients.openWindow) {
        return clients.openWindow(url);
      }
    })
  );
});
