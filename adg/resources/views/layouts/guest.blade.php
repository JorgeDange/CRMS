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
    @livewireStyles
</head>

<body>
    <!--! ================================================================ !-->
    <!--! [Start] Main Content - Login Centralizado !-->
    <!--! ================================================================ !-->
    <main class="auth-cover-wrapper">
        <div class="d-flex align-items-center justify-content-center min-vh-100 py-5">
            <div class="auth-cover-card-wrapper w-100" style="max-width: 420px;">
                <div class="auth-cover-card p-sm-5 p-4">
                    <div class="text-center mb-5">
                        <img src="{{ asset('images/logo-d.png') }}" alt="{{ config('app.name') }}" class="img-fluid mx-auto d-block" style="max-width: 200px;">
                    </div>
                    <h2 class="fs-20 fw-bolder mb-2 text-center">Entrar</h2>
                    <p class="fs-13 text-muted text-center mb-5">Aceda à sua conta — <strong>Lead Inteligente</strong></p>

                    {{ $slot }}

                </div>
            </div>
        </div>
    </main>
    <!--! ================================================================ !-->
    <!--! [End] Main Content !-->
    <!--! ================================================================ !-->
    @livewireScripts
</body>

</html>