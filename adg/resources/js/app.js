import './bootstrap';

/* Bootstrap 5.3 (bundle com Popper) — componentes data-bs-* (§4) */
import 'bootstrap';

/* ApexCharts — gráficos do Dashboard e Painel Executivo (prototype usa ApexCharts) */
import './apexcharts-init';

/* Sidebar — substitui o jQuery do prototipo (common-init.min.js / vendors.min.js)
   minimenu  = colapso do menu   (html.minimenu, localStorage igual ao prototipo)
   mobile    = menu em ecrãs pequenos (.nxl-navigation.mob-navigation-active)

   ATENÇÃO: o Alpine NÃO é iniciado aqui. O Livewire traz a sua própria instância
   (livewire.js) — importar/arrancar uma segunda causava "Detected multiple
   instances of Alpine running", directivos avaliados duas vezes e erros de
   expressão no drag & drop do kanban. Registamos o componente no evento
   `alpine:init` (disparado por Alpine.start() do Livewire, ANTES de percorrer o
   DOM), para que `x-data="sidebar"` resolva na instância única. */
document.addEventListener('alpine:init', () => {
    window.Alpine.data('sidebar', () => ({
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

    /* Modal de confirmacao global — substitui o window.confirm do browser.
       Uso: $store.confirmar.abrir({ titulo, mensagem, confirmar, cancelar })
                    .then(ok => ok && $wire.accao()) */
    Alpine.store('confirmar', {
        visivel: false,
        titulo: 'Confirmar',
        mensagem: '',
        rotuloConfirmar: 'Confirmar',
        rotuloCancelar: 'Cancelar',
        _resolve: null,

        abrir(opcoes = {}) {
            this.titulo = opcoes.titulo ?? 'Confirmar';
            this.mensagem = opcoes.mensagem ?? '';
            this.rotuloConfirmar = opcoes.confirmar ?? 'Confirmar';
            this.rotuloCancelar = opcoes.cancelar ?? 'Cancelar';
            this.visivel = true;
            document.body.classList.add('modal-open');

            return new Promise((resolve) => {
                this._resolve = resolve;
            });
        },

        responder(aceite) {
            if (!this.visivel) {
                return;
            }

            this.visivel = false;
            document.body.classList.remove('modal-open');

            const resolve = this._resolve;
            this._resolve = null;
            resolve(aceite);
        },
    });
});

/* Modal de detalhes da Agenda: o body recebe `modal-open` quando o modal abre
   (via dispatch `modal-actividade` no componente). Aqui garantimos que a classe
   nunca fica presa apos navegacao wire:navigate (o listener Alpine morre junto
   com o componente). */
['livewire:navigating', 'livewire:navigated'].forEach((evt) => {
    document.addEventListener(evt, () => document.body.classList.remove('modal-open'));
});
