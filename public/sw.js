/**
 * NewsPlatform Progressive Web App (PWA) Service Worker
 * news-platform / public / sw.js
 */

const CACHE_NAME = 'newsplatform-v2';

// 1. Install Event: Cache Core Assets Safely
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME).then(async cache => {
            const scope = self.registration.scope;
            const coreAssets = [
                scope,
                new URL('index.php', scope).href,
                new URL('offline.html', scope).href,
                new URL('public/offline.html', scope).href,
                new URL('manifest.json', scope).href,
                new URL('assets/css/style.css', scope).href,
                new URL('public/assets/css/style.css', scope).href,
                new URL('assets/icons/icon-192.png', scope).href,
                new URL('public/assets/icons/icon-192.png', scope).href,
                new URL('assets/icons/icon-512.png', scope).href,
                new URL('public/assets/icons/icon-512.png', scope).href,
                'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css',
                'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css',
                'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js'
            ];

            // Cache items individually to ensure partial network failures don't abort SW installation
            for (const assetUrl of coreAssets) {
                try {
                    const req = new Request(assetUrl, { mode: assetUrl.startsWith('http') && !assetUrl.includes(location.hostname) ? 'cors' : 'same-origin' });
                    const res = await fetch(req);
                    if (res && res.status === 200) {
                        await cache.put(req, res);
                    }
                } catch (e) {
                    // Non-critical asset cache fail
                }
            }
        }).then(() => self.skipWaiting())
    );
});

// 2. Activate Event: Clean up outdated caches & claim clients
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
    const url = new URL(request.url);

    // Only handle GET requests and http/https schemes
    if (request.method !== 'GET' || !url.protocol.startsWith('http')) {
        return;
    }

    // Bypass caching for live search API, dynamic APIs, and auth requests
    if (url.pathname.includes('search_api.php') ||
        url.pathname.includes('/api/') ||
        url.pathname.includes('login.php') ||
        url.pathname.includes('logout.php') ||
        url.pathname.includes('register.php') ||
        url.pathname.includes('/actions/')) {
        return;
    }

    // Handle HTML page navigation (Network First with Offline Fallback)
    if (request.mode === 'navigate' || (request.headers.get('accept') && request.headers.get('accept').includes('text/html'))) {
        event.respondWith(
            fetch(request)
                .then(networkResponse => {
                    if (networkResponse && networkResponse.status === 200) {
                        const copy = networkResponse.clone();
                        caches.open(CACHE_NAME).then(cache => cache.put(request, copy));
                    }
                    return networkResponse;
                })
                .catch(async () => {
                    // Look up current page in cache
                    const cachedResponse = await caches.match(request);
                    if (cachedResponse) {
                        return cachedResponse;
                    }

                    // Look up offline fallback page
                    const scope = self.registration.scope;
                    const offlineMatches = [
                        await caches.match(new URL('offline.html', scope).href),
                        await caches.match(new URL('public/offline.html', scope).href),
                        await caches.match('./offline.html'),
                        await caches.match('/News-platform-1/offline.html'),
                        await caches.match('/News-platform-1/public/offline.html')
                    ];

                    for (const fallback of offlineMatches) {
                        if (fallback) return fallback;
                    }

                    // Fallback to inline HTML if offline.html was not pre-cached
                    return new Response(
                        `<!DOCTYPE html>
                        <html lang="en">
                        <head><meta charset="UTF-8"><title>Offline | NewsPlatform</title>
                        <style>body{font-family:sans-serif;text-align:center;padding:3rem 1rem;background:#f8fafc;color:#1e293b;}h1{color:#c8102e;}a,button{color:#c8102e;font-weight:bold;cursor:pointer;}</style>
                        </head>
                        <body>
                            <h1>Offline Mode</h1>
                            <p>You are currently offline. Please check your internet connection.</p>
                            <p><button onclick="window.location.reload();">Retry</button></p>
                        </body>
                        </html>`,
                        { headers: { 'Content-Type': 'text/html; charset=utf-8' } }
                    );
                })
        );
        return;
    }

    // Handle static resources: Cache First with Background Revalidation
    event.respondWith(
        caches.match(request).then(cachedResponse => {
            if (cachedResponse) {
                // Background revalidation
                fetch(request).then(networkResponse => {
                    if (networkResponse && networkResponse.status === 200) {
                        caches.open(CACHE_NAME).then(cache => cache.put(request, networkResponse.clone()));
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
                return null;
            });
        })
    );
});

// 4. Web Push API: Handle Real-Time Push Notifications from Server (Breaking News)
self.addEventListener('push', event => {
    let payload = {
        title: '🚨 Breaking News Alert | NewsPlatform',
        body: 'A new breaking story has just been published.',
        icon: './assets/icons/icon-192.png',
        badge: './assets/icons/icon-192.png',
        tag: 'breaking-news',
        data: {
            url: './index.php'
        }
    };

    if (event.data) {
        try {
            const json = event.data.json();
            payload = Object.assign(payload, json);
        } catch (e) {
            payload.body = event.data.text();
        }
    }

    // Language detection: Check navigator.language or fallback
    const navLang = (navigator.language || 'en').toLowerCase();
    const isKhmerDevice = navLang.startsWith('kh') || navLang.startsWith('km');

    let displayTitle = payload.title;
    let displayBody = payload.body;

    if (isKhmerDevice) {
        if (payload.title_kh) displayTitle = payload.title_kh;
        if (payload.body_kh) displayBody = payload.body_kh;
    } else {
        if (payload.title_en) displayTitle = payload.title_en;
        if (payload.body_en) displayBody = payload.body_en;
    }

    const scope = self.registration.scope;
    const iconUrl = payload.icon ? new URL(payload.icon, scope).href : new URL('assets/icons/icon-192.png', scope).href;
    const badgeUrl = payload.badge ? new URL(payload.badge, scope).href : new URL('assets/icons/icon-192.png', scope).href;

    const options = {
        body: displayBody,
        icon: iconUrl,
        badge: badgeUrl,
        tag: payload.tag || ('breaking-news-' + Date.now()),
        renotify: true,
        requireInteraction: true,
        vibrate: [300, 100, 400],
        data: payload.data || { url: './index.php' }
    };

    event.waitUntil(
        self.registration.showNotification(displayTitle, options)
    );
});

// 5. Notification Click Handler: Open Target Article or Focus Existing Window
self.addEventListener('notificationclick', event => {
    event.notification.close();

    const targetUrl = (event.notification.data && event.notification.data.url)
        ? event.notification.data.url
        : './index.php';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(clientList => {
            const absoluteTarget = new URL(targetUrl, self.registration.scope).href;
            for (const client of clientList) {
                if (client.url === absoluteTarget && 'focus' in client) {
                    return client.focus();
                }
            }
            if (clients.openWindow) {
                return clients.openWindow(absoluteTarget);
            }
        })
    );
});
