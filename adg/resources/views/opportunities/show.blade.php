@use('App\Livewire\Opportunities\Pipeline')

@php
    $stageLabel = Pipeline::COLUMNS[$opportunity->fase_atual->value]['label'] ?? $opportunity->fase_atual->label();
    $stageVariant = $opportunity->fase_atual->badgeVariant();
@endphp

<x-app-layout :pageHeaderBreadcrumb="['Oportunidades', $opportunity->titulo]">
    <x-slot name="header">{{ $opportunity->titulo }}</x-slot>

    <x-slot name="pageHeaderRight">
        <span class="badge bg-soft-{{ $stageVariant }} text-{{ $stageVariant }} fs-12 px-3 py-2">{{ $stageLabel }}</span>
    </x-slot>

    {{-- Barra de tabs (leads-view.html) — entre page-header e main-content --}}
    <x-slot name="pageHeaderBelow">
        <div class="bg-white py-3 border-bottom rounded-0 p-md-0 mb-0">
            <div class="d-flex align-items-center justify-content-between">
                <div class="nav-tabs-wrapper page-content-left-sidebar-wrapper">
                    <ul class="nav nav-tabs nav-tabs-custom-style" id="oppTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button type="button" class="nav-link active" data-bs-toggle="tab" data-bs-target="#profileTab" onclick="setOppTab('profile')">Resumo</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#proposalTab" onclick="setOppTab('proposal')">Proposta</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#tasksTab" onclick="setOppTab('tasks')">Actividades</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#notesTab" onclick="setOppTab('notes')">Notas</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#visitTab" onclick="setOppTab('visit')">Relatório de Visita</button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <script>
            function setOppTab(tab) {
                var root = document.getElementById('opportunity-detail');
                if (root && window.Livewire) {
                    window.Livewire.find(root.getAttribute('wire:id')).set('activeTab', tab);
                }
            }
        </script>
    </x-slot>

    <livewire:opportunities.opportunity-view :opportunity="$opportunity" id="opportunity-detail" />
</x-app-layout>
