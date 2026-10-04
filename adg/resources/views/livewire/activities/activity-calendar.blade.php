{{-- Calendario da agenda — copiado de crm-prototipo/apps-calendar.html (Sprint 5, 8.4) --}}
{{-- Grelha propria a substituir o TUI Calendar (PROMPT_MASTER sec.4.4) --}}
<div class="d-flex flex-grow-1" x-data="{ sidebarOpen: false }">
    <div class="content-sidebar content-sidebar-xl" :class="sidebarOpen && 'app-sidebar-open'">
        <div class="content-sidebar-header bg-white sticky-top hstack justify-content-between">
            <h4 class="fw-bolder mb-0">Calendário</h4>
            <a href="javascript:void(0);" class="app-sidebar-close-trigger d-flex" @click="sidebarOpen = false">
                <i class="feather-x"></i>
            </a>
        </div>
        <div class="content-sidebar-header">
            <a href="{{ route('agenda.create') }}" class="btn btn-primary w-100" wire:navigate>
                <i class="feather-calendar me-2"></i>
                <span>Nova Actividade</span>
            </a>
        </div>
        <div class="content-sidebar-body">
            <div id="lnb-calendars" class="lnb-calendars">
                <div class="lnb-calendars-item">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="viewAllSchedules" wire:model.live="allVisible">
                        <label class="custom-control-label c-pointer" for="viewAllSchedules">
                            <span class="fs-13 fw-semibold lh-lg" style="margin-top: -2px">Ver todas as actividades</span>
                        </label>
                    </div>
                </div>
                <div class="px-4 mb-2">
                    <span class="fs-10 fw-bold text-uppercase text-muted text-spacing-1">Legenda de tipos (ActivityType)</span>
                </div>
                <div id="calendarList" class="lnb-calendars-d1">
                    @foreach ($tipos as $tipo)
                        <div class="lnb-calendars-item">
                            <label>
                                <input type="checkbox" class="tui-full-calendar-checkbox-round" value="{{ $tipo->value }}" wire:model.live="types">
                                <span style="border-color: {{ $tipo->color() }}; background-color: {{ in_array($tipo->value, $types, true) ? $tipo->color() : 'transparent' }}"></span>
                                <span>{{ $tipo->labelPlural() }}</span>
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
            <h2 class="px-4 fs-10 fw-bold text-uppercase my-4 text-muted text-spacing-1 text-truncate-1-line">Eventos e Agendamentos</h2>
            @forelse ($upcoming as $activity)
                <a href="{{ route('agenda.create', ['atividade' => $activity->uuid]) }}" wire:navigate
                   class="d-block p-4 border-top c-pointer single-item schedule-item text-decoration-none"
                   wire:key="schedule-{{ $activity->id }}">
                    <div class="d-flex align-items-start">
                        <div class="wd-50 ht-50 {{ $activity->tipo->softBadge() }} lh-1 d-flex align-items-center justify-content-center flex-column rounded-2 schedule-date">
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
                                    <p class="fs-12 fw-normal text-muted my-3 text-truncate-2-line">{{ $activity->proxima_accao }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <p class="px-4 fs-12 text-muted">Sem actividades agendadas.</p>
            @endforelse
        </div>
    </div>

    <div class="content-area">
        <div class="content-area-header sticky-top">
            <div class="page-header-left d-flex align-items-center gap-2">
                <a href="javascript:void(0);" class="app-sidebar-open-trigger me-2" @click="sidebarOpen = true">
                    <i class="feather-align-left fs-20"></i>
                </a>
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
                                   href="{{ route('agenda.create', ['atividade' => $event->uuid]) }}"
                                   wire:navigate
                                   style="--agenda-event-color: {{ $event->tipo->color() }}"
                                   title="{{ $event->descricao }}">
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
    </div>
</div>
