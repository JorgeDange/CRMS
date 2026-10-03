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
</head>

<body x-data="sidebar">
    <x-layout.sidebar />

    <x-layout.header />

    <!--! [Start] Main Content !-->
    <main class="nxl-container">
        <div class="nxl-content">
            <!-- [ page-header ] start -->
            <div class="page-header">
                <div class="page-header-left d-flex align-items-center">
                    <div class="page-header-title">
                        <h5 class="m-b-10">{{ $pageTitle }}</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" wire:navigate>Início</a></li>
                        <li class="breadcrumb-item">{{ $pageTitle }}</li>
                    </ul>
                </div>
                <div class="page-header-right ms-auto">
                    <div class="page-header-right-items">
                        <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                            {{ $pageHeaderRight ?? '' }}
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ page-header ] end -->
            <!-- [ Main Content ] start -->
            <div class="main-content">
                {{ $slot }}
            </div>
            <!-- [ Main Content ] end -->
        </div>
        <!-- [ Footer ] start -->
        <footer class="footer">
            <p class="fs-11 text-muted fw-medium text-uppercase mb-0 copyright">
                <span>Copyright © {{ date('Y') }}</span>
            </p>
            <div class="d-flex align-items-center gap-4">
                <a href="javascript:void(0);" class="fs-11 fw-semibold text-uppercase">Ajuda</a>
                <a href="javascript:void(0);" class="fs-11 fw-semibold text-uppercase">Termos</a>
                <a href="javascript:void(0);" class="fs-11 fw-semibold text-uppercase">Privacidade</a>
            </div>
        </footer>
        <!-- [ Footer ] end -->
    </main>
    <!--! [End] Main Content !-->
</body>

</html>
