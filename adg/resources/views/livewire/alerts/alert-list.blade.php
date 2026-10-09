{{-- Lista de Alertas — Sprint 10 (resposta.md §10) --}}
{{-- Raiz unica obrigatoria (Livewire); content-area ocupa toda a largura (sem content-sidebar).
     Abertura/fecho da modal de detalhe trava/destrava o scroll do body --}}
<div class="d-flex flex-grow-1"
     x-on:modal-alerta.window="document.body.classList.toggle('modal-open', $event.detail.aberto === true)">
    <div class="content-area">
        <div class="content-area-header sticky-top">
            <div class="page-header-left d-flex align-items-center gap-2">
                <div class="dropdown">
                    <a href="javascript:void(0)" class="btn btn-light-brand dropdown-toggle" data-bs-toggle="dropdown" data-bs-offset="0,16">
                        <i class="feather-filter me-2"></i>
                        <span>Filtros</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <h6 class="dropdown-header">Tipo</h6>
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
                        <h6 class="dropdown-header">Estado</h6>
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
                                        <tr class="single-item {{ ! $alert->lido ? 'table-info' : '' }}"
                                            wire:key="alert-{{ $alert->id }}">
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
                                            <td>
                                                <div class="text-truncate-1-line" style="max-width: 400px; cursor: pointer;"
                                                     title="Ver detalhe do alerta"
                                                     wire:click="abrirAlerta({{ $alert->id }})">{{ $alert->mensagem }}</div>
                                            </td>
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
                                                                    @click.prevent="$store.confirmar.abrir({ titulo: 'Eliminar alerta', mensagem: 'Eliminar este alerta? Esta acção não pode ser desfeita.', confirmar: 'Eliminar' }).then(ok => ok && $wire.remove({{ $alert->id }}))">
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

        @include('livewire.alerts.detail-modal', ['alerta' => $alertaActivo, 'modalId' => 'modalDetalheAlertaLista'])
    </div>
</div>