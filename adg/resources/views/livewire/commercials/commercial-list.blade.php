{{-- Lista de Comerciais — padrão Sprint 9 --}}
<div class="d-flex flex-grow-1" x-data="{ sidebarOpen: false }">
    <div class="content-sidebar content-sidebar-md" :class="sidebarOpen && 'app-sidebar-open'">
        <div class="content-sidebar-header bg-white sticky-top hstack justify-content-between">
            <h4 class="fw-bolder mb-0">Comerciais</h4>
            <a href="javascript:void(0);" class="app-sidebar-close-trigger d-flex" @click="sidebarOpen = false">
                <i class="feather-x"></i>
            </a>
        </div>
        <div class="content-sidebar-header">
            <a href="{{ route('commercials.create') }}" class="btn btn-primary w-100" wire:navigate>
                <i class="feather-plus me-2"></i>
                <span>Novo Comercial</span>
            </a>
        </div>
        <div class="content-sidebar-body">
            <ul class="nav flex-column nxl-content-sidebar-item">
                <li class="nav-item">
                    <a class="nav-link {{ $statusFilter === '' ? 'active' : '' }}"
                       href="javascript:void(0);" wire:click="filterStatus('')">
                        <i class="feather-list"></i>
                        <span>Todos</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $statusFilter === 'activo' ? 'active' : '' }}"
                       href="javascript:void(0);" wire:click="filterStatus('activo')">
                        <i class="feather-check-circle"></i>
                        <span>Activos</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $statusFilter === 'inactivo' ? 'active' : '' }}"
                       href="javascript:void(0);" wire:click="filterStatus('inactivo')">
                        <i class="feather-x-circle"></i>
                        <span>Inactivos</span>
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
                        <a class="dropdown-item {{ $statusFilter === '' ? 'active' : '' }}"
                           href="javascript:void(0)" wire:click="filterStatus('')">
                            <i class="feather-filter me-2"></i>
                            <span>Todos</span>
                        </a>
                        <a class="dropdown-item {{ $statusFilter === 'activo' ? 'active' : '' }}"
                           href="javascript:void(0)" wire:click="filterStatus('activo')">
                            <i class="feather-check-circle me-2"></i>
                            <span>Activos</span>
                        </a>
                        <a class="dropdown-item {{ $statusFilter === 'inactivo' ? 'active' : '' }}"
                           href="javascript:void(0)" wire:click="filterStatus('inactivo')">
                            <i class="feather-x-circle me-2"></i>
                            <span>Inactivos</span>
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
                            {{ $sort === 'asc' ? 'A-Z' : 'Z-A' }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item {{ $sort === 'asc' ? 'active' : '' }}"
                                   href="javascript:void(0)" wire:click="$set('sort', 'asc')">A-Z</a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ $sort === 'desc' ? 'active' : '' }}"
                                   href="javascript:void(0)" wire:click="$set('sort', 'desc')">Z-A</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-area-body">
            @if ($comerciais->isEmpty())
                <div class="card stretch stretch-full">
                    <div class="card-body text-center py-5">
                        <i class="feather-users fs-1 text-muted"></i>
                        <p class="text-muted mt-3 mb-3">Não existem comerciais para os filtros seleccionados.</p>
                        <a href="{{ route('commercials.create') }}" class="btn btn-primary btn-sm" wire:navigate>
                            <i class="feather-plus me-2"></i>Novo Comercial
                        </a>
                    </div>
                </div>
            @else
                <div class="card stretch stretch-full">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover" id="commercialList">
                                <thead>
                                    <tr>
                                        <th class="wd-30">
                                            <div class="btn-group mb-1">
                                                <div class="custom-control custom-checkbox ms-1">
                                                    <input type="checkbox" class="custom-control-input" id="checkAllCommercial">
                                                    <label class="custom-control-label" for="checkAllCommercial"></label>
                                                </div>
                                            </div>
                                        </th>
                                        <th>Nome</th>
                                        <th>Descrição</th>
                                        <th>Estado</th>
                                        <th class="text-end">Acções</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($comerciais as $comercial)
                                        <tr class="single-item wire:key=\"commercial-{{ $comercial->id }}\" {{ ! $comercial->activo ? 'table-warning' : '' }}">
                                            <td>
                                                <div class="item-checkbox ms-1">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input checkbox" id="checkBox_{{ $comercial->id }}">
                                                        <label class="custom-control-label" for="checkBox_{{ $comercial->id }}"></label>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="javascript:void(0);" class="hstack gap-3">
                                                    <div class="avatar-image avatar-md bg-primary text-white">
                                                        <i class="feather-users"></i>
                                                    </div>
                                                    <div>
                                                        <span class="text-truncate-1-line fw-semibold">{{ $comercial->nome }}</span>
                                                    </div>
                                                </a>
                                            </td>
                                            <td class="text-truncate-1-line" style="max-width: 300px;">{{ $comercial->descricao ?? '—' }}</td>
                                            <td>
                                                @if ($comercial->activo)
                                                    <span class="badge bg-soft-success text-success">
                                                        <i class="feather-check-circle me-1"></i>Activo
                                                    </span>
                                                @else
                                                    <span class="badge bg-soft-warning text-warning">
                                                        <i class="feather-x-circle me-1"></i>Inactivo
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <div class="hstack gap-2 justify-content-end">
                                                    <a href="{{ route('commercials.edit', ['commercial' => $comercial->uuid]) }}"
                                                       class="avatar-text avatar-md" title="Editar" wire:navigate>
                                                        <i class="feather-edit-3"></i>
                                                    </a>
                                                    <button type="button"
                                                            class="avatar-text avatar-md {{ $comercial->activo ? 'btn-warning' : 'btn-success' }}"
                                                            title="{{ $comercial->activo ? 'Desactivar' : 'Activar' }}"
                                                            wire:click="toggleStatus({{ $comercial->id }})">
                                                        <i class="feather-{{ $comercial->activo ? 'pause' : 'play' }}"></i>
                                                    </button>
                                                    <div class="dropdown">
                                                        <a href="javascript:void(0);" class="avatar-text avatar-md" data-bs-toggle="dropdown">
                                                            <i class="feather-more-horizontal"></i>
                                                        </a>
                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                            <li>
                                                                <a class="dropdown-item"
                                                                   href="{{ route('commercials.edit', ['commercial' => $comercial->uuid]) }}"
                                                                   wire:navigate>
                                                                    <i class="feather-edit-3 me-3"></i>
                                                                    <span>Editar</span>
                                                                </a>
                                                            </li>
                                                            <li class="dropdown-divider"></li>
                                                            <li>
                                                                <a class="dropdown-item text-danger"
                                                                   href="javascript:void(0);"
                                                                   @click.prevent="confirm('Eliminar este comercial?') && $wire.remove({{ $comercial->id }})">
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
                </div>
            @endif
        </div>

        <x-ui.footer />
    </div>
</div>