import './bootstrap';

/* Bootstrap 5.3 (bundle com Popper) — componentes data-bs-* (§4) */
import 'bootstrap';

import Alpine from 'alpinejs';

/* Sidebar — substitui o jQuery do prototipo (common-init.min.js / vendors.min.js)
   minimenu  = colapso do menu   (html.minimenu, localStorage igual ao prototipo)
   mobile    = menu em ecrãs pequenos (.nxl-navigation.mob-navigation-active) */
Alpine.data('sidebar', () => ({
    mini: false,
    mobile: false,

    init() {
        this.mini = localStorage.getItem('nexel-classic-dashboard-menu-mini-theme') === 'menu-mini-theme';
        this.applyMini();
    },

    toggleMini() {
        this.mini = !this.mini;
        localStorage.setItem('nexel-classic-dashboard-menu-mini-theme', this.mini ? 'menu-mini-theme' : '');
        this.applyMini();
    },

    applyMini() {
        document.documentElement.classList.toggle('minimenu', this.mini);
    },

    toggleMobile() {
        this.mobile = !this.mobile;
        const nav = document.querySelector('.nxl-navigation');
        if (nav) {
            nav.classList.toggle('mob-navigation-active', this.mobile);
        }
    },
}));

window.Alpine = Alpine;

Alpine.start();
