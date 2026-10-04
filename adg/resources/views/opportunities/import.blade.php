<x-app-layout :pageHeaderBreadcrumb="['Nova Oportunidade', 'Importar Excel']">
    <x-slot name="header">Importar Oportunidades</x-slot>

    <x-slot name="pageHeaderRight">
        <a href="{{ route('opportunities.create') }}" class="btn btn-light-brand" wire:navigate>
            <i class="feather-user-plus me-2"></i>
            <span>Nova Oportunidade</span>
        </a>
        <a href="{{ route('opportunities.import.template') }}" class="btn btn-primary">
            <i class="feather-download me-2"></i>
            <span>Descarregar template</span>
        </a>
    </x-slot>

    @livewire('opportunities.opportunity-import')
</x-app-layout>
