'use strict';

self.addEventListener('push', function(event) {
    var data = {};
    if (event.data) {
        try { data = event.data.json(); }
        catch (error) { data = { body: event.data.text() }; }
    }

    var options = {
        body: data.body || '',
        icon: data.icon || new URL('assets/img/logo-mark.png', self.registration.scope).href,
        badge: new URL('assets/img/favicon.png', self.registration.scope).href,
        data: { url: data.url || self.location.origin + '/' }
    };

    event.waitUntil(self.registration.showNotification(data.title || 'DryNutri', options));
});

self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    var destination = new URL(event.notification.data.url || self.location.origin + '/', self.location.origin);
    if (destination.protocol !== 'https:' && destination.origin !== self.location.origin) return;

    event.waitUntil(self.clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function(clients) {
        for (var i = 0; i < clients.length; i++) {
            if (clients[i].url === destination.href && 'focus' in clients[i]) return clients[i].focus();
        }
        return self.clients.openWindow(destination.href);
    }));
});
