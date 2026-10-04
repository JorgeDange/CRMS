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
</body>

</html>
