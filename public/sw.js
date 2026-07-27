const CACHE_NAME = 'balagh-v2';
const STATIC_ASSETS = [
    '/',
    '/assets/css/app.css',
    '/assets/css/citizen.css',
    '/assets/js/app.js',
    '/assets/js/citizen.js',
    '/assets/js/i18n.js',
    '/manifest.json',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js'
];

/* ======== IndexedDB helpers ======== */
function openDB() {
    return new Promise(function(resolve, reject) {
        var req = indexedDB.open('balagh-offline', 1);
        req.onupgradeneeded = function(e) {
            var db = e.target.result;
            if (!db.objectStoreNames.contains('pending-reports')) {
                db.createObjectStore('pending-reports', { keyPath: 'id', autoIncrement: true });
            }
        };
        req.onsuccess = function(e) { resolve(e.target.result); };
        req.onerror = function(e) { reject(e.target.error); };
    });
}

function queueReport(data) {
    return openDB().then(function(db) {
        return new Promise(function(resolve, reject) {
            var tx = db.transaction('pending-reports', 'readwrite');
            tx.objectStore('pending-reports').add(Object.assign({}, data, { timestamp: Date.now() }));
            tx.oncomplete = function() { resolve(); };
            tx.onerror = function(e) { reject(e.target.error); };
        });
    });
}

function getPendingReports() {
    return openDB().then(function(db) {
        return new Promise(function(resolve, reject) {
            var tx = db.transaction('pending-reports', 'readonly');
            var req = tx.objectStore('pending-reports').getAll();
            req.onsuccess = function() { resolve(req.result); };
            req.onerror = function(e) { reject(e.target.error); };
        });
    });
}

function deletePendingReport(id) {
    return openDB().then(function(db) {
        return new Promise(function(resolve, reject) {
            var tx = db.transaction('pending-reports', 'readwrite');
            tx.objectStore('pending-reports').delete(id);
            tx.oncomplete = function() { resolve(); };
            tx.onerror = function(e) { reject(e.target.error); };
        });
    });
}

function getPendingCount() {
    return openDB().then(function(db) {
        return new Promise(function(resolve, reject) {
            var tx = db.transaction('pending-reports', 'readonly');
            var req = tx.objectStore('pending-reports').count();
            req.onsuccess = function() { resolve(req.result); };
            req.onerror = function(e) { reject(e.target.error); };
        });
    });
}

/* ======== Sync pending reports ======== */
function syncPendingReports() {
    return getPendingReports().then(function(reports) {
        var chain = Promise.resolve();
        reports.forEach(function(report) {
            chain = chain.then(function() {
                var formData = new FormData();
                var data = report.formData;
                for (var key in data) {
                    if (!data.hasOwnProperty(key)) continue;
                    if (key === 'photos' && Array.isArray(data[key])) {
                        // Photos stored as base64 — skip large ones, only send small ones
                        data[key].forEach(function(photo) {
                            if (photo.data && photo.data.length < 500000) {
                                // Convert base64 back to File-like Blob
                                var byteString = atob(photo.data.split(',')[1]);
                                var ab = new ArrayBuffer(byteString.length);
                                var ia = new Uint8Array(ab);
                                for (var i = 0; i < byteString.length; i++) ia[i] = byteString.charCodeAt(i);
                                var blob = new Blob([ab], { type: photo.type || 'image/jpeg' });
                                formData.append('photos[]', blob, photo.name || 'photo.jpg');
                            }
                        });
                    } else if (key === '_token') {
                        formData.append(key, data[key]);
                    } else {
                        formData.append(key, data[key]);
                    }
                }

                return fetch(report.url, {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                }).then(function(resp) {
                    return deletePendingReport(report.id);
                }).catch(function() {
                    // Stop syncing on first failure — will retry later
                    return Promise.reject('sync-paused');
                });
            });
        });
        return chain;
    }).then(function() {
        // Notify all clients that sync completed
        return self.clients.matchAll().then(function(clients) {
            clients.forEach(function(client) {
                client.postMessage({ type: 'SYNC_COMPLETE' });
            });
        });
    }).catch(function() {
        // Sync will be retried on next connectivity change
    });
}

