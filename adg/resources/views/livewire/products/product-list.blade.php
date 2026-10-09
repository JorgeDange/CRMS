{{-- Lista de Produtos — copiado de crm-prototipo/customers.html (Sprint 8) --}}
{{-- Raiz unica obrigatoria (Livewire); content-area ocupa toda a largura (sem content-sidebar) --}}
<div class="d-flex flex-grow-1">
    <div class="content-area">
        <div class="content-area-header sticky-top">
            <div class="page-header-left d-flex align-items-center gap-2">
                <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm" wire:navigate>
                    <i class="feather-plus me-2"></i><span>Novo Produto</span>
                </a>
                <div class="dropdown">
                    <a href="javascript:void(0)" class="btn btn-light-brand dropdown-toggle" data-bs-toggle="dropdown" data-bs-offset="0,16">
                        <i class="feather-filter me-2"></i>
                        <span>Filtros</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <h6 class="dropdown-header">Estado</h6>
                        <a class="dropdown-item {{ $statusFilter === '' ? 'active' : '' }}"
                           href="javascript:void(0)" wire:click="filterStatus('')">
                            <i class="feather-filter me-2"></i>
                            <span>Todos os estados</span>
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
                        <div class="dropdown-divider"></div>
                        <h6 class="dropdown-header">Comercial</h6>
                        <a class="dropdown-item {{ $commercialFilter === '' ? 'active' : '' }}"
                           href="javascript:void(0)" wire:click="filterCommercial('')">
                            <i class="feather-filter me-2"></i>
                            <span>Todos os comerciais</span>
                        </a>
                        @foreach ($comerciais as $comercial)
                            <a class="dropdown-item {{ $commercialFilter === (string) $comercial->id ? 'active' : '' }}"
                               href="javascript:void(0)" wire:click="filterCommercial('{{ $comercial->id }}')">
                                <span>{{ $comercial->nome }}</span>
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
            @if ($products->isEmpty())
                <div class="card stretch stretch-full">
                    <div class="card-body text-center py-5">
                        <i class="feather-package fs-1 text-muted"></i>
                        <p class="text-muted mt-3 mb-3">Não existem produtos para os filtros seleccionados.</p>
                        <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm" wire:navigate>
                            <i class="feather-plus me-2"></i>Novo Produto
                        </a>
                    </div>
                </div>
            @else
                <div class="card stretch stretch-full">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover" id="productList">
                                <thead>
                                    <tr>
                                        <th class="wd-30">
                                            <div class="btn-group mb-1">
                                                <div class="custom-control custom-checkbox ms-1">
                                                    <input type="checkbox" class="custom-control-input" id="checkAllProduct">
                                                    <label class="custom-control-label" for="checkAllProduct"></label>
                                                </div>
                                            </div>
                                        </th>
                                        <th>Código</th>
                                        <th>Nome</th>
                                        <th>Nº Série</th>
                                        <th>Valor</th>
                                        <th>Comercial</th>
                                        <th>Estado</th>
                                        <th class="text-end">Acções</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($products as $product)
                                        <tr class="single-item {{ ! $product->activo ? 'table-warning' : '' }}"
                                            wire:key="product-{{ $product->id }}">
                                            <td>
                                                <div class="item-checkbox ms-1">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input checkbox" id="checkBox_{{ $product->id }}">
                                                        <label class="custom-control-label" for="checkBox_{{ $product->id }}"></label>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $product->codigo }}</td>
                                            <td>
                                                <a href="javascript:void(0);" class="hstack gap-3">
                                                    <div class="avatar-image avatar-md bg-primary text-white">
                                                        <i class="feather-package"></i>
                                                    </div>
                                                    <div>
                                                        <span class="text-truncate-1-line fw-semibold">{{ $product->nome }}</span>
                                                    </div>
                                                </a>
                                            </td>
                                            <td>{{ $product->numero_serie ?? '—' }}</td>
                                            <td class="fw-bold">{{ \App\Services\OpportunityService::formatKz($product->valor) }}</td>
                                            <td>
                                                @if ($product->commercial)
                                                    {{ $product->commercial->nome }}
                                                @else
                                                    —
                                                @endif
                                            </td>
                                            <td>
                                                @if ($product->activo)
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
                                                    <a href="{{ route('products.edit', ['produto' => $product->uuid]) }}"
                                                       class="avatar-text avatar-md" title="Editar" wire:navigate>
                                                        <i class="feather-edit-3"></i>
                                                    </a>
                                                    <button type="button"
                                                            class="avatar-text avatar-md {{ $product->activo ? 'btn-warning' : 'btn-success' }}"
                                                            title="{{ $product->activo ? 'Desactivar' : 'Activar' }}"
                                                            wire:click="toggleStatus({{ $product->id }})">
                                                        <i class="feather-{{ $product->activo ? 'pause' : 'play' }}"></i>
                                                    </button>
                                                    <div class="dropdown">
                                                        <a href="javascript:void(0);" class="avatar-text avatar-md" data-bs-toggle="dropdown">
                                                            <i class="feather-more-horizontal"></i>
                                                        </a>
                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                            <li>
                                                                <a class="dropdown-item"
                                                                   href="{{ route('products.edit', ['produto' => $product->uuid]) }}"
                                                                   wire:navigate>
                                                                    <i class="feather-edit-3 me-3"></i>
                                                                    <span>Editar</span>
                                                                </a>
                                                            </li>
                                                            <li class="dropdown-divider"></li>
                                                            <li>
                                                                <a class="dropdown-item text-danger"
                                                                   href="javascript:void(0);"
                                                                    @click.prevent="$store.confirmar.abrir({ titulo: 'Eliminar produto', mensagem: 'Eliminar este produto? Esta acção não pode ser desfeita.', confirmar: 'Eliminar' }).then(ok => ok && $wire.remove({{ $product->id }}))">
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