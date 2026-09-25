import './bootstrap';
import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';
import { registerSW } from 'virtual:pwa-register';

window.Alpine = Alpine;
Alpine.plugin(focus);
Alpine.start();

const updateServiceWorker = registerSW({
    immediate: true,
    onOfflineReady() {
        window.dispatchEvent(new CustomEvent('pwa:offline-ready'));
    },
    onNeedRefresh() {
        window.dispatchEvent(new CustomEvent('pwa:update-ready', { detail: updateServiceWorker }));
    },
});

window.LaundryKu = {
    updateServiceWorker,
};
