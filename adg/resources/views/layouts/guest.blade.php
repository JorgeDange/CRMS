<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Entrar || {{ config('app.name') }}</title>

    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/images/favicon.ico') }}" />

    {{-- PWA leve (fase.md PENDÊNCIA 1) — manifest também no login --}}
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}" />
    <meta name="theme-color" content="#3454d1" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-title" content="Lead Inteligente" />
    <link rel="apple-touch-icon" href="{{ asset('assets/images/pwa-180.png') }}" />

    <!-- Fonts: Plus Jakarta Sans (design do protótipo de login) + Inter (app) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    {{-- Design copiado de prototipo/ui/index.html — só frontend; auth continua no backend Livewire --}}
    <style>
        /* ==========================================================================
           DESIGN SYSTEM & CSS VARIABLES
           ========================================================================== */
        :root {
            --primary: #3454d1;
            --primary-hover: #2a46b8;
            --primary-active: #203797;
            --primary-light: #eef2fc;
            --primary-focus: rgba(52, 84, 209, 0.22);

            --showcase-bg: #3454d1;
            --showcase-geo-1: #4263e0;
            --showcase-geo-2: #2844b5;
            --showcase-geo-3: #1d338f;

            --text-title: #0f1c3f;
            --text-heading: #1e293b;
            --text-label: #334155;
            --text-body: #475569;
            --text-muted: #64748b;
            --text-placeholder: #94a3b8;
            --text-white: #ffffff;
            --text-white-sub: rgba(255, 255, 255, 0.9);
            --text-white-dim: rgba(255, 255, 255, 0.45);

            --bg-white: #ffffff;
            --bg-input: #ffffff;
            --border-input: #d8e1ec;
            --border-input-hover: #b4c4d9;

            --font-main: 'Plus Jakarta Sans', 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;

            --ease-out: cubic-bezier(0.16, 1, 0.3, 1);
            --duration-fast: 0.18s;
            --duration-normal: 0.28s;
        }

        /* Reset parcial — só a página de login (não toca no CSS global do app) */
        .auth-guest-page *,
        .auth-guest-page *::before,
        .auth-guest-page *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        .auth-guest-page {
            width: 100%;
            min-height: 100vh;
            min-height: 100dvh;
            font-family: var(--font-main);
            color: var(--text-heading);
            background-color: var(--bg-white);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            text-rendering: optimizeLegibility;
            overflow-x: hidden;
        }

        /* ==========================================================================
           SPLIT VIEWPORT CONTAINER
           ========================================================================== */
        .auth-guest-page .app-viewport {
            display: flex;
            width: 100%;
            min-height: 100vh;
            min-height: 100dvh;
            overflow-x: hidden;
        }

        /* ==========================================================================
           LEFT COLUMN: AUTHENTICATION
           ========================================================================== */
        .auth-guest-page .auth-section {
            flex: 0 0 41.5%;
            width: 41.5%;
            min-width: 420px;
            background-color: var(--bg-white);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 52px 48px 40px;
            position: relative;
            z-index: 10;
        }

        .auth-guest-page .auth-inner-box {
            width: 100%;
            max-width: 375px;
            display: flex;
            flex-direction: column;
            margin: 0;
        }

        /* Brand Logo (logo.png) */
        .auth-guest-page .brand-identity {
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 38px;
            text-decoration: none;
            cursor: pointer;
            width: 100%;
            text-align: center;
        }

        .auth-guest-page .brand-logo-img {
            max-width: 220px;
            width: 100%;
            height: auto;
            object-fit: contain;
            display: block;
            margin: 0 auto;
            transition: transform var(--duration-fast) var(--ease-out);
        }

        .auth-guest-page .brand-identity:hover .brand-logo-img {
            transform: scale(1.02);
        }

        /* Form Fields */
        .auth-guest-page .form-stack {
            display: flex;
            flex-direction: column;
            width: 100%;
        }

        .auth-guest-page .field-group {
            display: flex;
            flex-direction: column;
            margin-bottom: 22px;
        }

        .auth-guest-page .field-label {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-label);
            margin-bottom: 8px;
            letter-spacing: -0.1px;
        }

        .auth-guest-page .field-control-wrap {
            position: relative;
            width: 100%;
            display: flex;
            align-items: center;
        }

        .auth-guest-page .field-input {
            width: 100%;
            height: 48px;
            padding: 0 16px;
            font-family: var(--font-main);
            font-size: 14px;
            font-weight: 500;
            color: var(--text-heading);
            background-color: var(--bg-input);
            border: 1px solid var(--border-input);
            border-radius: 6px;
            outline: none;
            transition: border-color var(--duration-fast), box-shadow var(--duration-fast), background-color var(--duration-fast);
            letter-spacing: -0.15px;
        }

        .auth-guest-page .field-input:hover {
            border-color: var(--border-input-hover);
        }

        .auth-guest-page .field-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3.5px var(--primary-focus);
        }

        .auth-guest-page .field-input.is-invalid {
            border-color: #dc3545;
        }

        .auth-guest-page .field-input.is-invalid:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 3.5px rgba(220, 53, 69, 0.22);
        }

        .auth-guest-page .field-error {
            font-size: 12.5px;
            font-weight: 500;
            color: #dc3545;
            margin-top: 6px;
        }

        /* Custom Password Bullets */
        .auth-guest-page .field-input.input-password {
            font-size: 20px;
            letter-spacing: 3.5px;
            font-weight: 700;
            color: #334155;
        }

        .auth-guest-page .field-input.input-password:placeholder-shown {
            font-size: 14px;
            letter-spacing: normal;
            font-weight: 400;
        }

        .auth-guest-page .btn-toggle-pwd {
            position: absolute;
            right: 12px;
            background: none;
            border: none;
            padding: 4px;
            cursor: pointer;
            color: var(--text-placeholder);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color var(--duration-fast);
        }

        .auth-guest-page .btn-toggle-pwd:hover {
            color: var(--primary);
        }

        /* Primary Submit Button */
        .auth-guest-page .btn-sign-in {
            width: 100%;
            height: 48px;
            margin-top: 6px;
            background-color: var(--primary);
            color: var(--text-white);
            border: none;
            border-radius: 6px;
            font-family: var(--font-main);
            font-size: 15px;
            font-weight: 600;
            letter-spacing: 0.1px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 14px rgba(52, 84, 209, 0.28);
            transition: all var(--duration-normal) var(--ease-out);
        }

        .auth-guest-page .btn-sign-in:hover:not(:disabled) {
            background-color: var(--primary-hover);
            box-shadow: 0 6px 18px rgba(52, 84, 209, 0.38);
            transform: translateY(-1px);
        }

        .auth-guest-page .btn-sign-in:active:not(:disabled) {
            background-color: var(--primary-active);
            transform: translateY(0);
            box-shadow: 0 2px 8px rgba(52, 84, 209, 0.2);
        }

        .auth-guest-page .btn-sign-in:disabled {
            opacity: 0.75;
            cursor: wait;
        }

        /* Sub-Action Row (Lembrar de mim & Esqueceu a senha) */
        .auth-guest-page .subactions-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 22px;
            font-size: 13.5px;
        }

        .auth-guest-page .checkbox-item {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            user-select: none;
            color: #486581;
            font-weight: 500;
        }

        .auth-guest-page .checkbox-box {
            width: 18px;
            height: 18px;
            border: 1.5px solid #bcccdc;
            border-radius: 4px;
            background-color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all var(--duration-fast);
        }

        .auth-guest-page .checkbox-native {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        .auth-guest-page .checkbox-native:checked+.checkbox-box {
            background-color: #fff;
            border-color: #bcccdc;
        }

        .auth-guest-page .checkbox-icon {
            stroke: var(--primary);
            stroke-width: 2.4;
            stroke-linecap: round;
            stroke-linejoin: round;
            fill: none;
            opacity: 0;
            transform: scale(0.6);
            transition: all var(--duration-fast) var(--ease-out);
        }

        .auth-guest-page .checkbox-native:checked+.checkbox-box .checkbox-icon {
            opacity: 1;
            transform: scale(1);
        }

        .auth-guest-page .link-forgot {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            transition: color var(--duration-fast);
        }

        .auth-guest-page .link-forgot:hover {
            text-decoration: underline;
            color: var(--primary-hover);
        }

        /* Alert de status da sessão */
        .auth-guest-page .guest-status-alert {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13.5px;
            font-weight: 500;
            color: #047857;
            background-color: #ecfdf5;
            border: 1px solid #a7f3d0;
            border-radius: 6px;
            padding: 10px 14px;
            margin-bottom: 20px;
        }

        /* ==========================================================================
           RIGHT COLUMN: SHOWCASE HERO COM LOGO-S (AVATAR/1.PNG)
           ========================================================================== */
        .auth-guest-page .showcase-section {
            flex: 1 1 58.5%;
            width: 58.5%;
            background-color: var(--showcase-bg);
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 44px 36px 36px;
            overflow: hidden;
        }

        /* Abstract Geometric Background */
        .auth-guest-page .geo-backdrop {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1;
            overflow: hidden;
        }

        .auth-guest-page .geo-svg {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Showcase Container */
        .auth-guest-page .showcase-container {
            position: relative;
            z-index: 5;
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
            max-width: 800px;
        }

        /* Hero Centerpiece: logo-s */
        .auth-guest-page .hero-symbol-stage {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 100%;
            max-width: 480px;
            min-height: 290px;
            margin-bottom: 24px;
            perspective: 1200px;
        }

        .auth-guest-page .hero-symbol-card {
            position: relative;
            width: 280px;
            height: 230px;
            background: rgba(255, 255, 255, 0.96);
            border-radius: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            box-shadow:
                0 24px 50px rgba(7, 24, 88, 0.4),
                0 2px 6px rgba(255, 255, 255, 0.8) inset,
                0 -2px 6px rgba(0, 0, 0, 0.04) inset;
            border: 1px solid rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            transition: transform 0.35s var(--ease-out), box-shadow 0.35s var(--ease-out);
            z-index: 2;
        }

        .auth-guest-page .hero-symbol-card:hover {
            transform: translateY(-6px) scale(1.02);
            box-shadow:
                0 32px 60px rgba(7, 24, 88, 0.46),
                0 2px 6px rgba(255, 255, 255, 0.9) inset;
        }

        .auth-guest-page .hero-symbol-img {
            width: 175px;
            max-width: 85%;
            height: auto;
            object-fit: contain;
            filter: drop-shadow(0 6px 14px rgba(52, 84, 209, 0.24));
            transition: transform 0.35s var(--ease-out);
        }

        .auth-guest-page .hero-symbol-card:hover .hero-symbol-img {
            transform: scale(1.05);
        }

        .auth-guest-page .hero-symbol-shadow {
            position: absolute;
            bottom: -6px;
            width: 250px;
            height: 30px;
            background: radial-gradient(ellipse at center, rgba(7, 24, 88, 0.55) 0%, rgba(13, 36, 120, 0.2) 50%, transparent 75%);
            filter: blur(10px);
            z-index: 1;
            pointer-events: none;
        }

        /* Hero Text Content */
        .auth-guest-page .showcase-text-group {
            text-align: center;
            max-width: 550px;
            margin-top: 10px;
        }

        .auth-guest-page .showcase-tagline {
            display: inline-block;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.6px;
            color: rgba(255, 255, 255, 0.92);
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.22);
            padding: 4px 14px;
            border-radius: 20px;
            margin-bottom: 12px;
            backdrop-filter: blur(6px);
            transition: opacity var(--duration-fast);
        }

        .auth-guest-page .showcase-heading {
            color: var(--text-white);
            font-size: 24.5px;
            font-weight: 700;
            letter-spacing: -0.3px;
            margin-bottom: 10px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.12);
            transition: opacity var(--duration-fast);
        }

        .auth-guest-page .showcase-paragraph {
            color: var(--text-white-sub);
            font-size: 14.5px;
            line-height: 1.55;
            font-weight: 400;
            letter-spacing: -0.1px;
            transition: opacity var(--duration-fast);
        }

        /* Slider Dots Navigation */
        .auth-guest-page .slider-indicators {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 36px;
        }

        .auth-guest-page .slider-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background-color: var(--text-white-dim);
            cursor: pointer;
            transition: all var(--duration-normal) var(--ease-out);
            border: none;
            padding: 0;
            outline: none;
        }

        .auth-guest-page .slider-dot:hover {
            background-color: rgba(255, 255, 255, 0.75);
        }

        .auth-guest-page .slider-dot.active {
            background-color: var(--text-white);
            width: 8px;
            height: 8px;
            box-shadow: 0 0 6px rgba(255, 255, 255, 0.4);
        }

        /* ==========================================================================
           RESPONSIVE ADAPTATIONS (MOBILE & TABLET)
           ========================================================================== */
        @media (max-width: 960px) {
            .auth-guest-page .app-viewport {
                flex-direction: column;
                overflow-y: auto;
                -webkit-overflow-scrolling: touch;
            }

            .auth-guest-page .auth-section {
                flex: 0 0 auto;
                width: 100%;
                min-width: unset;
                min-height: 100vh;
                min-height: 100dvh;
                padding: max(32px, env(safe-area-inset-top)) max(24px, env(safe-area-inset-right)) max(32px, env(safe-area-inset-bottom)) max(24px, env(safe-area-inset-left));
                box-sizing: border-box;
            }

            .auth-guest-page .auth-inner-box {
                max-width: 400px;
                width: 100%;
            }

            .auth-guest-page .brand-identity {
                margin-bottom: 32px;
            }

            .auth-guest-page .brand-logo-img {
                max-width: clamp(170px, 48vw, 210px);
            }

            .auth-guest-page .field-group {
                margin-bottom: 20px;
            }

            .auth-guest-page .field-label {
                font-size: 14px;
                margin-bottom: 7px;
            }

            /* 16px no mobile elimina o auto-zoom do Safari no iOS */
            .auth-guest-page .field-input {
                font-size: 16px;
                height: 50px;
                border-radius: 8px;
            }

            .auth-guest-page .field-input.input-password {
                font-size: 22px;
                letter-spacing: 4px;
            }

            .auth-guest-page .field-input.input-password:placeholder-shown {
                font-size: 15px;
                letter-spacing: normal;
            }

            .auth-guest-page .btn-sign-in {
                height: 50px;
                font-size: 16px;
                border-radius: 8px;
                margin-top: 8px;
            }

            .auth-guest-page .btn-toggle-pwd {
                width: 44px;
                height: 44px;
                right: 4px;
            }

            .auth-guest-page .subactions-row {
                margin-top: 20px;
                font-size: 14px;
            }

            .auth-guest-page .checkbox-item {
                padding: 6px 0;
            }

            .auth-guest-page .link-forgot {
                padding: 6px 0;
            }

            /* Coluna do Showcase no Mobile */
            .auth-guest-page .showcase-section {
                flex: 0 0 auto;
                width: 100%;
                min-height: auto;
                padding: 60px max(24px, env(safe-area-inset-right)) max(52px, env(safe-area-inset-bottom)) max(24px, env(safe-area-inset-left));
                box-sizing: border-box;
            }

            .auth-guest-page .hero-symbol-stage {
                min-height: 220px;
                margin-bottom: 20px;
            }

            .auth-guest-page .hero-symbol-card {
                width: clamp(200px, 62vw, 260px);
                height: clamp(160px, 48vw, 210px);
                border-radius: 24px;
                padding: 20px;
            }

            .auth-guest-page .hero-symbol-img {
                width: clamp(130px, 40vw, 165px);
            }

            .auth-guest-page .hero-symbol-shadow {
                width: clamp(180px, 52vw, 230px);
            }

            .auth-guest-page .showcase-text-group {
                max-width: 480px;
                width: 100%;
                margin-top: 8px;
            }

            .auth-guest-page .showcase-tagline {
                font-size: 11.5px;
                padding: 4px 12px;
                margin-bottom: 10px;
            }

            .auth-guest-page .showcase-heading {
                font-size: clamp(20px, 5.5vw, 24px);
                line-height: 1.25;
                margin-bottom: 8px;
            }

            .auth-guest-page .showcase-paragraph {
                font-size: clamp(13.5px, 3.8vw, 14.5px);
                line-height: 1.55;
            }

            .auth-guest-page .slider-indicators {
                margin-top: 28px;
                gap: 12px;
            }

            .auth-guest-page .slider-dot {
                width: 8px;
                height: 8px;
                position: relative;
            }

            /* Expande área de toque nos dots para mobile */
            .auth-guest-page .slider-dot::before {
                content: '';
                position: absolute;
                inset: -10px;
            }

            .auth-guest-page .slider-dot.active {
                width: 10px;
                height: 10px;
            }
        }

        @media (max-width: 440px) {
            .auth-guest-page .auth-section {
                padding-left: 20px;
                padding-right: 20px;
            }

            .auth-guest-page .subactions-row {
                gap: 10px;
            }

            .auth-guest-page .checkbox-item {
                font-size: 13.5px;
            }

            .auth-guest-page .link-forgot {
                font-size: 13.5px;
            }
        }
    </style>
