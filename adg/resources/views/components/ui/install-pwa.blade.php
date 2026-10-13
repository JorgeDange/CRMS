{{-- Barra de instalação PWA (mobile) — PENDÊNCIA 1, Opção B.
     O browser só mostra a sua banner de instalação de forma automática quando
     cumpre critérios de engajamento e mesmo assim é suprimível; por isso
     capturamos `beforeinstallprompt` e oferecemos um botão próprio.
     - Android / Chrome / Edge (desktop incluído): usa deferredPrompt.prompt().
     - iOS / Safari: `beforeinstallprompt` não existe → mostra instruções
       ("Partilhar → Adicionar ao Ecrã Inicial").
     Fica escondida se a app já estiver instalada ou se o utilizador fechar. --}}
<div id="pwa-install" class="pwa-install" hidden>
    <div class="pwa-install-card">
        <img src="{{ asset('assets/images/pwa-192.png') }}" alt="" class="pwa-install-icon" />
        <div class="pwa-install-body">
            <strong class="pwa-install-title">Instalar aplicação</strong>
            <span class="pwa-install-sub">Adicione o Lead Inteligente ao seu telemóvel.</span>
        </div>
        <div class="pwa-install-actions">
            <button type="button" id="pwa-install-go" class="btn btn-primary btn-sm">
                <i class="feather-download me-1"></i>Instalar
            </button>
            <button type="button" id="pwa-install-close" class="pwa-install-close" aria-label="Fechar aviso de instalação">&times;</button>
        </div>
    </div>
</div>

<style>
    .pwa-install { position: fixed; left: 0; right: 0; bottom: 0; z-index: 1090; padding: 10px; pointer-events: none; }
    .pwa-install[hidden] { display: none !important; }
    .pwa-install-card {
        pointer-events: auto; display: flex; align-items: center; gap: 12px;
        background: #fff; border: 1px solid #e5e7eb; border-radius: 14px;
        box-shadow: 0 10px 34px rgba(15, 23, 42, .20); padding: 10px 12px;
        max-width: 560px; margin: 0 auto;
    }
    .pwa-install-icon { width: 42px; height: 42px; border-radius: 10px; flex-shrink: 0; object-fit: contain; }
    .pwa-install-body { min-width: 0; flex: 1; display: flex; flex-direction: column; line-height: 1.25; }
    .pwa-install-title { font-size: 14px; color: #283c50; }
    .pwa-install-sub { font-size: 12px; color: #6b7280; }
    .pwa-install-actions { display: flex; align-items: center; gap: 6px; flex-shrink: 0; }
    .pwa-install-close { border: none; background: transparent; color: #6b7280; font-size: 20px; line-height: 1; padding: 4px 8px; border-radius: 8px; cursor: pointer; }
    .pwa-install-close:hover { background: #f3f4f6; color: #283c50; }
    @media (max-width: 480px) { .pwa-install-sub { display: none; } }
</style>

<script>
    (function () {
        var KEY_DISMISS = 'pwa-install-dismissed';
        var KEY_INSTALLED = 'pwa-install-installed';

        var banner = document.getElementById('pwa-install');
        if (!banner) return;

        var goBtn = document.getElementById('pwa-install-go');
        var closeBtn = document.getElementById('pwa-install-close');
        var deferred = null;
        var dismissed = false;
        var installed = false;

        try {
            dismissed = localStorage.getItem(KEY_DISMISS) === '1';
            installed = localStorage.getItem(KEY_INSTALLED) === '1';
        } catch (e) {}

        // Já instalada (standalone) → nunca mostrar
        var isStandalone = (window.matchMedia && window.matchMedia('(display-mode: standalone)').matches) || window.navigator.standalone === true;
        if (isStandalone) installed = true;

        function show() { if (!installed && !dismissed) banner.hidden = false; }
        function hide() { banner.hidden = true; }

        // --- Android / Chrome / Edge (e desktop): captura o prompt nativo ---
        window.addEventListener('beforeinstallprompt', function (e) {
            e.preventDefault();
            deferred = e;
            show();
        });

        // --- iOS / Safari: não existe beforeinstallprompt → mostrar já ---
        var isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
        if (isIOS) show();

        goBtn && goBtn.addEventListener('click', function () {
            if (deferred) {
                deferred.prompt();
                deferred.userChoice.then(function (choice) {
                    if (choice && choice.outcome === 'accepted') {
                        try { localStorage.setItem(KEY_INSTALLED, '1'); } catch (e) {}
                        installed = true;
                    }
                    deferred = null;
                    hide();
                });
            } else if (isIOS) {
                alert('Para instalar no iPhone/iPad:\n\n1. Toque no botão Partilhar (quadrado com seta)\n2. Toque em "Adicionar ao Ecrã Inicial"\n3. Confirme em "Adicionar"');
            } else {
                alert('A instalação ainda não está disponível neste browser. Tente novamente dentro de momentos.');
            }
        });

        closeBtn && closeBtn.addEventListener('click', function () {
            hide();
            dismissed = true;
            try { localStorage.setItem(KEY_DISMISS, '1'); } catch (e) {}
        });

        window.addEventListener('appinstalled', function () {
            installed = true;
            hide();
            try { localStorage.setItem(KEY_INSTALLED, '1'); } catch (e) {}
        });
    })();
</script>
