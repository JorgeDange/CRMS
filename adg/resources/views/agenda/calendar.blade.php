{{-- Agenda / calendario — copiado de crm-prototipo/apps-calendar.html (Sprint 5) --}}
<x-app-layout
    containerClass="apps-container apps-calendar"
    contentClass="without-header nxl-full-content"
    mainClass="d-flex"
    hidePageHeader="true"
    hideFooter="true">
    <x-slot name="header">Calendário</x-slot>

    @livewire('activities.activity-calendar')
</x-app-layout>
