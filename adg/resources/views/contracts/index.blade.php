{{-- Contratos / Lista — copiado de crm-prototipo/projects.html (Sprint 7) --}}
<x-app-layout
    containerClass="apps-container"
    contentClass="without-header nxl-full-content"
    mainClass="d-flex"
    hidePageHeader="true"
    hideFooter="true">
    <x-slot name="header">Contratos</x-slot>

    @livewire('contracts.contract-list')
</x-app-layout>