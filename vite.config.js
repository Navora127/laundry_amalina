import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import { VitePWA } from 'vite-plugin-pwa';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
        VitePWA({
            registerType: 'autoUpdate',
            injectRegister: false,
            strategies: 'generateSW',
            manifest: {
                name: 'LaundryKu',
                short_name: 'LaundryKu',
                description: 'Kelola layanan laundry dan pantau pesanan dengan mudah.',
                lang: 'id',
                theme_color: '#16877f',
                background_color: '#f8fafc',
                display: 'standalone',
                orientation: 'portrait-primary',
                start_url: '/',
                scope: '/',
                icons: [
                    {
                        src: '/icons/pwa-192x192.png',
                        sizes: '192x192',
                        type: 'image/png',
                        purpose: 'any',
                    },
                    {
                        src: '/icons/pwa-512x512.png',
                        sizes: '512x512',
                        type: 'image/png',
                        purpose: 'any',
                    },
                    {
                        src: '/icons/pwa-maskable-512x512.png',
                        sizes: '512x512',
                        type: 'image/png',
                        purpose: 'maskable',
                    },
                ],
            },
            workbox: {
                additionalManifestEntries: [
                    { url: 'offline.html', revision: null },
                ],
                cleanupOutdatedCaches: true,
                clientsClaim: true,
                navigateFallback: '/offline.html',
                skipWaiting: true,
                globPatterns: ['**/*.{js,css,html,ico,png,svg,webp,woff,woff2}'],
                runtimeCaching: [
                    {
                        urlPattern: /\/katalog(?:\/|$)/,
                        handler: 'StaleWhileRevalidate',
                        options: {
                            cacheName: 'laundryku-public-catalog',
                            cacheableResponse: {
                                statuses: [0, 200],
                            },
                            expiration: {
                                maxEntries: 30,
                                maxAgeSeconds: 60 * 60 * 24,
                            },
                        },
                    },
                ],
            },
            devOptions: {
                enabled: false,
            },
        }),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
