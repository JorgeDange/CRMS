{{-- Novo Contrato — copiado de crm-prototipo/projects-create.html (Sprint 7) --}}
<x-app-layout
    containerClass="apps-container"
    contentClass="without-header nxl-full-content"
    mainClass="d-flex"
    hidePageHeader="true"
    hideFooter="true">
    <x-slot name="header">Novo Contrato</x-slot>

    @livewire('contracts.contract-form')
</x-app-layout>