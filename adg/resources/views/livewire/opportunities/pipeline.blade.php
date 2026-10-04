@use('App\Services\OpportunityService')

{{-- Filtro + kanban copiados de leads.html (PROMPT_MASTER Sprint 2) --}}
<div class="row">
    <div class="col-lg-12">
        <!--! BEGIN: [FilterBar Oportunidades] !-->
        <div class="card mb-3" id="oppFilterBar">
            <div class="card-body py-3">
                <div class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fs-12 mb-1" for="oppSearch">Pesquisar</label>
                        <input type="search" wire:model.live.debounce.300ms="search"
                               class="form-control form-control-sm" id="oppSearch"
                               placeholder="Título ou cliente..." autocomplete="off">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fs-12 mb-1" for="oppEstado">Estado</label>
                        <select wire:model.live="stageFilter" class="form-select form-select-sm" id="oppEstado">
                            <option value="">Todos (colunas)</option>
                            @foreach ($columns as $stage => $config)
                                <option value="{{ $stage }}">{{ $config['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3"></div>
                    <div class="col-md-2">
                        <button type="button" wire:click="clearFilters" class="btn btn-sm btn-outline-secondary" id="btnClearOppFilters">
                            <i class="feather-x me-1"></i>Limpar
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!--! END: [FilterBar Oportunidades] !-->
        <div class="kanban-board d-flex gap-3 overflow-auto pb-3" id="kanbanBoard">

            @foreach ($columns as $stage => $config)
                @if ($stageFilter === '' || $stageFilter === $stage)
                    <div class="kanban-column flex-shrink-0" data-stage="{{ $stage }}" style="min-width: 270px; width: 270px;">
                        <div class="kanban-col-header {{ $config['header'] }} p-3 rounded-top border">
                            <div class="d-flex align-items-center justify-content-between">
                                <h6 class="mb-0 fw-bold {{ $config['heading'] }}">{{ $config['label'] }}</h6>
                                <span class="{{ $config['badge'] }}">{{ $grouped->get($stage, collect())->count() }}</span>
                            </div>
                            <span class="fs-12 text-muted">{{ OpportunityService::formatKz($totals->get($stage, 0)) }}</span>
                        </div>
                        <div class="kanban-col-body {{ $config['body'] }} p-2 rounded-bottom border border-top-0 d-flex flex-column gap-2"
                             style="min-height: 320px;" data-dropzone="{{ $stage }}"
                             x-on:dragover.prevent="$el.classList.add('drag-over')"
                             x-on:dragleave="$el.classList.remove('drag-over')"
                             x-on:drop.prevent="$el.classList.remove('drag-over'); $wire.move(parseInt($event.dataTransfer.getData('text/plain')), '{{ $stage }}')">

                            @foreach ($grouped->get($stage, collect()) as $opportunity)
                                <div class="kanban-card card mb-0 shadow-sm {{ $config['card'] }}" draggable="true"
                                     x-on:dragstart="$event.dataTransfer.setData('text/plain', '{{ $opportunity->id }}'); $el.classList.add('dragging')"
                                     x-on:dragend="$el.classList.remove('dragging')">
                                    <div class="card-body p-3">
                                        <a href="{{ route('opportunities.show', $opportunity) }}" wire:navigate
                                           class="fw-bold d-block text-dark mb-1 text-decoration-none">{{ $opportunity->titulo }}</a>
                                        <span class="fs-12 text-muted d-block mb-2">{{ $opportunity->empresa ?? $opportunity->nome }}</span>
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="fw-bold {{ $config['value'] }} fs-13">{{ OpportunityService::formatKz($opportunity->valor_estimado) }}</span>
                                            <span class="badge {{ $config['prob'] }}">{{ $opportunity->probabilidade }}%</span>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between">
                                            @if ($opportunity->data_fecho)
                                                <span class="fs-11 text-muted"><i class="feather-calendar me-1"></i>{{ $opportunity->data_fecho->format('d/m/Y') }}</span>
                                            @else
                                                <span class="fs-11 text-muted">&nbsp;</span>
                                            @endif
                                            <div class="avatar-image avatar-xs rounded-circle">
                                                <img src="{{ asset('assets/images/avatar/1.png') }}" alt="" class="img-fluid">
                                            </div>
                                        </div>
                                        @if ($stage === 'nao_aprovado' && $opportunity->motivo_nao_aprovacao)
                                            <span class="fs-11 text-danger d-block mt-2"><i class="feather-x-circle me-1"></i>Motivo: {{ $opportunity->motivo_nao_aprovacao }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach

        </div>
    </div>
</div>
