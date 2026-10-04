{{-- Lista de actividades — copiado de crm-prototipo/apps-tasks.html (Sprint 5, 8.4) --}}
{{-- Raiz unica obrigatoria (Livewire); envolve content-sidebar + content-area como no prototipo --}}
<div class="d-flex flex-grow-1" x-data="{ sidebarOpen: false }">
    <div class="content-sidebar content-sidebar-md" :class="sidebarOpen && 'app-sidebar-open'">
        <div class="content-sidebar-header bg-white sticky-top hstack justify-content-between">
            <h4 class="fw-bolder mb-0">Actividades</h4>
            <a href="javascript:void(0);" class="app-sidebar-close-trigger d-flex" @click="sidebarOpen = false">
                <i class="feather-x"></i>
            </a>
        </div>
        <div class="content-sidebar-header">
            <a href="{{ route('agenda.create') }}" class="btn btn-primary w-100" wire:navigate>
                <i class="feather-plus me-2"></i>
                <span>Nova Actividade</span>
            </a>
        </div>
        <div class="content-sidebar-body">
            <ul class="nav flex-column nxl-content-sidebar-item">
                <li class="nav-item">
                    <a class="nav-link {{ $statusFilter === '' ? 'active' : '' }}" href="javascript:void(0);" wire:click="filterStatus('')">
                        <i class="feather-list"></i>
                        <span>Todas</span>
                    </a>
                </li>
                @foreach ($estados as $estado)
                    <li class="nav-item">
                        <a class="nav-link {{ $statusFilter === $estado->value ? 'active' : '' }}"
                           href="javascript:void(0);" wire:click="filterStatus('{{ $estado->value }}')">
                            <i class="{{ $estado->value === 'pendente' ? 'feather-watch' : ($estado->value === 'concluida' ? 'feather-check-circle' : 'feather-x-circle') }}"></i>
                            <span>{{ $estado->label() }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
            <ul class="nav flex-column nxl-content-sidebar-item">
                <li class="px-4 my-2 fs-10 fw-bold text-uppercase text-muted text-spacing-1">Tipo</li>
                @foreach ($tipos as $tipo)
                    <li class="nav-item">
                        <a class="nav-link {{ $typeFilter === $tipo->value ? 'active' : '' }}"
                           href="javascript:void(0);" wire:click="filterType('{{ $tipo->value }}')">
                            <i class="{{ $tipo->icon() }}"></i>
                            <span>{{ $tipo->labelPlural() }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="content-area">
        <div class="content-area-header sticky-top">
            <div class="page-header-left d-flex align-items-center gap-2">
                <a href="javascript:void(0);" class="app-sidebar-open-trigger me-2" @click="sidebarOpen = true">
                    <i class="feather-align-left fs-20"></i>
                </a>
                <div class="dropdown">
                    <a href="javascript:void(0)" class="btn btn-light-brand dropdown-toggle" data-bs-toggle="dropdown" data-bs-offset="0,16">
                        <i class="feather-check-circle me-2"></i>
                        <span>{{ $statusFilter === '' ? 'As minhas actividades' : \App\Enums\ActivityStatus::from($statusFilter)->label() }}</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item {{ $statusFilter === '' ? 'active' : '' }}" href="javascript:void(0)" wire:click="filterStatus('')">
                            <i class="feather-hash"></i>
                            <span>Todas</span>
                        </a>
                        @foreach ($estados as $estado)
                            <a class="dropdown-item {{ $statusFilter === $estado->value ? 'active' : '' }}"
                               href="javascript:void(0)" wire:click="filterStatus('{{ $estado->value }}')">
                                <i class="{{ $estado->value === 'pendente' ? 'feather-clock' : ($estado->value === 'concluida' ? 'feather-check-circle' : 'feather-x-circle') }}"></i>
                                <span>{{ $estado->label() }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="page-header-right ms-auto">
                <div class="hstack gap-2">
                    <div class="hstack" x-data="{ searchOpen: false }">
                        <a href="javascript:void(0)" class="search-form-open-toggle" @click="searchOpen = true">
                            <div class="avatar-text avatar-md" data-bs-toggle="tooltip" data-bs-trigger="hover" title="Pesquisar">
                                <i class="feather-search"></i>
                            </div>
                        </a>
                        <form class="search-form" x-show="searchOpen" x-cloak @submit.prevent @click.outside="searchOpen = false">
                            <div class="search-form-inner">
                                <a href="javascript:void(0)" class="search-form-close-toggle" @click="searchOpen = false">
                                    <div class="avatar-text avatar-md" data-bs-toggle="tooltip" data-bs-trigger="hover" title="Fechar pesquisa">
                                        <i class="feather-arrow-left"></i>
                                    </div>
                                </a>
                                <input type="search" class="py-3 px-0 border-0 w-100" placeholder="Pesquisar..."
                                       wire:model.live.debounce.300ms="search">
                            </div>
                        </form>
                    </div>
                    <div class="dropdown d-none d-sm-flex">
                        <a href="javascript:void(0)" class="btn btn-light-brand btn-sm rounded-pill dropdown-toggle"
                           data-bs-toggle="dropdown" data-bs-offset="0,23">
                            {{ $sort === 'asc' ? 'Mais antigas' : 'Mais recentes' }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item {{ $sort === 'desc' ? 'active' : '' }}"
                                   href="javascript:void(0)" wire:click="$set('sort', 'desc')">Mais recentes</a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ $sort === 'asc' ? 'active' : '' }}"
                                   href="javascript:void(0)" wire:click="$set('sort', 'asc')">Mais antigas</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-area-body">
            @forelse ($groups as $group)
                @php($collapseId = 'tasks_collapse_'.$loop->index)
                <div class="card stretch stretch-full">
                    <a href="javascript:void(0);" class="card-header" data-bs-toggle="collapse" data-bs-target="#{{ $collapseId }}">
                        <h5 class="mb-0">{{ $group['label'] }}</h5>
                    </a>
                    <div class="card-body collapse show" id="{{ $collapseId }}">
                        <ul class="list-unstyled mb-0">
                            @foreach ($group['items'] as $activity)
                                <li class="single-task-list p-3 mb-3 border border-dashed rounded-3 {{ $activity->status->value === 'concluida' ? 'task-completed' : '' }}"
                                    wire:key="activity-{{ $activity->id }}">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center gap-3 me-3">
                                            <div class="custom-control custom-checkbox me-2">
                                                <input type="checkbox" class="custom-control-input"
                                                       id="activityCheck{{ $activity->id }}"
                                                       {{ $activity->status->value === 'concluida' ? 'checked' : '' }}
                                                       wire:click="toggle({{ $activity->id }})">
                                                <label class="custom-control-label c-pointer" for="activityCheck{{ $activity->id }}"></label>
                                            </div>
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="lh-base"><i class="feather-star"></i></div>
                                                <a href="{{ route('agenda.create', ['atividade' => $activity->uuid]) }}"
                                                   class="single-task-list-link" wire:navigate>
                                                    <div class="fs-13 fw-bold text-truncate-1-line">
                                                        {{ $activity->descricao }}
                                                        <span class="ms-2 badge {{ $activity->status->badgeClass() }}">{{ $activity->status->label() }}</span>
                                                    </div>
                                                    <div class="fs-12 fw-normal text-muted text-truncate-1-line">
                                                        {{ $activity->opportunity->nome }}{{ $activity->proxima_accao ? ' · '.$activity->proxima_accao : '' }}
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="d-flex flex-shrink-0 align-items-center gap-3">
                                            @if ($activity->status->value !== 'concluida')
                                                <button type="button"
                                                        class="btn btn-xs btn-outline-success d-none d-sm-inline-flex"
                                                        title="Concluir actividade"
                                                        wire:click="toggle({{ $activity->id }})">
                                                    <i class="feather-check me-1"></i>Concluir
                                                </button>
                                            @endif
                                            <div class="badge {{ $activity->tipo->badge() }} d-md-inline-block d-none">{{ $activity->tipo->labelPlural() }}</div>
                                            @if ($activity->proxima_accao_prazo)
                                                <div class="d-md-inline-block d-none me-3">{{ $activity->proxima_accao_prazo->format('d M, Y') }}</div>
                                            @endif
                                            <div class="dropdown">
                                                <a href="javascript:void(0);" class="avatar-text avatar-md" data-bs-toggle="dropdown">
                                                    <i class="feather-more-vertical"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <a class="dropdown-item"
                                                       href="{{ route('agenda.create', ['atividade' => $activity->uuid]) }}"
                                                       wire:navigate>Editar actividade</a>
                                                    <a class="dropdown-item" href="javascript:void(0);"
                                                       @click.prevent="confirm('Eliminar esta actividade?') && $wire.remove({{ $activity->id }})">Eliminar actividade</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @empty
                <div class="card stretch stretch-full">
                    <div class="card-body text-center py-5">
                        <i class="feather-calendar fs-1 text-muted"></i>
                        <p class="text-muted mt-3 mb-3">Não existem actividades para os filtros seleccionados.</p>
                        <a href="{{ route('agenda.create') }}" class="btn btn-primary btn-sm" wire:navigate>
                            <i class="feather-plus me-2"></i>Nova Actividade
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        <x-ui.footer />
    </div>
</div>
