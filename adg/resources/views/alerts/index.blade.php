{{-- Alertas / Lista — Sprint 10 (resposta.md §10) --}}
<x-app-layout
    containerClass="apps-container"
    contentClass="without-header nxl-full-content"
    mainClass="d-flex"
    hidePageHeader="true"
    hideFooter="true">
    <x-slot name="header">Alertas</x-slot>

    @livewire('alerts.alert-list')
</x-app-layout>