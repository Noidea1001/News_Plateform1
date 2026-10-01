/**
 * NewsPlatform Progressive Web App (PWA) Service Worker
 * news-platform / public / sw.js
 */

const CACHE_NAME = 'newsplatform-cache-v1';

const STATIC_ASSETS = [
    './',
    './index.php',
    './offline.html',
    './manifest.json',
    './assets/css/style.css',
    './assets/icons/icon-192.svg',
    './assets/icons/icon-512.svg',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css',
    'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js'
];

// 1. Install Event: Cache Core Assets
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            return cache.addAll(STATIC_ASSETS).catch(err => {
                console.warn('PWA: Some static assets failed to pre-cache:', err);
            });
        }).then(() => self.skipWaiting())
    );
});

// 2. Activate Event: Clean up outdated caches
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys => {
            return Promise.all(
                keys.map(key => {
                    if (key !== CACHE_NAME) {
                        return caches.delete(key);
                    }
                })
            );
        }).then(() => self.clients.claim())
    );
});

// 3. Fetch Event: Network-First for Navigation / HTML, Cache-First for static assets
self.addEventListener('fetch', event => {
    const request = event.request;

    // Only handle GET requests
    if (request.method !== 'GET') {
        return;
    }

    // Handle HTML page navigation (Network First with Offline Fallback)
    if (request.mode === 'navigate' || request.headers.get('accept')?.includes('text/html')) {
        event.respondWith(
            fetch(request)
                .then(networkResponse => {
                    // Update cache with the fresh page
                    const copy = networkResponse.clone();
                    caches.open(CACHE_NAME).then(cache => cache.put(request, copy));
                    return networkResponse;
                })
                .catch(async () => {
                    // Look up page in cache
                    const cachedResponse = await caches.match(request);
                    if (cachedResponse) {
                        return cachedResponse;
                    }
                    // If not found in cache, return offline fallback page
                    const fallback = await caches.match('./offline.html');
                    return fallback || caches.match('/News-platform-1/public/offline.html');
                })
        );
        return;
    }

    // Handle static resources (Cache First with Network Background Revalidation)
    event.respondWith(
        caches.match(request).then(cachedResponse => {
            if (cachedResponse) {
                // Fetch in background to update cache
                fetch(request).then(networkResponse => {
                    if (networkResponse && networkResponse.status === 200) {
                        caches.open(CACHE_NAME).then(cache => cache.put(request, networkResponse));
                    }
                }).catch(() => {/* ignore background update failures */});
                return cachedResponse;
            }

            return fetch(request).then(networkResponse => {
                if (networkResponse && networkResponse.status === 200) {
                    const copy = networkResponse.clone();
                    caches.open(CACHE_NAME).then(cache => cache.put(request, copy));
                }
                return networkResponse;
            }).catch(() => {
                // If offline and request is an image, could return an empty SVG placeholder
                return null;
            });
        })
    );
});
