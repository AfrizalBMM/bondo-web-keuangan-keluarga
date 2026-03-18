self.addEventListener('push', function (e) {
    if (!(self.Notification && self.Notification.permission === 'granted')) {
        // Notifications aren't supported or permission not granted
        return;
    }

    if (e.data) {
        var msg = e.data.json();
        e.waitUntil(self.registration.showNotification(msg.title, {
            body: msg.body,
            icon: msg.icon || '/favicon.ico', // Adjust path later if needed
            actions: msg.actions || [],
            data: msg.data || {}
        }));
    }
});

self.addEventListener('notificationclick', function (e) {
    var notification = e.notification;
    notification.close();

    e.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function (clientList) {
            // Jika tab sudah terbuka, fokus ke tab tersebut
            for (var i = 0; i < clientList.length; i++) {
                var client = clientList[i];
                if (client.url === (notification.data.url || '/') && 'focus' in client) {
                    return client.focus();
                }
            }
            // Jika belum ada tab terbuka, buka tab baru
            if (clients.openWindow) {
                return clients.openWindow(notification.data.url || '/');
            }
        })
    );
});
