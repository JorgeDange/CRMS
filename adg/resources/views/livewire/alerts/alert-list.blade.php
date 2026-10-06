{{-- Lista de Alertas — Sprint 10 (resposta.md §10) --}}
<div class="d-flex flex-grow-1" x-data="{ sidebarOpen: false }">
    <div class="content-sidebar content-sidebar-md" :class="sidebarOpen && 'app-sidebar-open'">
        <div class="content-sidebar-header bg-white sticky-top hstack justify-content-between">
            <h4 class="fw-bolder mb-0">Alertas</h4>
            <a href="javascript:void(0);" class="app-sidebar-close-trigger d-flex" @click="sidebarOpen = false">
                <i class="feather-x"></i>
            </a>
        </div>
        <div class="content-sidebar-body">
            <ul class="nav flex-column nxl-content-sidebar-item">
                <li class="nav-item">
                    <a class="nav-link {{ $tipoFilter === '' ? 'active' : '' }}"
                       href="javascript:void(0);" wire:click="filterTipo('')">
                        <i class="feather-list"></i>
                        <span>Todos</span>
                    </a>
                </li>
                @foreach ($tipos as $tipo)
                    <li class="nav-item">
                        <a class="nav-link {{ $tipoFilter === $tipo->value ? 'active' : '' }}"
                           href="javascript:void(0);" wire:click="filterTipo('{{ $tipo->value }}')">
                            <i class="{{ $tipo->icon() }}"></i>
                            <span>{{ $tipo->label() }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
            <ul class="nav flex-column nxl-content-sidebar-item">
                <li class="px-4 my-2 fs-10 fw-bold text-uppercase text-muted text-spacing-1">Estado</li>
                <li class="nav-item">
                    <a class="nav-link {{ $statusFilter === '' ? 'active' : '' }}"
                       href="javascript:void(0);" wire:click="filterStatus('')">
                        <i class="feather-filter"></i>
                        <span>Todos</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $statusFilter === 'nao_lido' ? 'active' : '' }}"
                       href="javascript:void(0);" wire:click="filterStatus('nao_lido')">
                        <i class="feather-bell"></i>
                        <span>Não lidos</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $statusFilter === 'lido' ? 'active' : '' }}"
                       href="javascript:void(0);" wire:click="filterStatus('lido')">
                        <i class="feather-check-circle"></i>
                        <span>Lidos</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $statusFilter === 'resolvido' ? 'active' : '' }}"
                       href="javascript:void(0);" wire:click="filterStatus('resolvido')">
                        <i class="feather-check-square"></i>
                        <span>Resolvidos</span>
                    </a>
                </li>
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
                        <i class="feather-filter me-2"></i>
                        <span>Filtros</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item {{ $tipoFilter === '' ? 'active' : '' }}"
                           href="javascript:void(0)" wire:click="filterTipo('')">
                            <i class="feather-filter me-2"></i>
                            <span>Todos os tipos</span>
                        </a>
                        @foreach ($tipos as $tipo)
                            <a class="dropdown-item {{ $tipoFilter === $tipo->value ? 'active' : '' }}"
                               href="javascript:void(0)" wire:click="filterTipo('{{ $tipo->value }}')">
                                <i class="{{ $tipo->icon() }} me-2"></i>
                                <span>{{ $tipo->label() }}</span>
                            </a>
                        @endforeach
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item {{ $statusFilter === '' ? 'active' : '' }}"
                           href="javascript:void(0)" wire:click="filterStatus('')">
                            <i class="feather-filter me-2"></i>
                            <span>Todos os estados</span>
                        </a>
                        <a class="dropdown-item {{ $statusFilter === 'nao_lido' ? 'active' : '' }}"
                           href="javascript:void(0)" wire:click="filterStatus('nao_lido')">
                            <i class="feather-bell me-2"></i>
                            <span>Não lidos</span>
                        </a>
                        <a class="dropdown-item {{ $statusFilter === 'lido' ? 'active' : '' }}"
                           href="javascript:void(0)" wire:click="filterStatus('lido')">
                            <i class="feather-check-circle me-2"></i>
                            <span>Lidos</span>
                        </a>
                        <a class="dropdown-item {{ $statusFilter === 'resolvido' ? 'active' : '' }}"
                           href="javascript:void(0)" wire:click="filterStatus('resolvido')">
                            <i class="feather-check-square me-2"></i>
                            <span>Resolvidos</span>
                        </a>
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
                            {{ $sort === 'asc' ? 'Mais antigos' : 'Mais recentes' }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item {{ $sort === 'desc' ? 'active' : '' }}"
                                   href="javascript:void(0)" wire:click="$set('sort', 'desc')">Mais recentes</a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ $sort === 'asc' ? 'active' : '' }}"
                                   href="javascript:void(0)" wire:click="$set('sort', 'asc')">Mais antigos</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-area-body">
            @if ($alerts->isEmpty())
                <div class="card stretch stretch-full">
                    <div class="card-body text-center py-5">
                        <i class="feather-bell fs-1 text-muted"></i>
                        <p class="text-muted mt-3 mb-3">Não existem alertas para os filtros seleccionados.</p>
                    </div>
                </div>
            @else
                <div class="card stretch stretch-full">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle" id="alertList">
                                <thead>
                                    <tr>
                                        <th class="wd-30">
                                            <div class="btn-group mb-1">
                                                <div class="custom-control custom-checkbox ms-1">
                                                    <input type="checkbox" class="custom-control-input" id="checkAllAlert">
                                                    <label class="custom-control-label" for="checkAllAlert"></label>
                                                </div>
                                            </div>
                                        </th>
                                        <th>Tipo</th>
                                        <th>Mensagem</th>
                                        <th>Entidade</th>
                                        <th>Criado por</th>
                                        <th>Criado em</th>
                                        <th>Estado</th>
                                        <th class="text-end">Acções</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($alerts as $alert)
                                        <tr class="single-item wire:key=\"alert-{{ $alert->id }}\" {{ ! $alert->lido ? 'table-info' : '' }}>
                                            <td>
                                                <div class="item-checkbox ms-1">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input checkbox" id="checkBox_{{ $alert->id }}">
                                                        <label class="custom-control-label" for="checkBox_{{ $alert->id }}"></label>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge {{ $alert->tipo->badgeClass() }}">
                                                    <i class="{{ $alert->tipo->icon() }} me-1"></i>
                                                    {{ $alert->tipo->label() }}
                                                </span>
                                            </td>
                                            <td class="text-truncate-1-line" style="max-width: 400px;">{{ $alert->mensagem }}</td>
                                            <td>
                                                @if ($alert->entidade === 'opportunities')
                                                    <span class="badge bg-soft-primary text-primary">Oportunidade</span>
                                                @elseif ($alert->entidade === 'contracts')
                                                    <span class="badge bg-soft-primary text-primary">Contrato</span>
                                                @elseif ($alert->entidade === 'conciliactions')
                                                    <span class="badge bg-soft-secondary text-secondary">Conciliação</span>
                                                @else
                                                    {{ $alert->entidade }}
                                                @endif
                                            </td>
                                            <td>
                                                {{ $alert->user?->name ?? '—' }}
                                            </td>
                                            <td class="text-end">{{ $alert->created_at?->format('d/m/Y H:i') ?? '—' }}</td>
                                            <td>
                                                @if ($alert->resolvido)
                                                    <span class="badge bg-soft-success text-success">
                                                        <i class="feather-check-circle me-1"></i>Resolvido
                                                    </span>
                                                @elseif ($alert->lido)
                                                    <span class="badge bg-soft-info text-info">
                                                        <i class="feather-check me-1"></i>Lido
                                                    </span>
                                                @else
                                                    <span class="badge bg-soft-warning text-warning">
                                                        <i class="feather-alert-triangle me-1"></i>Não lido
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <div class="hstack gap-2 justify-content-end">
                                                    @if (! $alert->lido)
                                                        <button type="button"
                                                                class="avatar-text avatar-md btn-primary"
                                                                title="Marcar como lido"
                                                                wire:click="marcarLido({{ $alert->id }})">
                                                            <i class="feather-check"></i>
                                                        </button>
                                                    @endif
                                                    @if (! $alert->resolvido)
                                                        <button type="button"
                                                                class="avatar-text avatar-md btn-success"
                                                                title="Marcar como resolvido"
                                                                wire:click="marcarResolvido({{ $alert->id }})">
                                                            <i class="feather-check-circle"></i>
                                                        </button>
                                                    @endif
                                                    <div class="dropdown">
                                                        <a href="javascript:void(0);" class="avatar-text avatar-md" data-bs-toggle="dropdown">
                                                            <i class="feather-more-horizontal"></i>
                                                        </a>
                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                            @if (! $alert->lido)
                                                                <li>
                                                                    <a class="dropdown-item"
                                                                       href="javascript:void(0);"
                                                                       wire:click="marcarLido({{ $alert->id }})">
                                                                        <i class="feather-check me-3"></i>
                                                                        <span>Marcar como lido</span>
                                                                    </a>
                                                                </li>
                                                            @endif
                                                            @if (! $alert->resolvido)
                                                                <li>
                                                                    <a class="dropdown-item"
                                                                       href="javascript:void(0);"
                                                                       wire:click="marcarResolvido({{ $alert->id }})">
                                                                        <i class="feather-check-circle me-3"></i>
                                                                        <span>Marcar como resolvido</span>
                                                                    </a>
                                                                </li>
                                                            @endif
                                                            <li class="dropdown-divider"></li>
                                                            <li>
                                                                <a class="dropdown-item text-danger"
                                                                   href="javascript:void(0);"
                                                                   @click.prevent="confirm('Eliminar este alerta?') && $wire.remove({{ $alert->id }})">
                                                                    <i class="feather-trash-2 me-3"></i>
                                                                    <span>Eliminar</span>
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    {{ $alerts->links() }}
                </div>
            @endif
        </div>

        <x-ui.footer />
    </div>
</div>