const CACHE_NAME = 'hades-shop-v2';
const STATIC_ASSETS = [
  '/css/shop.min.css',
  '/css/animation.min.css',
  '/js/cart-anim.js',
  '/favicon.ico',
  '/image/logo.webp',
  '/manifest.json',
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then((cache) => cache.addAll(STATIC_ASSETS))
      .then(() => self.skipWaiting())
  );
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys()
      .then((names) => Promise.all(
        names.filter((n) => n !== CACHE_NAME).map((n) => caches.delete(n))
      ))
      .then(() => self.clients.claim())
  );
});

function isStaticAsset(pathname) {
  return /\.(css|js|ico|webp|png|jpe?g|avif|gif|woff2?)$/i.test(pathname)
    || pathname.startsWith('/image/');
}

self.addEventListener('fetch', (event) => {
  if (event.request.method !== 'GET') {
    return;
  }

  const url = new URL(event.request.url);

  if (
    url.pathname.startsWith('/api')
    || url.pathname.startsWith('/admin')
    || url.pathname.startsWith('/payment')
    || url.pathname.startsWith('/checkout')
  ) {
    return;
  }

  const accept = event.request.headers.get('accept') || '';

  // HTML: luôn lấy mạng — tránh trang cũ / reload lạ
  if (event.request.mode === 'navigate' || accept.includes('text/html')) {
    event.respondWith(
      fetch(event.request).catch(() => caches.match('/css/shop.min.css'))
    );
    return;
  }

  if (!isStaticAsset(url.pathname)) {
    return;
  }

  // CSS/JS/ảnh: cache-first + cập nhật nền
  event.respondWith(
    caches.match(event.request).then((cached) => {
      const network = fetch(event.request).then((res) => {
        if (res.ok) {
          const copy = res.clone();
          caches.open(CACHE_NAME).then((cache) => cache.put(event.request, copy));
        }
        return res;
      });
      return cached || network;
    })
  );
});
