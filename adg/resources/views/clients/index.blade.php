{{-- Clientes / Tabela de Clientes — Sprint 13 (seguir.md §8) --}}
<x-app-layout
    containerClass="apps-container"
    contentClass="without-header nxl-full-content"
    mainClass="d-flex"
    hidePageHeader="true"
    hideFooter="true">
    <x-slot name="header">Clientes</x-slot>

    @livewire('clients.client-table')
</x-app-layout>
