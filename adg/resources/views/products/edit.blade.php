{{-- Editar Produto — copiado de crm-prototipo/customers-create.html (Sprint 8) --}}
<x-app-layout
    containerClass="apps-container"
    contentClass="without-header nxl-full-content"
    mainClass="d-flex"
    hidePageHeader="true"
    hideFooter="true">
    <x-slot name="header">Editar Produto</x-slot>

    @livewire('products.product-form')
</x-app-layout>