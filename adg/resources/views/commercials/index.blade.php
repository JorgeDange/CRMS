{{-- Comerciais / Lista — Sprint 9 --}}
<x-app-layout
    containerClass="apps-container"
    contentClass="without-header nxl-full-content"
    mainClass="d-flex"
    hidePageHeader="true"
    hideFooter="true">
    <x-slot name="header">Comerciais</x-slot>

    @livewire('commercials.commercial-list')
</x-app-layout>