/* ======== Install & Activate ======== */
self.addEventListener('install', function(event) {
    event.waitUntil(
        caches.open(CACHE_NAME).then(function(cache) {
            return cache.addAll(STATIC_ASSETS).catch(function() {
                // Cache what we can — don't block install on CDN failures
            });
        })
    );
    self.skipWaiting();
});

self.addEventListener('activate', function(event) {
    event.waitUntil(
        caches.keys().then(function(keys) {
            return Promise.all(keys.filter(function(k) { return k !== CACHE_NAME; }).map(function(k) { return caches.delete(k); }));
        })
    );
    self.clients.claim();
    // Attempt sync on activate
    self.registration.sync.register('sync-reports').catch(function() {});
});

/* ======== Fetch handler ======== */
self.addEventListener('fetch', function(event) {
    var req = event.request;

    // Skip non-GET for caching, but intercept POST for offline queue
    if (req.method === 'POST') {
        // Only intercept form submissions to report endpoints
        if (req.url.includes('/reports/store') || req.url.includes('/quick-report')) {
            event.respondWith(
                fetch(req.clone()).catch(function() {
                    // Network failed — queue for later sync
                    var cloned = req.clone();
                    return cloned.formData().then(function(fd) {
                        var data = {};
                        fd.forEach(function(value, key) {
                            if (value instanceof File && value.size < 500000) {
                                // Read small files as base64 for IndexedDB storage
                                var reader = new FileReader();
                                data[key] = { name: value.name, type: value.type, size: value.size, _file: value };
                            } else if (!(value instanceof File)) {
                                data[key] = value;
                            }
                        });

                        // Process files sequentially
                        var fileKeys = Object.keys(data).filter(function(k) { return data[k] && data[k]._file; });
                        var filePromises = fileKeys.map(function(k) {
                            return new Promise(function(resolve) {
                                var reader = new FileReader();
                                reader.onloadend = function() {
                                    data[k].data = reader.result;
                                    delete data[k]._file;
                                    resolve();
                                };
                                reader.onerror = function() { resolve(); };
                                reader.readAsDataURL(data[k]._file);
                            });
                        });

                        return Promise.all(filePromises).then(function() {
                            return queueReport({ url: req.url, method: 'POST', formData: data });
                        });
                    }).then(function() {
                        self.registration.sync.register('sync-reports');
                        return new Response(JSON.stringify({
                            success: true,
                            offline: true,
                            message: 'Signalement enregistré hors ligne. Il sera envoyé automatiquement.'
                        }), {
                            headers: { 'Content-Type': 'application/json' }
                        });
                    });
                })
            );
            return;
        }
    }

    // GET requests — cache-first with network update
    if (req.method !== 'GET') return;
    if (req.url.includes('/api/')) return;

    event.respondWith(
        caches.match(req).then(function(cached) {
            var fetched = fetch(req).then(function(response) {
                if (response && response.status === 200 && response.type === 'basic') {
                    var clone = response.clone();
                    caches.open(CACHE_NAME).then(function(cache) { cache.put(req, clone); });
                }
                return response;
            }).catch(function() { return cached; });
            return cached || fetched;
        })
    );
});

/* ======== Background Sync ======== */
self.addEventListener('sync', function(event) {
    if (event.tag === 'sync-reports') {
        event.waitUntil(syncPendingReports());
    }
});

/* ======== Push Notifications ======== */
self.addEventListener('push', function(event) {
    var data = event.data ? event.data.json() : { title: 'Balagh', body: 'Nouvelle notification' };
    var options = {
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

self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    if (event.action === 'dismiss') return;
    var url = event.notification.data || '/notifications';
    event.waitUntil(clients.matchAll({ type: 'window' }).then(function(list) {
        for (var i = 0; i < list.length; i++) {
            if (list[i].url.includes(self.location.origin) && 'focus' in list[i]) {
                list[i].navigate(url);
                return list[i].focus();
            }
        }
        return clients.openWindow(url);
    }));
});
