@use('App\Services\OpportunityService')

{{-- Filtro + kanban copiados de leads.html (PROMPT_MASTER Sprint 2) --}}
<div class="row">
    <div class="col-lg-12"
         x-data="{
             boardEsq: 0,
             boardMax: 0,
             dirAuto: 0,
             _autoRaf: null,
             drop(event, stage) {
                 const id = parseInt(event.dataTransfer.getData('text/plain'));
                 if (isNaN(id)) return;
                 $wire.$call('move', id, stage);
             },
             actualizarSetas() {
                 const b = this.$refs.board;
                 if (!b) return;
                 this.boardEsq = b.scrollLeft;
                 this.boardMax = Math.max(0, b.scrollWidth - b.clientWidth);
             },
             moverColunas(dir) {
                 this.actualizarSetas();
                 if (dir < 0 ? this.boardEsq <= 2 : this.boardEsq >= this.boardMax - 2) return;
                 this.$refs.board.scrollBy({ left: dir * 564, behavior: 'smooth' });
             },
             iniciarAuto(dir) {
                 this.dirAuto = dir;
                 if (this._autoRaf !== null) return;
                 const passo = () => {
                     if (this.dirAuto === 0 || !this.$refs.board) { this._autoRaf = null; return; }
                     this.$refs.board.scrollBy({ left: this.dirAuto * 14 });
                     this._autoRaf = requestAnimationFrame(passo);
                 };
                 this._autoRaf = requestAnimationFrame(passo);
             },
             pararAuto() {
                 this.dirAuto = 0;
                 if (this._autoRaf !== null) { cancelAnimationFrame(this._autoRaf); this._autoRaf = null; }
             },
             boardDragOver(event) {
                 const b = this.$refs.board;
                 if (!b) return;
                 const r = b.getBoundingClientRect();
                 const margem = 72;
                 if (event.clientX < r.left + margem) this.iniciarAuto(-1);
                 else if (event.clientX > r.right - margem) this.iniciarAuto(1);
                 else this.pararAuto();
             },
             _init() { this.actualizarSetas(); }
         }"
         x-init="_init()"
         x-on:livewire:morphed.window="actualizarSetas()">
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
                    <div class="col-md-3 d-flex justify-content-end align-items-end gap-1">
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnKanbanAnterior"
                                title="Colunas anteriores" aria-label="Navegar para a esquerda"
                                x-on:click="moverColunas(-1)" x-bind:disabled="boardEsq <= 2">
                            <i class="feather-chevron-left"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnKanbanSeguinte"
                                title="Colunas seguintes" aria-label="Navegar para a direita"
                                x-on:click="moverColunas(1)" x-bind:disabled="boardEsq >= boardMax - 2">
                            <i class="feather-chevron-right"></i>
                        </button>
                    </div>
                    <div class="col-md-2">
                        <button type="button" wire:click="clearFilters" class="btn btn-sm btn-outline-secondary" id="btnClearOppFilters">
                            <i class="feather-x me-1"></i>Limpar
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!--! END: [FilterBar Oportunidades] !-->
        <div class="kanban-board d-flex gap-3 overflow-auto pb-3" id="kanbanBoard" x-ref="board"
             x-on:scroll.passive="actualizarSetas()"
             x-on:dragover="boardDragOver($event)"
             x-on:dragleave="if (!$el.contains($event.relatedTarget)) pararAuto()"
             x-on:drop="pararAuto()"
             x-on:dragend.window="pararAuto()">

            @foreach ($columns as $stage => $config)
                @if ($stageFilter === '' || $stageFilter === $stage)
                    <div class="kanban-column flex-shrink-0" data-stage="{{ $stage }}" style="min-width: 270px; width: 270px;">
                        <div class="kanban-col-header {{ $config['header'] }} p-3 rounded-top border">
                            <div class="d-flex align-items-center justify-content-between">
                                <h6 class="mb-0 fw-bold {{ $config['heading'] }}">{{ $config['label'] }}</h6>
                                <span class="{{ $config['badge'] }}">{{ $grouped->get($stage, collect())->count() }}</span>
                            </div>
                            <span class="fs-12 text-muted" title="{{ OpportunityService::formatKz($totals->get($stage, 0)) }}">{{ OpportunityService::formatKzCompact($totals->get($stage, 0)) }}</span>
                        </div>
                        <div class="kanban-col-body {{ $config['body'] }} p-2 rounded-bottom border border-top-0 d-flex flex-column gap-2"
                             style="min-height: 320px;" data-dropzone="{{ $stage }}"
                             x-on:dragover.prevent="$el.classList.add('drag-over')"
                             x-on:dragleave="$el.classList.remove('drag-over')"
                             x-on:drop.prevent="$el.classList.remove('drag-over'); drop($event, $el.dataset.dropzone)">

                            @foreach ($grouped->get($stage, collect()) as $opportunity)
                                <div class="kanban-card card mb-0 shadow-sm {{ $config['card'] }}" draggable="true"
                                     data-opp="{{ $opportunity->id }}"
                                     x-on:dragstart="$event.dataTransfer.setData('text/plain', $el.dataset.opp); $el.classList.add('dragging')"
                                     x-on:dragend="$el.classList.remove('dragging')">
                                    <div class="card-body p-3">
                                        <a href="{{ route('opportunities.show', $opportunity) }}" wire:navigate
                                           class="fw-bold d-block text-dark mb-1 text-decoration-none">{{ $opportunity->titulo }}</a>
                                        <span class="fs-12 text-muted d-block mb-2">{{ $opportunity->empresa ?? $opportunity->nome }}</span>
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="fw-bold {{ $config['value'] }} fs-13" title="{{ OpportunityService::formatKz($opportunity->valor_estimado) }}">{{ OpportunityService::formatKzCompact($opportunity->valor_estimado) }}</span>
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