</head>

<body class="auth-guest-page">

    <div class="app-viewport">

        <!-- ====================================================================
             LEFT COLUMN: FORMULÁRIO DE ENTRADA
             ==================================================================== -->
        <main class="auth-section">
            <div class="auth-inner-box">

                <!-- Logo da Marca (logo.png) -->
                <a href="{{ route('login') }}" class="brand-identity" aria-label="{{ config('app.name') }}">
                    <img src="{{ asset('assets/images/logo.png') }}" alt="{{ config('app.name') }}" class="brand-logo-img">
                </a>

                {{ $slot }}

            </div>
        </main>

        <!-- ====================================================================
             RIGHT COLUMN: APRESENTAÇÃO HERO COM LOGO-S (avatar/1.png)
             ==================================================================== -->
        <section class="showcase-section" aria-label="Apresentação da Plataforma Lead Inteligente">

            <!-- Padrões Geométricos Vetoriais em Sintonia com #3454d1 -->
            <div class="geo-backdrop" aria-hidden="true">
                <svg class="geo-svg" viewBox="0 0 1000 900" fill="none" preserveAspectRatio="xMidYMid slice" xmlns="http://www.w3.org/2000/svg">
                    <!-- Círculo e Arco Superior Esquerdo -->
                    <circle cx="120" cy="80" r="260" fill="#4364e2" fill-opacity="0.55"/>

                    <!-- Elementos Angulares e Chevron Central -->
                    <path d="M380 -80L520 60L420 160L280 20Z" fill="#2844b5" fill-opacity="0.5"/>
                    <path d="M360 80L480 200L380 300L260 180Z" fill="#1e3592" fill-opacity="0.32"/>
                    <path d="M490 -40L630 100L550 180L410 40Z" fill="#4c6de8" fill-opacity="0.28"/>

                    <!-- Anéis e Setor Circular Direito -->
                    <circle cx="950" cy="220" r="300" stroke="#223b9f" stroke-width="70" stroke-opacity="0.65"/>
                    <rect x="740" y="320" width="340" height="130" transform="rotate(-15 740 320)" fill="#2a47bd" fill-opacity="0.4"/>
                    <circle cx="980" cy="780" r="320" fill="#2743b3" fill-opacity="0.45"/>
                </svg>
            </div>

            <div class="showcase-container">

                <!-- Símbolo Hero: logo-s (avatar/1.png) -->
                <div class="hero-symbol-stage">
                    <div class="hero-symbol-card">
                        <img src="{{ asset('assets/images/avatar/1.png') }}" alt="Símbolo Lead Inteligente" class="hero-symbol-img">
                    </div>
                    <div class="hero-symbol-shadow"></div>
                </div>

                <!-- Bloco de Texto Explicativo (Lead Inteligente) -->
                <div class="showcase-text-group">
                    <span class="showcase-tagline" id="sliderTagline">Da prospecção ao cliente — tudo num único sistema.</span>
                    <h2 class="showcase-heading" id="sliderTitle">Bem-vindo ao Lead Inteligente</h2>
                    <p class="showcase-paragraph" id="sliderDesc">
                        A plataforma que centraliza toda a sua gestão comercial num só lugar. Registe oportunidades, acompanhe o pipeline, e nunca perca um seguimento importante.
                    </p>
                </div>

                <!-- Pontos de Paginação do Carrossel -->
                <div class="slider-indicators" role="tablist" aria-label="Controles da Apresentação">
                    <button class="slider-dot active" role="tab" aria-selected="true" data-index="0" aria-label="Slide 1"></button>
                    <button class="slider-dot" role="tab" aria-selected="false" data-index="1" aria-label="Slide 2"></button>
                    <button class="slider-dot" role="tab" aria-selected="false" data-index="2" aria-label="Slide 3"></button>
                </div>

            </div>

        </section>

    </div>

    @livewireScripts

    <!-- Carrossel do Showcase (copiado do protótipo) -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const slides = [
                {
                    tagline: "Da prospecção ao cliente — tudo num único sistema.",
                    title: "Bem-vindo ao Lead Inteligente",
                    desc: "A plataforma que centraliza toda a sua gestão comercial num só lugar. Registe oportunidades, acompanhe o pipeline, e nunca perca um seguimento importante."
                },
                {
                    tagline: "Automação que trabalha por si.",
                    title: "Do primeiro contacto ao cliente final",
                    desc: "Organize os seus leads, qualifique oportunidades, acompanhe visitas e propostas. O sistema lembra-o automaticamente do que precisa da sua atenção, para que nenhum negócio fique esquecido."
                },
                {
                    tagline: "A sua gestão comercial, sempre consigo.",
                    title: "Decisões informadas, resultados consistentes",
                    desc: "Tenha visibilidade total sobre o seu pipeline, clientes e contratos. Acompanhe o desempenho em tempo real e aceda à plataforma de qualquer lugar do mundo."
                }
            ];

            const dots = document.querySelectorAll('.slider-dot');
            const taglineEl = document.getElementById('sliderTagline');
            const titleEl = document.getElementById('sliderTitle');
            const descEl = document.getElementById('sliderDesc');
            let currentSlide = 0;
            let slideInterval;

            function goToSlide(index) {
                currentSlide = index;
                dots.forEach((dot, idx) => {
                    dot.classList.toggle('active', idx === index);
                    dot.setAttribute('aria-selected', idx === index ? 'true' : 'false');
                });

                if (titleEl && descEl && taglineEl) {
                    taglineEl.style.opacity = '0';
                    titleEl.style.opacity = '0';
                    descEl.style.opacity = '0';

                    setTimeout(() => {
                        taglineEl.innerHTML = slides[index].tagline;
                        titleEl.innerHTML = slides[index].title;
                        descEl.innerHTML = slides[index].desc;
                        taglineEl.style.opacity = '1';
                        titleEl.style.opacity = '1';
                        descEl.style.opacity = '1';
                    }, 180);
                }
            }

            dots.forEach((dot, idx) => {
                dot.addEventListener('click', () => {
                    goToSlide(idx);
                    resetAutoSlide();
                });
            });

            function startAutoSlide() {
                slideInterval = setInterval(() => {
                    const next = (currentSlide + 1) % slides.length;
                    goToSlide(next);
                }, 5500);
            }

            function resetAutoSlide() {
                clearInterval(slideInterval);
                startAutoSlide();
            }

            startAutoSlide();

            // Suporte a gesto de deslizar (swipe) em dispositivos móveis
            const showcaseSection = document.querySelector('.showcase-section');
            let touchStartX = 0;
            let touchEndX = 0;

            if (showcaseSection) {
                showcaseSection.addEventListener('touchstart', (e) => {
                    touchStartX = e.changedTouches[0].screenX;
                }, { passive: true });

                showcaseSection.addEventListener('touchend', (e) => {
                    touchEndX = e.changedTouches[0].screenX;
                    const threshold = 45;
                    if (touchEndX < touchStartX - threshold) {
                        const next = (currentSlide + 1) % slides.length;
                        goToSlide(next);
                        resetAutoSlide();
                    } else if (touchEndX > touchStartX + threshold) {
                        const prev = (currentSlide - 1 + slides.length) % slides.length;
                        goToSlide(prev);
                        resetAutoSlide();
                    }
                }, { passive: true });
            }
        });
    </script>
</body>

</html>
