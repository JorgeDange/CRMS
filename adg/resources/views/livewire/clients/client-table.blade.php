{{-- Tabela de Clientes — Sprint 13 (seguir.md §6). Base visual: customers.html --}}
{{-- Raiz unica obrigatoria (Livewire); sem content-sidebar vertical --}}
<div class="d-flex flex-grow-1">
    <div class="content-area">
        <div class="content-area-header sticky-top">
            <div class="page-header-left d-flex align-items-center gap-3">
                <h5 class="mb-0 text-dark">Lista de Clientes</h5>
                <div class="dropdown">
                    <a href="javascript:void(0)" class="btn btn-light-brand dropdown-toggle" data-bs-toggle="dropdown" data-bs-offset="0,16">
                        <i class="feather-filter me-2"></i>
                        <span>Filtros</span>
                        @php
                            $filtrosActivos = array_filter([
                                $kamFilter,
                                $comprasFilter,
                                $conciliacaoFilter,
                                $contratoFilter,
                            ]);
                        @endphp
                        @if ($search !== '' || $filtrosActivos !== [])
                            <span class="badge bg-danger rounded-pill ms-1">{{ ($search !== '' ? 1 : 0) + count($filtrosActivos) }}</span>
                        @endif
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <h6 class="dropdown-header">KAM</h6>
                        <a class="dropdown-item {{ $kamFilter === '' ? 'active' : '' }}"
                           href="javascript:void(0)" wire:click="$set('kamFilter', '')">
                            <span>Todos os KAMs</span>
                        </a>
                        @foreach ($kams as $kam)
                            <a class="dropdown-item {{ $kamFilter === (string) $kam->id ? 'active' : '' }}"
                               href="javascript:void(0)" wire:click="$set('kamFilter', '{{ $kam->id }}')">
                                <i class="feather-user me-2"></i>
                                <span>{{ $kam->nome }}</span>
                            </a>
                        @endforeach
                        <div class="dropdown-divider"></div>
                        <h6 class="dropdown-header">Compras</h6>
                        <a class="dropdown-item {{ $comprasFilter === '' ? 'active' : '' }}"
                           href="javascript:void(0)" wire:click="$set('comprasFilter', '')">Todas</a>
                        <a class="dropdown-item {{ $comprasFilter === 'sim' ? 'active' : '' }}"
                           href="javascript:void(0)" wire:click="$set('comprasFilter', 'sim')">Sim</a>
                        <a class="dropdown-item {{ $comprasFilter === 'nao' ? 'active' : '' }}"
                           href="javascript:void(0)" wire:click="$set('comprasFilter', 'nao')">Não</a>
                        <div class="dropdown-divider"></div>
                        <h6 class="dropdown-header">Conciliação</h6>
                        <a class="dropdown-item {{ $conciliacaoFilter === '' ? 'active' : '' }}"
                           href="javascript:void(0)" wire:click="$set('conciliacaoFilter', '')">Todas</a>
                        @foreach ($conciliacaoEstados as $estado)
                            <a class="dropdown-item {{ $conciliacaoFilter === $estado->value ? 'active' : '' }}"
                               href="javascript:void(0)" wire:click="$set('conciliacaoFilter', '{{ $estado->value }}')">
                                <span>{{ $estado->label() }}</span>
                            </a>
                        @endforeach
                        <div class="dropdown-divider"></div>
                        <h6 class="dropdown-header">Contratos</h6>
                        <a class="dropdown-item {{ $contratoFilter === '' ? 'active' : '' }}"
                           href="javascript:void(0)" wire:click="$set('contratoFilter', '')">Todos</a>
                        @foreach ($contratoEstados as $estado)
                            <a class="dropdown-item {{ $contratoFilter === $estado->value ? 'active' : '' }}"
                               href="javascript:void(0)" wire:click="$set('contratoFilter', '{{ $estado->value }}')">
                                <span>{{ $estado->label() }}</span>
                            </a>
                        @endforeach
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item text-danger" href="javascript:void(0)" wire:click="resetFilters">
                            <i class="feather-x me-2"></i>
                            <span>Limpar filtros</span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="page-header-right ms-auto">
                <input type="search" class="form-control form-control-sm" placeholder="Pesquisar nome ou código..."
                       style="min-width: 240px;"
                       wire:model.live.debounce.300ms="search">
            </div>
        </div>

        <div class="content-area-body">
            <div class="card stretch stretch-full">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="customerList">
                            <thead class="bg-light">
                                <tr>
                                    <th>Nº</th>
                                    <th wire:click="sortBy('codigo')" style="cursor: pointer;" title="Ordenar por código">
                                        CÓDIGO
                                        @if ($sortField === 'codigo')
                                            <i class="feather-{{ $sortDirection === 'asc' ? 'chevron-up' : 'chevron-down' }} fs-12"></i>
                                        @endif
                                    </th>
                                    <th wire:click="sortBy('nome')" style="cursor: pointer;" title="Ordenar por cliente">
                                        CLIENTE
                                        @if ($sortField === 'nome')
                                            <i class="feather-{{ $sortDirection === 'asc' ? 'chevron-up' : 'chevron-down' }} fs-12"></i>
                                        @endif
                                    </th>
                                    <th>KAM</th>
                                    <th>COMPRAS</th>
                                    <th>ASSINATURA DO DIRECTOR</th>
                                    <th>ENVIADO/ENTREGUE</th>
                                    <th>CONCILIAÇÃO</th>
                                    <th>CONTRATOS</th>
                                    <th class="text-end">ACÇÕES</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($clients as $index => $client)
                                    <tr class="single-item" wire:key="client-{{ $client->id }}">
                                        <td>{{ ($clients->currentPage() - 1) * $clients->perPage() + $index + 1 }}</td>
                                        <td>{{ $client->codigo ?? '—' }}</td>
                                        <td><span class="text-truncate-1-line fw-semibold" style="max-width: 240px;">{{ $client->nome }}</span></td>
                                        <td>{{ $client->commercial?->nome ?? '—' }}</td>
                                        <td>
                                            @if ($client->tem_compras)
                                                <span class="badge bg-soft-success text-success">SIM</span>
                                            @else
                                                <span class="badge bg-soft-secondary text-secondary">NÃO</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($client->assinatura_director)
                                                <span class="badge bg-soft-success text-success">Sim</span>
                                            @else
                                                <span class="badge bg-soft-secondary text-secondary">Não</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($client->enviado_entregue)
                                                <span class="badge bg-soft-success text-success">Sim</span>
                                            @else
                                                <span class="badge bg-soft-secondary text-secondary">Não</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($client->conciliacao_estado)
                                                <span class="badge {{ $client->conciliacao_estado->badgeClass() }}">
                                                    {{ $client->conciliacao_estado->label() }}
                                                </span>
                                            @else
                                                <span class="badge bg-soft-secondary text-secondary">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($client->contrato_estado)
                                                <span class="badge {{ $client->contrato_estado->badgeClass() }}">
                                                    {{ $client->contrato_estado->label() }}
                                                </span>
                                            @else
                                                <span class="badge bg-soft-secondary text-secondary">—</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('opportunities.show', $client) }}" wire:navigate
                                               class="btn btn-sm btn-light" title="Ver detalhe">
                                                <i class="feather-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10">
                                            <div class="text-center py-5">
                                                <i class="feather-users fs-1 text-muted"></i>
                                                <p class="text-muted mt-3 mb-0">Nenhum cliente encontrado</p>
                                                <p class="text-muted mb-0">Ainda não existem clientes aprovados no sistema.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($clients->hasPages())
                    <div class="card-footer">
                        {{ $clients->links() }}
                    </div>
                @endif
            </div>
        </div>

        <x-ui.footer />
    </div>
</div>
