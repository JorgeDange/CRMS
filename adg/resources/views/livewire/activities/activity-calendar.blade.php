{{-- Calendario da agenda — copiado de crm-prototipo/apps-calendar.html (Sprint 5, 8.4) --}}
{{-- Grelha propria a substituir o TUI Calendar (PROMPT_MASTER sec.4.4) --}}
{{-- Abertura/fecho do modal de detalhes trava/destrava o scroll do body --}}
<div class="d-flex flex-grow-1"
     x-on:modal-actividade.window="document.body.classList.toggle('modal-open', $event.detail.aberto === true)">
    <div class="content-area">
        <div class="content-area-header sticky-top">
            <div class="page-header-left d-flex align-items-center gap-2">
                <a href="{{ route('agenda.create') }}" class="btn btn-primary btn-sm" wire:navigate>
                    <i class="feather-plus me-2"></i><span>Nova Actividade</span>
                </a>
                <div class="dropdown">
                    <a href="javascript:void(0)" class="btn btn-light-brand dropdown-toggle" data-bs-toggle="dropdown"
                       data-bs-auto-close="outside" data-bs-offset="0,16">
                        <i class="feather-filter me-2"></i><span>Tipos</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end" style="min-width: 260px;">
                        <div class="dropdown-item mb-0">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="viewAllSchedules" wire:model.live="allVisible">
                                <label class="custom-control-label c-pointer" for="viewAllSchedules">
                                    <span class="fs-13 fw-semibold lh-lg">Ver todas as actividades</span>
                                </label>
                            </div>
                        </div>
                        <div class="dropdown-divider"></div>
                        <h6 class="dropdown-header">Legenda de tipos</h6>
                        @foreach ($tipos as $tipo)
                            <div class="dropdown-item mb-0">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="calendarTipo{{ $loop->index }}"
                                           value="{{ $tipo->value }}" wire:model.live="types">
                                    <label class="custom-control-label c-pointer" for="calendarTipo{{ $loop->index }}">
                                        <span class="tui-full-calendar-checkbox-round me-2" style="border-color: {{ $tipo->color() }}; background-color: {{ in_array($tipo->value, $types, true) ? $tipo->color() : 'transparent' }};"></span>
                                        {{ $tipo->labelPlural() }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="dropdown">
                    <a href="javascript:void(0)" class="btn btn-light-brand dropdown-toggle" data-bs-toggle="dropdown"
                       data-bs-auto-close="outside" data-bs-offset="0,16">
                        <i class="feather-clock me-2"></i><span>Próximos</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end p-2" style="min-width: 340px; max-height: 65vh; overflow-y: auto;">
                        <h6 class="dropdown-header">Eventos e Agendamentos</h6>
                        @forelse ($upcoming as $activity)
                            <a href="{{ route('agenda.create', ['atividade' => $activity->uuid]) }}" wire:navigate
                               class="dropdown-item d-block rounded-2 px-2 py-2 c-pointer text-decoration-none"
                               wire:key="schedule-{{ $activity->id }}">
                                <div class="d-flex align-items-start">
                                    <div class="wd-50 ht-50 {{ $activity->tipo->softBadge() }} lh-1 d-flex align-items-center justify-content-center flex-column rounded-2 schedule-date flex-shrink-0">
                                        <span class="fs-18 fw-bold mb-1 d-block">{{ $activity->data->format('d') }}</span>
                                        <span class="fs-10 text-semibold text-uppercase d-block">{{ $activity->data->format('M') }}</span>
                                    </div>
                                    <div class="ms-3 schedule-body">
                                        <div class="text-dark">
                                            <h6 class="fw-bold my-1 text-truncate-1-line">{{ $activity->descricao }}</h6>
                                            <span class="fs-11 fw-normal text-muted">
                                                {{ $activity->horaCurta() ? $activity->horaCurta().' — ' : '' }}{{ $activity->opportunity->nome }}
                                            </span>
                                            @if ($activity->proxima_accao)
                                                <p class="fs-12 fw-normal text-muted my-2 mb-0 text-truncate-2-line">{{ $activity->proxima_accao }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <p class="px-3 py-2 fs-12 text-muted mb-0">Sem actividades agendadas.</p>
                        @endforelse
                    </div>
                </div>
                <div id="menu" class="d-flex align-items-center justify-content-between">
                    <div class="d-flex calendar-action-btn">
                        <div class="dropdown me-1">
                            <button id="dropdownMenu-calendarType" class="dropdown-toggle calendar-dropdown-btn" type="button"
                                    data-bs-toggle="dropdown" data-bs-auto-close="outside" data-bs-offset="0,17">
                                <i class="{{ $viewIcons[$view] }} calendar-icon fs-12 me-1"></i>
                                <span id="calendarTypeName">{{ $viewLabels[$view] }}</span>
                            </button>
                            <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu-calendarType">
                                @foreach ($viewLabels as $viewKey => $viewLabel)
                                    <li role="presentation">
                                        <div class="dropdown-item c-pointer {{ $view === $viewKey ? 'active' : '' }}"
                                             role="menuitem" wire:click="setView('{{ $viewKey }}')">
                                            <i class="{{ $viewIcons[$viewKey] }} calendar-icon me-3"></i>
                                            <span>{{ $viewLabel }}</span>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="menu-navi d-none d-sm-flex">
                            <button type="button" class="move-today" wire:click="today">
                                <i class="feather-clock calendar-icon me-1 fs-12"></i>
                                <span>Hoje</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="page-header-right ms-auto">
                <div class="hstack gap-2">
                    <div id="renderRange" class="render-range d-none d-sm-flex">{{ $rangeLabel }}</div>
                    <div class="btn-group gap-1 menu-navi" role="group">
                        <button type="button" class="avatar-text avatar-md move-day" title="Anterior" wire:click="prev">
                            <i class="feather-chevron-left fs-12"></i>
                        </button>
                        <button type="button" class="avatar-text avatar-md move-day" title="Seguinte" wire:click="next">
                            <i class="feather-chevron-right fs-12"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-area-body p-0">
            <div class="agenda-calendar" style="--agenda-cols: {{ $cols }}">
                <div class="agenda-calendar-head">
                    @foreach ($dayNames as $dayName)
                        <div class="agenda-calendar-head-cell">{{ $dayName }}</div>
                    @endforeach
                </div>
                <div class="agenda-calendar-grid">
                    @foreach ($days as $day)
                        @php($dayKey = $day->toDateString())
                        @php($dayEvents = $eventsByDate->get($dayKey, collect()))
                        <div class="agenda-calendar-cell {{ $day->isToday() ? 'is-today' : '' }} {{ $greyOthers && $day->format('Y-m') !== $monthKey ? 'is-other' : '' }}"
                             wire:key="day-{{ $dayKey }}">
                            <span class="agenda-day-num">{{ $day->format('j') }}</span>
                            @foreach ($dayEvents->take(3) as $event)
                                <a class="agenda-event" wire:key="event-{{ $event->id }}"
                                   href="javascript:void(0);"
                                   wire:click="abrirDetalhe({{ $event->id }})"
                                   style="--agenda-event-color: {{ $event->tipo->color() }}"
                                   title="Ver detalhes: {{ $event->descricao }}">
                                    @if ($event->horaCurta())
                                        <span class="agenda-event-time">{{ $event->horaCurta() }}</span>
                                    @endif
                                    <span class="agenda-event-title text-truncate">{{ $event->descricao }}</span>
                                </a>
                            @endforeach
                            @if ($dayEvents->count() > 3)
                                <div class="weekday-grid-more-schedules">+{{ $dayEvents->count() - 3 }}</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <x-ui.footer />

        {{-- Modal de detalhes do evento — aberto via wire:click="abrirDetalhe" (sem JS do Bootstrap) --}}
        <div class="modal fade {{ $detalheId ? 'show d-block' : '' }}" id="modalDetalheActividade" tabindex="-1"
             role="dialog" aria-modal="true" aria-hidden="{{ $detalheId ? 'false' : 'true' }}"
             style="{{ $detalheId ? '' : 'display: none;' }}"
             wire:click.self="fecharDetalhe">
            @if ($detalhe)
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <div class="d-flex align-items-center gap-2 flex-wrap me-3">
                                <span class="badge {{ $detalhe->tipo->badge() }}">{{ $detalhe->tipo->label() }}</span>
                                <h5 class="modal-title mb-0">{{ $detalhe->descricao }}</h5>
                            </div>
                            <button type="button" class="btn-close" wire:click="fecharDetalhe" aria-label="Fechar"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <span class="fs-11 fw-semibold text-muted text-uppercase d-block mb-1">Cliente</span>
                                    <span class="fs-14 fw-semibold">{{ $detalhe->opportunity->nome }}</span>
                                </div>
                                <div class="col-sm-3">
                                    <span class="fs-11 fw-semibold text-muted text-uppercase d-block mb-1">Estado</span>
                                    <span class="badge {{ $detalhe->status->badgeClass() }}">{{ $detalhe->status->label() }}</span>
                                </div>
                                <div class="col-sm-3">
                                    <span class="fs-11 fw-semibold text-muted text-uppercase d-block mb-1">Tipo</span>
                                    <span class="fs-13 fw-semibold">{{ $detalhe->tipo->labelPlural() }}</span>
                                </div>
                                <div class="col-sm-4">
                                    <span class="fs-11 fw-semibold text-muted text-uppercase d-block mb-1">Data</span>
                                    <span class="fs-13 fw-semibold">{{ $detalhe->data->format('d/m/Y') }}</span>
                                </div>
                                <div class="col-sm-4">
                                    <span class="fs-11 fw-semibold text-muted text-uppercase d-block mb-1">Hora</span>
                                    <span class="fs-13 fw-semibold">{{ $detalhe->horaCurta() ?? '—' }}</span>
                                </div>
                                <div class="col-sm-4">
                                    <span class="fs-11 fw-semibold text-muted text-uppercase d-block mb-1">Prazo</span>
                                    <span class="fs-13 fw-semibold">{{ $detalhe->proxima_accao_prazo?->format('d/m/Y') ?? '—' }}</span>
                                </div>
                                @if ($detalhe->proxima_accao)
                                    <div class="col-12">
                                        <span class="fs-11 fw-semibold text-muted text-uppercase d-block mb-1">Próxima acção</span>
                                        <p class="fs-13 fw-normal mb-0">{{ $detalhe->proxima_accao }}</p>
                                    </div>
                                @endif
                                @if ($detalhe->resultado)
                                    <div class="col-12">
                                        <span class="fs-11 fw-semibold text-muted text-uppercase d-block mb-1">Resultado</span>
                                        <p class="fs-13 fw-normal mb-0">{{ $detalhe->resultado }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="modal-footer">
                            <a href="{{ route('agenda.create', ['atividade' => $detalhe->uuid]) }}" wire:navigate
                               class="btn btn-primary btn-sm">
                                <i class="feather-edit me-2"></i><span>Editar</span>
                            </a>
                            @if ($detalhe->status->value !== 'concluida')
                                <button type="button" class="btn btn-outline-success btn-sm" wire:click="concluirDetalhe">
                                    <i class="feather-check me-2"></i><span>Concluir</span>
                                </button>
                            @endif
                            <button type="button" class="btn btn-outline-danger btn-sm"
                                    @click.prevent="$store.confirmar.abrir({ titulo: 'Eliminar actividade', mensagem: 'Eliminar esta actividade? Esta acção não pode ser desfeita.', confirmar: 'Eliminar' }).then(ok => ok && $wire.eliminarDetalhe())">
                                <i class="feather-trash-2 me-2"></i><span>Eliminar</span>
                            </button>
                            <button type="button" class="btn btn-light-brand btn-sm" wire:click="fecharDetalhe">Fechar</button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        {{-- Backdrop: blur do fundo (prototipo blur .nxl-container com modal aberto);
             inline porque o minifier do build dropa a propriedade `backdrop-filter`
             e so deixa o `-webkit-` (que o Chrome ignora) --}}
        <div class="modal-backdrop fade {{ $detalheId ? 'show' : '' }}"
             style="{{ $detalheId ? 'backdrop-filter: blur(3px); -webkit-backdrop-filter: blur(3px);' : 'display: none;' }}"
             wire:click="fecharDetalhe"></div>
    </div>
</div>
