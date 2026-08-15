/**
 * Service worker for the Portail Numérique du Ministère des Transports.
 *
 * Deliberately minimal and scoped: this is a data-driven admin app, so this
 * worker MUST NOT cache anything that can go stale in a harmful way (page
 * navigations, /admin/*, /admin/api/*, form submissions). It only speeds up
 * repeat loads of static assets (CSS/JS/icons/manifest) with a cache-first
 * strategy. Everything else passes straight through to the network.
 *
 * Registered from public/sw.js, so its default scope is /public/ (or
 * "<subdir>/public/" in a subdirectory deployment) — it never even sees
 * fetch events for page navigations or /admin/* routes, which is an
 * additional safety net on top of the path checks below.
 */

const CACHE_NAME = 'min-transport-static-v1';

// Same-origin GET requests are only cache-first when their path contains
// one of these segments (works regardless of any BASE_PATH subdirectory
// prefix, since we test with .includes() rather than a fixed leading path).
const CACHEABLE_SEGMENTS = ['/public/assets/', '/public/manifest.json', '/public/sw.js'];

function isCacheableStaticAsset(url) {
    if (url.origin !== self.location.origin) {
        return false;
    }
    return CACHEABLE_SEGMENTS.some(function (segment) {
        return url.pathname.includes(segment);
    });
}

self.addEventListener('install', function (event) {
    // Activate the new worker as soon as it finishes installing.
    self.skipWaiting();
});

self.addEventListener('activate', function (event) {
    event.waitUntil(
        caches.keys().then(function (cacheNames) {
            return Promise.all(
                cacheNames
                    .filter(function (name) {
                        return name !== CACHE_NAME;
                    })
                    .map(function (name) {
                        return caches.delete(name);
                    })
            );
        }).then(function () {
            return self.clients.claim();
        })
    );
});

self.addEventListener('fetch', function (event) {
    const request = event.request;

    // Never touch anything but GET (POST/PUT/DELETE form submissions and
    // AJAX writes must always go straight to the network, untouched).
    if (request.method !== 'GET') {
        return;
    }

    const url = new URL(request.url);

    if (!isCacheableStaticAsset(url)) {
        // Network-only pass-through: page navigations, /admin/*,
        // /admin/api/*, and anything else are never cached here.
        return;
    }

    // Cache-first for static assets under public/assets, plus the
    // manifest/service-worker files themselves.
    event.respondWith(
        caches.open(CACHE_NAME).then(function (cache) {
            return cache.match(request).then(function (cached) {
                if (cached) {
                    return cached;
                }
                return fetch(request).then(function (response) {
                    if (response && response.status === 200) {
                        cache.put(request, response.clone());
                    }
                    return response;
                }).catch(function () {
                    return cached;
                });
            });
        })
    );
});
