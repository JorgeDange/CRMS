{{-- Detalhe do Contrato — copiado de crm-prototipo/projects-view.html (Sprint 7) --}}
<x-app-layout
    containerClass="apps-container"
    contentClass="without-header nxl-full-content"
    mainClass="d-flex"
    hidePageHeader="true"
    hideFooter="true">
    <x-slot name="header">{{ $contract->numero_contrato }}</x-slot>

    @livewire('contracts.contract-view', ['contract' => $contract])
</x-app-layout>