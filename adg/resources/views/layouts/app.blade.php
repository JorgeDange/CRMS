<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    @php
        $pageTitle = trim(strip_tags((string) ($title ?? ($header ?? 'Dashboard')))) ?: 'Dashboard';
    @endphp
    <title>{{ $pageTitle }} || {{ config('app.name') }}</title>

    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/images/favicon.ico') }}" />

    <!-- Fonts (Inter — PROMPT_MASTER §3) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Livewire explícito: garante Alpine/Livewire em TODAS as páginas
         (a auto-inject só injecta quando existe um componente Livewire) --}}
    @livewireStyles
</head>

<body x-data="sidebar">
    {{-- Loading overlay — mostra logo enquanto carrega --}}
    <div id="app-loading" class="app-loading" x-cloak>
        <div class="app-loading-inner">
            <img src="{{ asset('images/logo-d.png') }}" alt="{{ config('app.name') }}" class="app-loading-logo">
            <div class="app-loading-spinner"></div>
        </div>
    </div>

    <x-layout.sidebar />
    <x-layout.header />

    <!--! [Start] Main Content !-->
    <main class="nxl-container {{ $attributes->get('containerClass') }}">
        <div class="nxl-content {{ $attributes->get('contentClass') }}">
            <!-- [ page-header ] start -->
            @unless($attributes->get('hidePageHeader'))
                <x-ui.page-header :title="$pageTitle" :breadcrumb="$attributes->get('pageHeaderBreadcrumb')">
                    <x-slot name="right">{{ $pageHeaderRight ?? '' }}</x-slot>
                </x-ui.page-header>
                {{ $pageHeaderBelow ?? '' }}
            @endunless
            <!-- [ page-header ] end -->
            <!-- [ Main Content ] start -->
            <div class="main-content {{ $attributes->get('mainClass') }}">
                {{ $slot }}
            </div>
            <!-- [ Main Content ] end -->
        </div>
        <!-- [ Footer ] start -->
        @unless($attributes->get('hideFooter'))
            <x-ui.footer />
        @endunless
        <!-- [ Footer ] end -->
    </main>
    <!--! [End] Main Content !-->

    {{-- Modal de confirmacao global (store Alpine `confirmar`) — substitui o
         window.confirm do browser. Fora de </main> como no prototipo (nunca
         apanha o blur do contentor). --}}
    <div class="modal fade" id="modalConfirmar" tabindex="-1" role="dialog" aria-modal="true"
         aria-labelledby="modalConfirmarTitulo"
         :aria-hidden="$store.confirmar.visivel ? 'false' : 'true'"
         :class="{ 'show d-block': $store.confirmar.visivel }"
         @click.self="$store.confirmar.responder(false)"
         @keydown.escape.window="$store.confirmar.visivel && $store.confirmar.responder(false)">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalConfirmarTitulo" x-text="$store.confirmar.titulo"></h5>
                    <button type="button" class="btn-close" aria-label="Fechar"
                            @click="$store.confirmar.responder(false)"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0" x-text="$store.confirmar.mensagem"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-brand"
                            x-text="$store.confirmar.rotuloCancelar"
                            @click="$store.confirmar.responder(false)"></button>
                    <button type="button" class="btn btn-danger"
                            x-text="$store.confirmar.rotuloConfirmar"
                            @click="$store.confirmar.responder(true)"></button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade" :class="{ show: $store.confirmar.visivel }"
         :style="{ display: $store.confirmar.visivel ? 'block' : 'none', backdropFilter: 'blur(3px)', WebkitBackdropFilter: 'blur(3px)' }"
         @click="$store.confirmar.responder(false)"></div>

    @stack('scripts')
    @livewireScripts
</body>

</html>
