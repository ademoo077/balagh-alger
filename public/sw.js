const CACHE_NAME = 'balagh-v5';
const STATIC_ASSETS = [
    '/',
    '/offline.html',
    '/assets/css/citizen.css',
    '/assets/js/citizen.js',
    '/assets/js/i18n.js',
    '/assets/img/icon-192.png',
    '/assets/img/icon-512.png',
    '/manifest.json',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js',
    'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js',
    'https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js',
    'https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css',
    'https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css',
    'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600;700;800&family=Noto+Sans+Arabic:wght@400;500;600;700;800&display=swap',
    'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css'
];
const PRECACHE_PAGES = [
    '/home', '/reports', '/reports/create', '/feed', '/my-profile',
    '/notifications', '/suivi', '/badges', '/leaderboard', '/citizen/map'
];
const API_CACHE = 'balagh-api-v1';
const TILE_CACHE = 'balagh-tiles-v1';
const API_STALE_REVALIDATE = [
    '/api/stats/live',
    '/api/my-badges',
    '/api/commune-ranking'
];
const MAX_RETRY_ATTEMPTS = 5;
const RETRY_DELAY_MS = 30000;

/* ======== IndexedDB helpers ======== */
function openDB() {
    return new Promise(function(resolve, reject) {
        var req = indexedDB.open('balagh-offline', 2);
        req.onupgradeneeded = function(e) {
            var db = e.target.result;
            if (!db.objectStoreNames.contains('pending-reports')) {
                db.createObjectStore('pending-reports', { keyPath: 'id', autoIncrement: true });
            }
            if (!db.objectStoreNames.contains('cached-reports')) {
                var store = db.createObjectStore('cached-reports', { keyPath: 'id' });
                store.createIndex('status', 'status', { unique: false });
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
            tx.objectStore('pending-reports').add(Object.assign({}, data, { timestamp: Date.now(), retries: 0 }));
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

function updatePendingReport(id, updates) {
    return openDB().then(function(db) {
        return new Promise(function(resolve, reject) {
            var tx = db.transaction('pending-reports', 'readwrite');
            var store = tx.objectStore('pending-reports');
            var getReq = store.get(id);
            getReq.onsuccess = function() {
                var data = getReq.result;
                if (data) {
                    Object.assign(data, updates);
                    store.put(data);
                }
                resolve();
            };
            getReq.onerror = function(e) { reject(e.target.error); };
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

function cacheReport(data) {
    return openDB().then(function(db) {
        return new Promise(function(resolve, reject) {
            var tx = db.transaction('cached-reports', 'readwrite');
            tx.objectStore('cached-reports').put(Object.assign({}, data, { cachedAt: Date.now() }));
            tx.oncomplete = function() { resolve(); };
            tx.onerror = function(e) { reject(e.target.error); };
        });
    });
}

function getCachedReports() {
    return openDB().then(function(db) {
        return new Promise(function(resolve, reject) {
            var tx = db.transaction('cached-reports', 'readonly');
            var req = tx.objectStore('cached-reports').getAll();
            req.onsuccess = function() { resolve(req.result); };
            req.onerror = function(e) { reject(e.target.error); };
        });
    });
}

function getCachedReport(id) {
    return openDB().then(function(db) {
        return new Promise(function(resolve, reject) {
            var tx = db.transaction('cached-reports', 'readonly');
            var req = tx.objectStore('cached-reports').get(Number(id));
            req.onsuccess = function() { resolve(req.result); };
            req.onerror = function(e) { reject(e.target.error); };
        });
    });
}

/* ======== Sync pending reports ======== */
function syncPendingReports() {
    return getPendingReports().then(function(reports) {
        var chain = Promise.resolve();
        var synced = [];
        reports.forEach(function(report) {
            chain = chain.then(function() {
                if (report.retries >= MAX_RETRY_ATTEMPTS) {
                    synced.push(report.id);
                    return Promise.resolve();
                }
                var formData = new FormData();
                var data = report.formData;
                for (var key in data) {
                    if (!data.hasOwnProperty(key)) continue;
                    if (key === 'photos' && Array.isArray(data[key])) {
                        data[key].forEach(function(photo) {
                            if (photo.data && photo.data.length < 500000) {
                                var byteString = atob(photo.data.split(',')[1]);
                                var ab = new ArrayBuffer(byteString.length);
                                var ia = new Uint8Array(ab);
                                for (var i = 0; i < byteString.length; i++) ia[i] = byteString.charCodeAt(i);
                                var blob = new Blob([ab], { type: photo.type || 'image/jpeg' });
                                formData.append('photos[]', blob, photo.name || 'photo.jpg');
                            }
                        });
                    } else {
                        formData.append(key, data[key]);
                    }
                }
                return fetch(report.url, {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                }).then(function(resp) {
                    if (resp.ok) {
                        synced.push(report.id);
                        return deletePendingReport(report.id);
                    }
                    return updatePendingReport(report.id, { retries: report.retries + 1 });
                }).catch(function() {
                    return updatePendingReport(report.id, { retries: report.retries + 1 });
                });
            });
        });
        return chain.then(function() {
            return self.clients.matchAll().then(function(clients) {
                clients.forEach(function(client) {
                    client.postMessage({ type: 'SYNC_COMPLETE', synced: synced.length, remaining: reports.length - synced.length });
                });
            });
        });
    }).catch(function() {});
}

/* ======== Stale-while-revalidate for API ======== */
function fromCacheOrFetch(req, cacheName) {
    var cName = cacheName || API_CACHE;
    return caches.match(req, { cacheName: cName }).then(function(cached) {
        var fetchPromise = fetch(req).then(function(response) {
            if (response && response.status === 200) {
                var clone = response.clone();
                caches.open(cName).then(function(cache) { cache.put(req, clone); });
            }
            return response;
        }).catch(function() { return cached; });
        return cached || fetchPromise;
    });
}

/* ======== Stale-while-revalidate with exponential backoff retry ======== */
var retryCounts = {};
function fromCacheOrFetchRetry(req, cacheName) {
    var cName = cacheName || API_CACHE;
    return caches.match(req, { cacheName: cName }).then(function(cached) {
        var url = req.url;
        if (!retryCounts[url]) retryCounts[url] = 0;
        var fetchPromise = fetch(req).then(function(response) {
            retryCounts[url] = 0;
            if (response && response.status === 200) {
                var clone = response.clone();
                caches.open(cName).then(function(cache) { cache.put(req, clone); });
            }
            return response;
        }).catch(function(err) {
            retryCounts[url]++;
            if (retryCounts[url] <= MAX_RETRY_ATTEMPTS) {
                var delay = Math.min(RETRY_DELAY_MS * Math.pow(2, retryCounts[url] - 1), 600000);
                setTimeout(function() {
                    fetch(req).then(function(response) {
                        if (response && response.status === 200) {
                            caches.open(cName).then(function(cache) { cache.put(req, response); });
                        }
                    }).catch(function() {});
                }, delay);
            }
            return cached;
        });
        return cached || fetchPromise;
    });
}

/* ======== Tile cache eviction (keep most recent 500 entries) ======== */
function evictTileCache() {
    caches.open(TILE_CACHE).then(function(cache) {
        cache.keys().then(function(keys) {
            if (keys.length > 500) {
                var toDelete = keys.slice(0, keys.length - 500);
                toDelete.forEach(function(req) { cache.delete(req); });
            }
        });
    });
}

/* ======== Install & Activate ======== */
self.addEventListener('install', function(event) {
    event.waitUntil(
        caches.open(CACHE_NAME).then(function(cache) {
            return cache.addAll(STATIC_ASSETS).then(function() {
                return Promise.allSettled(
                    PRECACHE_PAGES.map(function(url) {
                        return fetch(url).then(function(resp) {
                            if (resp.ok) return cache.put(url, resp);
                        }).catch(function() {});
                    })
                );
            });
        })
    );
    self.skipWaiting();
});

self.addEventListener('activate', function(event) {
    event.waitUntil(
        caches.keys().then(function(keys) {
            return Promise.all(keys.filter(function(k) {
                return k !== CACHE_NAME && k !== API_CACHE && k !== TILE_CACHE;
            }).map(function(k) { return caches.delete(k); }));
        }).then(function() {
            // Prefetch common pages in background
            return caches.open(CACHE_NAME).then(function(cache) {
                return Promise.allSettled(
                    PRECACHE_PAGES.map(function(url) {
                        return fetch(url).then(function(resp) {
                            if (resp.ok) return cache.put(url, resp);
                        }).catch(function() {});
                    })
                );
            });
        })
    );
    self.clients.claim();
    self.registration.sync.register('sync-reports').catch(function() {});
    // Periodic cache refresh every 30 min
    setInterval(function() {
        caches.open(CACHE_NAME).then(function(cache) {
            PRECACHE_PAGES.forEach(function(url) {
                fetch(url).then(function(resp) {
                    if (resp.ok) cache.put(url, resp);
                }).catch(function() {});
            });
        });
        caches.open(API_CACHE).then(function(cache) {
            API_STALE_REVALIDATE.forEach(function(url) {
                fetch(url).then(function(resp) {
                    if (resp.ok) cache.put(url, resp);
                }).catch(function() {});
            });
        });
        evictTileCache();
    }, 30 * 60 * 1000);
});

/* ======== Fetch handler ======== */
self.addEventListener('fetch', function(event) {
    var req = event.request;

    // POST: offline queue for reports
    if (req.method === 'POST') {
        if (req.url.includes('/reports/store') || req.url.includes('/quick-report') ||
            req.url.includes('/reports/') && (req.url.includes('/update') || req.url.includes('/comment'))) {
            event.respondWith(
                fetch(req.clone()).then(function(response) {
                    // On success, try to refresh cached data
                    if (response.ok && req.url.includes('/reports/store') || req.url.includes('/quick-report')) {
                        caches.open(CACHE_NAME).then(function(cache) {
                            PRECACHE_PAGES.forEach(function(p) {
                                if (p === '/reports' || p === '/my-profile') {
                                    fetch(p).then(function(r) {
                                        if (r.ok) cache.put(p, r);
                                    }).catch(function() {});
                                }
                            });
                        });
                    }
                    return response;
                }).catch(function() {
                    var cloned = req.clone();
                    return cloned.formData().then(function(fd) {
                        var data = {};
                        fd.forEach(function(value, key) {
                            if (value instanceof File && value.size < 500000) {
                                data[key] = { name: value.name, type: value.type, size: value.size, _file: value };
                            } else if (!(value instanceof File)) {
                                data[key] = value;
                            }
                        });
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
                            message: 'Signalement enregistré hors ligne. Il sera envoyé automatiquement dès que la connexion sera rétablie.'
                        }), {
                            headers: { 'Content-Type': 'application/json' }
                        });
                    });
                })
            );
            return;
        }
    }

    if (req.method !== 'GET') return;

    /* ---- API stale-while-revalidate with retry backoff ---- */
    var isStaleApi = API_STALE_REVALIDATE.some(function(prefix) { return req.url.includes(prefix); });
    if (isStaleApi) {
        event.respondWith(fromCacheOrFetchRetry(req));
        return;
    }

    /* ---- Map tiles: cache-first with large limit ---- */
    if (req.url.includes('tile.openstreetmap') || req.url.includes('basemaps.cartocdn') || req.url.includes('tile.osm.org')) {
        event.respondWith(
            caches.match(req, { cacheName: TILE_CACHE }).then(function(cached) {
                var fetchPromise = fetch(req).then(function(response) {
                    if (response && response.status === 200) {
                        caches.open(TILE_CACHE).then(function(cache) { cache.put(req, response.clone()); });
                    }
                    return response;
                }).catch(function() { return cached; });
                return cached || fetchPromise;
            })
        );
        return;
    }

    /* ---- Map data: cache-first with stale & retry ---- */
    if (req.url.includes('/api/map') || req.url.includes('/api/reports') && req.url.endsWith('/reports')) {
        event.respondWith(fromCacheOrFetch(req));
        return;
    }

    /* ---- HTML navigation ---- */
    if (req.mode === 'navigate' || req.headers.get('accept')?.includes('text/html')) {
        event.respondWith(
            fetch(req).then(function(response) {
                if (response && response.status === 200) {
                    var clone = response.clone();
                    caches.open(CACHE_NAME).then(function(cache) { cache.put(req, clone); });
                }
                return response;
            }).catch(function() {
                return caches.match(req).then(function(cached) {
                    return cached || caches.match('/offline.html');
                });
            })
        );
        return;
    }

    /* ---- Report detail pages: cache on first visit ---- */
    if (/\/reports\/\d+$/.test(new URL(req.url).pathname)) {
        event.respondWith(
            fetch(req).then(function(response) {
                if (response && response.status === 200) {
                    var clone = response.clone();
                    caches.open(CACHE_NAME).then(function(cache) { cache.put(req, clone); });
                    response.clone().json ? response.clone().json().then(function(data) {
                        if (data && data.id) cacheReport(data);
                    }).catch(function() {}) : null;
                }
                return response;
            }).catch(function() {
                return caches.match(req).then(function(cached) {
                    return cached || caches.match('/offline.html');
                });
            })
        );
        return;
    }

    /* ---- Static assets ---- */
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

/* ======== Message handler ======== */
self.addEventListener('message', function(event) {
    if (!event.data) return;
    switch (event.data.type) {
        case 'GET_PENDING_COUNT':
            getPendingCount().then(function(count) {
                event.source.postMessage({ type: 'PENDING_COUNT', count: count });
            });
            break;
        case 'SYNC_NOW':
            event.waitUntil(syncPendingReports());
            break;
        case 'GET_CACHED_REPORTS':
            getCachedReports().then(function(reports) {
                event.source.postMessage({ type: 'CACHED_REPORTS', reports: reports });
            });
            break;
        case 'GET_CACHED_REPORT':
            getCachedReport(event.data.id).then(function(report) {
                event.source.postMessage({ type: 'CACHED_REPORT', report: report });
            });
            break;
    }
});
