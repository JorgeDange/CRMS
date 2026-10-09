{{-- Editar Comercial — Sprint 9 --}}
<x-app-layout
    containerClass="apps-container"
    contentClass="without-header nxl-full-content"
    mainClass="d-flex"
    hidePageHeader="true"
    hideFooter="true">
    <x-slot name="header">Editar Comercial</x-slot>

    @livewire('commercials.commercial-form', ['commercial' => $commercial])
</x-app-layout>