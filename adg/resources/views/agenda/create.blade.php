{{-- Agenda / nova actividade — copiado de crm-prototipo/apps-notes.html (Sprint 5) --}}
<x-app-layout
    containerClass="apps-container apps-notes"
    contentClass="without-header nxl-full-content"
    mainClass="d-flex"
    hidePageHeader="true"
    hideFooter="true">
    <x-slot name="header">Nova Actividade</x-slot>

    @livewire('activities.activity-form')
</x-app-layout>
