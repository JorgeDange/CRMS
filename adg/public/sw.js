/**
 * Service Worker — PWA leve (fase.md PENDÊNCIA 1, Opção B).
 *
 * Estratégia: cache-FIRST apenas para ASSETS ESTÁTICOS (CSS/JS/imagens/fonts).
 * NUNCA intercepta navegação HTML nem pedidos POST (Livewire) — evita
 * conflitos com as actualizações em vivo do framework.
 */

const CACHE = 'adg-static-v1';

self.addEventListener('install', (event) => {
    // Activa de imediato sem esperar que as páginas abertas fechem o SW antigo
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    // Remove caches de versões anteriores
    event.waitUntil(
        caches.keys()
            .then((keys) => Promise.all(
                keys.filter((key) => key !== CACHE).map((key) => caches.delete(key))
            ))
            .then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', (event) => {
    const req = event.request;

    // Só GET — nunca interceptar POST/PUT/DELETE (Livewire usa POST)
    if (req.method !== 'GET') return;

    const url = new URL(req.url);

    // Só o nosso origin (nunca CDN/fonts externos)
    if (url.origin !== self.location.origin) return;

    // Assets estáticos: cache primeiro, revalida em background
    const isAsset =
        url.pathname.startsWith('/build/') ||       // Vite (hashed → cache seguro)
        url.pathname.startsWith('/assets/') ||
        url.pathname.startsWith('/images/') ||
        /\.(css|js|png|jpg|jpeg|svg|webp|avif|woff|woff2|ttf|ico|webmanifest)$/i.test(url.pathname);

    // HTML/navegação/dados: sempre rede (páginas Livewire nunca são cacheadas)
    if (!isAsset) return;

    event.respondWith(
        caches.open(CACHE).then((cache) => cache.match(req).then((cached) => {
            // Dispara o fetch já (revalidação em background se houver cache)
            const network = fetch(req)
                .then((res) => {
                    if (res && res.status === 200 && res.type === 'basic') {
                        cache.put(req, res.clone());
                    }
                    return res;
                })
                .catch(() => cached); // offline: usa cache se existir

            return cached || network;
        }))
    );
});
