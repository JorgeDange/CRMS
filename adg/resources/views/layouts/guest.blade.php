<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Entrar || {{ config('app.name') }}</title>

    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/images/favicon.ico') }}" />

    <!-- Fonts (Inter — PROMPT_MASTER §3) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <!--! ================================================================ !-->
    <!--! [Start] Main Content !-- copiado de crm-prototipo/auth-login-cover.html !-->
    <!--! ================================================================ !-->
    <main class="auth-cover-wrapper">
        <div class="auth-cover-content-inner">
            <div class="auth-cover-content-wrapper">
                <div class="auth-img">
                    <img src="{{ asset('assets/images/auth/auth-cover-login-bg.svg') }}" alt="" class="img-fluid">
                </div>
            </div>
        </div>
        <div class="auth-cover-sidebar-inner">
            <div class="auth-cover-card-wrapper">
                <div class="auth-cover-card p-sm-5">
                    <div class="mb-5" style="max-width:230px">
                        <img src="{{ asset('images/logo-d.png') }}" alt="{{ config('app.name') }}" class="img-fluid">
                    </div>
                    <h2 class="fs-20 fw-bolder mb-4">Entrar</h2>
                    <h4 class="fs-13 fw-bold mb-2">Aceda à sua conta</h4>
                    <p class="fs-12 fw-medium text-muted">Bem-vindo de volta ao <strong>Lead Inteligente</strong> — gestão comercial B2B.</p>

                    {{ $slot }}

                    <div class="mt-5 text-muted text-center">
                        <span class="fs-12">Contacte o administrador para criar uma conta.</span>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <!--! ================================================================ !-->
    <!--! [End] Main Content !-->
    <!--! ================================================================ !-->
</body>

</html>
