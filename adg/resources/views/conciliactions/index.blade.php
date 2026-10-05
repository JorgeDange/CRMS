{{-- Conciliações / Lista — copiado de crm-prototipo/payment.html (Sprint 6) --}}
<x-app-layout
    containerClass="apps-container"
    contentClass="without-header nxl-full-content"
    mainClass="d-flex"
    hidePageHeader="true"
    hideFooter="true">
    <x-slot name="header">Conciliações</x-slot>

    @livewire('conciliactions.conciliaction-list')
</x-app-layout>