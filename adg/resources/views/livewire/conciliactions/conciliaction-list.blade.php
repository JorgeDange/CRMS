{{-- Lista de Conciliações — copiado de crm-prototipo/payment.html (Sprint 6) --}}
{{-- Raiz unica obrigatoria (Livewire); content-area ocupa toda a largura (sem content-sidebar) --}}
<div class="d-flex flex-grow-1">
    <div class="content-area">
        <div class="content-area-header sticky-top">
            <div class="page-header-left d-flex align-items-center gap-2">
                <a href="{{ route('conciliactions.create') }}" class="btn btn-primary btn-sm" wire:navigate>
                    <i class="feather-plus me-2"></i><span>Nova Conciliação</span>
                </a>
                <div class="dropdown">
                    <a href="javascript:void(0)" class="btn btn-light-brand dropdown-toggle" data-bs-toggle="dropdown" data-bs-offset="0,16">
                        <i class="feather-filter me-2"></i>
                        <span>Filtros</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <h6 class="dropdown-header">Classificação</h6>
                        <a class="dropdown-item {{ $classificacaoFilter === '' ? 'active' : '' }}"
                           href="javascript:void(0)" wire:click="filterClassificacao('')">
                            <i class="feather-filter me-2"></i>
                            <span>Todas as classificações</span>
                        </a>
                        @foreach ($classificacoes as $c)
                            <a class="dropdown-item {{ $classificacaoFilter === $c->value ? 'active' : '' }}"
                               href="javascript:void(0)" wire:click="filterClassificacao('{{ $c->value }}')">
                                <i class="{{ $c->icon() }} me-2"></i>
                                <span>{{ $c->label() }}</span>
                            </a>
                        @endforeach
                        <div class="dropdown-divider"></div>
                        <h6 class="dropdown-header">Estado</h6>
                        <a class="dropdown-item {{ $resolvidoFilter === '' ? 'active' : '' }}"
                           href="javascript:void(0)" wire:click="filterResolvido('')">
                            <i class="feather-filter me-2"></i>
                            <span>Todos os estados</span>
                        </a>
                        <a class="dropdown-item {{ $resolvidoFilter === 'true' ? 'active' : '' }}"
                           href="javascript:void(0)" wire:click="filterResolvido('true')">
                            <i class="feather-check-circle me-2"></i>
                            <span>Resolvidas</span>
                        </a>
                        <a class="dropdown-item {{ $resolvidoFilter === 'false' ? 'active' : '' }}"
                           href="javascript:void(0)" wire:click="filterResolvido('false')">
                            <i class="feather-clock me-2"></i>
                            <span>Pendentes</span>
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
            @if ($conciliactions->isEmpty())
                <div class="card stretch stretch-full">
                    <div class="card-body text-center py-5">
                        <i class="feather-dollar-sign fs-1 text-muted"></i>
                        <p class="text-muted mt-3 mb-3">Não existem conciliações para os filtros seleccionados.</p>
                        <a href="{{ route('conciliactions.create') }}" class="btn btn-primary btn-sm" wire:navigate>
                            <i class="feather-plus me-2"></i>Nova Conciliação
                        </a>
                    </div>
                </div>
            @else
                <div class="card stretch stretch-full">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle" id="conciliactionList">
                                <thead>
                                    <tr>
                                        <th>Data</th>
                                        <th>Cliente</th>
                                        <th>Comercial</th>
                                        <th class="text-end">Valor</th>
                                        <th>Classificação</th>
                                        <th>Resolvido</th>
                                        <th>Observações</th>
                                        <th class="text-end">Acções</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($conciliactions as $conciliaction)
                                        <tr class="single-item {{ $conciliaction->classificacao === \App\Enums\ConciliactionClassificacao::DISCORDANTE ? 'table-danger' : '' }}"
                                            wire:key="conciliaction-{{ $conciliaction->id }}">
                                            <td>{{ $conciliaction->data->format('d/m/Y') }}</td>
                                            <td>
                                                @if ($conciliaction->opportunity)
                                                    <a href="{{ route('opportunities.show', $conciliaction->opportunity) }}" wire:navigate class="hstack gap-3">
                                                        <div class="avatar-image avatar-md">
                                                            <img src="{{ asset('assets/images/avatar/1.png') }}" alt="" class="img-fluid">
                                                        </div>
                                                        <div>
                                                            <span class="text-truncate-1-line fw-semibold">{{ $conciliaction->opportunity?->nome ?? '—' }}</span>
                                                        </div>
                                                    </a>
                                                @else
                                                    —
                                                @endif
                                            </td>
                                            <td>
                                                @if ($conciliaction->commercial)
                                                    {{ $conciliaction->commercial->nome }}
                                                @else
                                                    —
                                                @endif
                                            </td>
                                            <td class="text-end fw-bold">
                                                {{ \App\Services\OpportunityService::formatKz($conciliaction->valor) }}
                                            </td>
                                            <td>
                                                <span class="badge {{ $conciliaction->classificacao->badgeClass() }}">
                                                    <i class="{{ $conciliaction->classificacao->icon() }} me-1"></i>
                                                    {{ $conciliaction->classificacao->label() }}
                                                </span>
                                            </td>
                                            <td>
                                                @if ($conciliaction->resolvido)
                                                    <span class="badge bg-soft-success text-success">
                                                        <i class="feather-check-circle me-1"></i>Sim
                                                    </span>
                                                @else
                                                    <span class="badge bg-soft-warning text-warning">
                                                        <i class="feather-clock me-1"></i>Pendente
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="text-truncate-1-line" style="max-width: 300px;">{{ $conciliaction->observacoes ?: '—' }}</div>
                                            </td>
                                            <td class="text-end">
                                                <div class="hstack gap-2 justify-content-end">
                                                    <a href="{{ route('conciliactions.edit', ['conciliaction' => $conciliaction->uuid]) }}"
                                                       class="avatar-text avatar-md" title="Editar" wire:navigate>
                                                        <i class="feather-edit-3"></i>
                                                    </a>
                                                    @if (! $conciliaction->resolvido)
                                                        <button type="button"
                                                                class="avatar-text avatar-md btn-success"
                                                                title="Marcar como resolvida"
                                                                wire:click="resolver({{ $conciliaction->id }})">
                                                            <i class="feather-check"></i>
                                                        </button>
                                                    @endif
                                                    <div class="dropdown">
                                                        <a href="javascript:void(0);" class="avatar-text avatar-md" data-bs-toggle="dropdown">
                                                            <i class="feather-more-vertical"></i>
                                                        </a>
                                                        <div class="dropdown-menu dropdown-menu-end">
                                                            @foreach ($classificacoes as $c)
                                                                <a class="dropdown-item"
                                                                   href="javascript:void(0);"
                                                                   wire:click="classificar({{ $conciliaction->id }}, '{{ $c->value }}')">
                                                                    <i class="{{ $c->icon() }} me-2"></i>
                                                                    {{ $c->label() }}
                                                                </a>
                                                            @endforeach
                                                            <div class="dropdown-divider"></div>
                                                            <a class="dropdown-item text-danger"
                                                               href="javascript:void(0);"
                                                                @click.prevent="$store.confirmar.abrir({ titulo: 'Eliminar conciliação', mensagem: 'Eliminar esta conciliação? Esta acção não pode ser desfeita.', confirmar: 'Eliminar' }).then(ok => ok && $wire.remove({{ $conciliaction->id }}))">
                                                                <i class="feather-trash-2 me-2"></i>Eliminar
                                                            </a>
                                                        </div>
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