{{-- Lista de Contratos — copiado de crm-prototipo/projects.html (Sprint 7) --}}
{{-- Raiz unica obrigatoria (Livewire); content-area ocupa toda a largura (sem content-sidebar) --}}
<div class="d-flex flex-grow-1">
    <div class="content-area">
        <div class="content-area-header sticky-top">
            <div class="page-header-left d-flex align-items-center gap-2">
                <a href="{{ route('contracts.create') }}" class="btn btn-primary btn-sm" wire:navigate>
                    <i class="feather-plus me-2"></i><span>Novo Contrato</span>
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
                        @foreach ($estados as $estado)
                            <a class="dropdown-item {{ $statusFilter === $estado->value ? 'active' : '' }}"
                               href="javascript:void(0)" wire:click="filterStatus('{{ $estado->value }}')">
                                <i class="{{ $estado->icon() }} me-2"></i>
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
            @if ($contracts->isEmpty())
                <div class="card stretch stretch-full">
                    <div class="card-body text-center py-5">
                        <i class="feather-shopping-cart fs-1 text-muted"></i>
                        <p class="text-muted mt-3 mb-3">Não existem contratos para os filtros seleccionados.</p>
                        <a href="{{ route('contracts.create') }}" class="btn btn-primary btn-sm" wire:navigate>
                            <i class="feather-plus me-2"></i>Novo Contrato
                        </a>
                    </div>
                </div>
            @else
                <div class="card stretch stretch-full">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle" id="contractList">
                                <thead>
                                    <tr>
                                        <th>Cliente</th>
                                        <th>Produto</th>
                                        <th class="text-end">Qtd</th>
                                        <th>Destino</th>
                                        <th>Contrato Nº</th>
                                        <th>Estado</th>
                                        <th>Atraso</th>
                                        <th class="text-end">Acções</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($contracts as $contract)
                                        <tr class="single-item {{ $contract->estado->value === 'cancelado' ? 'table-warning' : '' }}"
                                            wire:key="contract-{{ $contract->id }}">
                                            <td>
                                                @if ($contract->opportunity)
                                                    <a href="{{ route('opportunities.show', $contract->opportunity) }}" wire:navigate class="hstack gap-3">
                                                        <div class="avatar-image avatar-md">
                                                            <img src="{{ asset('assets/images/avatar/1.png') }}" alt="" class="img-fluid">
                                                        </div>
                                                        <div>
                                                            <span class="text-truncate-1-line fw-semibold">{{ $contract->opportunity?->nome ?? '—' }}</span>
                                                        </div>
                                                    </a>
                                                @else
                                                    —
                                                @endif
                                            </td>
                                            <td>
                                                @if ($contract->product)
                                                    {{ $contract->product->nome }}
                                                @else
                                                    —
                                                @endif
                                            </td>
                                            <td class="text-end">—</td>
                                            <td>—</td>
                                            <td>
                                                <a href="{{ route('contracts.show', $contract) }}" wire:navigate class="fw-bold">
                                                    {{ $contract->numero_contrato }}
                                                </a>
                                            </td>
                                            <td>
                                                <span class="badge {{ $contract->estado->badgeClass() }}">
                                                    <i class="{{ $contract->estado->icon() }} me-1"></i>
                                                    {{ $contract->estado->label() }}
                                                </span>
                                            </td>
                                            <td>
                                                @php
                                                    $atraso = $contract->data_fim->isPast() && $contract->estado->value === 'activo' ? 'Sim' : 'Não';
                                                    $dias = $contract->data_fim->isPast() ? $contract->data_fim->diffInDays(now()) : 0;
                                                @endphp
                                                <span class="badge {{ $atraso === 'Sim' ? 'bg-soft-danger text-danger' : 'bg-soft-secondary text-secondary' }}">
                                                    {{ $atraso }}
                                                    @if ($atraso === 'Sim') {{ $dias }} dias @endif
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                {{-- BLOCO 1: ícones directos (sem dropdown de 3 pontos) --}}
                                                <div class="action-icons">
                                                    <a href="{{ route('contracts.show', $contract) }}"
                                                       class="btn btn-icon" title="Ver detalhe" wire:navigate>
                                                        <i class="feather-eye"></i>
                                                    </a>
                                                    <a href="{{ route('contracts.edit', ['contract' => $contract->uuid]) }}"
                                                       class="btn btn-icon" title="Editar" wire:navigate>
                                                        <i class="feather-edit-3"></i>
                                                    </a>
                                                    @if ($contract->estado->value !== 'activo')
                                                        <button type="button"
                                                                class="btn btn-icon"
                                                                title="Marcar Activo"
                                                                wire:click="updateStatus({{ $contract->id }}, 'activo')">
                                                            <i class="feather-check-circle"></i>
                                                        </button>
                                                    @endif
                                                    @if (in_array($contract->estado->value, ['activo', 'expirado'], true))
                                                        <button type="button"
                                                                class="btn btn-icon"
                                                                title="Renovar"
                                                                wire:click="updateStatus({{ $contract->id }}, 'renovado')">
                                                            <i class="feather-refresh-cw"></i>
                                                        </button>
                                                    @endif
                                                    @if ($contract->estado->value !== 'cancelado')
                                                        <button type="button"
                                                                class="btn btn-icon text-danger"
                                                                title="Cancelar"
                                                                wire:click="cancelar({{ $contract->id }})">
                                                            <i class="feather-x-circle"></i>
                                                        </button>
                                                    @endif
                                                    <button type="button"
                                                            class="btn btn-icon text-danger"
                                                            title="Eliminar"
                                                            @click.prevent="$store.confirmar.abrir({ titulo: 'Eliminar contrato', mensagem: 'Eliminar este contrato? Esta acção não pode ser desfeita.', confirmar: 'Eliminar' }).then(ok => ok && $wire.remove({{ $contract->id }}))">
                                                        <i class="feather-trash-2"></i>
                                                    </button>
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