self.addEventListener('push', function (event) {
    if (!(self.Notification && self.Notification.permission === 'granted')) {
        return;
    }

    const data = event.data?.json() ?? {};

    const title = data.title || 'Nueva Notificación';
    const options = {
        body: data.body,
        icon: data.icon || '/icon.png', // Replace with an actual valid icon link if needed
        data: {
            url: data.action_url || '/'
        }
    };

    if (data.image) {
        options.image = data.image; // Large image for popup style
    }

    event.waitUntil(
        self.registration.showNotification(title, options)
    );
});

self.addEventListener('notificationclick', function (event) {
    event.notification.close();
    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function (clientList) {
            if (event.notification.data && event.notification.data.url) {
                let url = event.notification.data.url;
                
                // If we already have a window open, focus it and navigate
                for (let i = 0; i < clientList.length; i++) {
                    let client = clientList[i];
                    if (client.url === url && 'focus' in client) {
                        return client.focus();
                    }
                }
                
                // Otherwise open a new window
                if (clients.openWindow) {
                    return clients.openWindow(url);
                }
            }
        })
    );
});
