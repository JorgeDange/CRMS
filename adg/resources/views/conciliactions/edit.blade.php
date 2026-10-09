{{-- Editar Conciliação — copiado de payment.html (Sprint 6) --}}
<x-app-layout
    containerClass="apps-container"
    contentClass="without-header nxl-full-content"
    mainClass="d-flex"
    hidePageHeader="true"
    hideFooter="true">
    <x-slot name="header">Editar Conciliação</x-slot>

    @livewire('conciliactions.conciliaction-form', ['conciliaction' => $conciliaction])
</x-app-layout>