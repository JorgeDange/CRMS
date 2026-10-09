{{-- Painel Executivo — copiado de crm-prototipo/analytics.html (Sprint 11) --}}
<x-app-layout
    containerClass="nxl-container"
    contentClass="nxl-content"
    mainClass="main-content">
    <x-slot name="header">Painel Executivo</x-slot>

    {{-- Badge movido do content-area-header duplicado (igual ao protótipo) --}}
    <x-slot name="pageHeaderRight">
        <span class="badge bg-soft-secondary text-secondary fs-12 px-3 py-2">
            <i class="feather-eye me-1"></i>Somente leitura
        </span>
    </x-slot>

    @livewire('analytics.analytics')
</x-app-layout>