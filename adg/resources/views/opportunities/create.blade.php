<x-app-layout :pageHeaderBreadcrumb="['Nova']">
    <x-slot name="header">Nova Oportunidade</x-slot>

    <x-slot name="pageHeaderRight">
        <a href="javascript:void(0);" class="btn btn-light-brand">
            <i class="feather-layers me-2"></i>
            <span>Guardar rascunho</span>
        </a>
        <a href="javascript:void(0);" class="btn btn-primary"
           onclick="document.getElementById('oppForm').requestSubmit(); return false;">
            <i class="feather-user-plus me-2"></i>
            <span>Criar Oportunidade</span>
        </a>
    </x-slot>

    @livewire('opportunities.opportunity-create')
</x-app-layout>
