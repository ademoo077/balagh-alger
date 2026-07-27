const CACHE_NAME = 'balagh-v1';
const STATIC_ASSETS = [
    '/',
    '/assets/css/app.css',
    '/assets/js/app.js',
    '/assets/js/i18n.js',
    '/manifest.json',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js'
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => cache.addAll(STATIC_ASSETS))
    );
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(keys.filter((k) => k !== CACHE_NAME).map((k) => caches.delete(k)))
        )
    );
    self.clients.claim();
});

self.addEventListener('fetch', (event) => {
    if (event.request.method !== 'GET') return;
    if (event.request.url.includes('/api/')) return;

    event.respondWith(
        caches.match(event.request).then((cached) => {
            const fetched = fetch(event.request).then((response) => {
                if (response && response.status === 200 && response.type === 'basic') {
                    const clone = response.clone();
                    caches.open(CACHE_NAME).then((cache) => cache.put(event.request, clone));
                }
                return response;
            }).catch(() => cached);
            return cached || fetched;
        })
    );
});

self.addEventListener('push', (event) => {
    const data = event.data ? event.data.json() : { title: 'Balagh', body: 'Nouvelle notification' };
    const options = {
        body: data.body,
        icon: '/assets/img/icon-192.png',
        badge: '/assets/img/icon-192.png',
        vibrate: [200, 100, 200],
        data: data.url || '/notifications',
        actions: [
            { action: 'open', title: 'Voir', icon: '/assets/img/icon-192.png' },
            { action: 'dismiss', title: 'Fermer', icon: '/assets/img/icon-192.png' }
        ]
    };
    event.waitUntil(self.registration.showNotification(data.title, options));
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    if (event.action === 'dismiss') return;
    const url = event.notification.data || '/notifications';
    event.waitUntil(clients.matchAll({ type: 'window' }).then((list) => {
        for (const client of list) {
            if (client.url.includes(self.location.origin) && 'focus' in client) {
                client.navigate(url);
                return client.focus();
            }
        }
        return clients.openWindow(url);
    }));
});
