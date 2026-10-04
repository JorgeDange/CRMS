{{-- Agenda / lista de actividades — copiado de crm-prototipo/apps-tasks.html (Sprint 5) --}}
<x-app-layout
    containerClass="apps-container apps-tasks"
    contentClass="without-header nxl-full-content"
    mainClass="d-flex"
    hidePageHeader="true"
    hideFooter="true">
    <x-slot name="header">Actividades</x-slot>

    @livewire('activities.activity-list')
</x-app-layout>
