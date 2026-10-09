{{-- Modal de detalhe do alerta — mesmo padrao do modal da Agenda (Sprint 14):
     markup-puro aberto/fechado pelo estado Livewire, backdrop com blur e o
     scroll do body travado pelo listener x-on na raiz do componente. --}}
@php
    $linkRegisto = null;
    $modalId = $modalId ?? 'modalDetalheAlerta';

    if ($alerta !== null && $alerta->entidade && $alerta->entidade_id) {
        $registo = match ($alerta->entidade) {
            'opportunities' => \App\Models\Opportunity::find($alerta->entidade_id),
            'contracts' => \App\Models\Contract::find($alerta->entidade_id),
            'conciliactions' => \App\Models\Conciliaction::find($alerta->entidade_id),
            default => null,
        };

        $linkRegisto = match ($alerta->entidade) {
            'opportunities' => $registo ? route('opportunities.show', $registo) : null,
            'contracts' => $registo ? route('contracts.show', $registo) : null,
            'conciliactions' => $registo ? route('conciliactions.edit', ['conciliaction' => $registo->uuid]) : null,
            default => null,
        };
    }
@endphp
<div class="modal fade {{ $alerta ? 'show d-block' : '' }}" id="{{ $modalId }}" tabindex="-1"
     role="dialog" aria-modal="true" aria-hidden="{{ $alerta ? 'false' : 'true' }}"
     style="{{ $alerta ? '' : 'display: none;' }}"
     wire:click.self="fecharDetalhe">
    @if ($alerta)
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="d-flex align-items-center gap-2 flex-wrap me-3">
                        <span class="badge {{ $alerta->tipo->badgeClass() }}">
                            <i class="{{ $alerta->tipo->icon() }} me-1"></i>{{ $alerta->tipo->label() }}
                        </span>
                        <h5 class="modal-title mb-0">Detalhe do alerta</h5>
                    </div>
                    <button type="button" class="btn-close" wire:click="fecharDetalhe" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <span class="fs-11 fw-semibold text-muted text-uppercase d-block mb-1">Estado</span>
                            @if ($alerta->resolvido)
                                <span class="badge bg-soft-success text-success">
                                    <i class="feather-check-circle me-1"></i>Resolvido
                                </span>
                            @elseif ($alerta->lido)
                                <span class="badge bg-soft-info text-info">
                                    <i class="feather-check me-1"></i>Lido
                                </span>
                            @else
                                <span class="badge bg-soft-warning text-warning">
                                    <i class="feather-alert-triangle me-1"></i>Não lido
                                </span>
                            @endif
                        </div>
                        <div class="col-sm-6">
                            <span class="fs-11 fw-semibold text-muted text-uppercase d-block mb-1">Criado por</span>
                            <span class="fs-13 fw-semibold">{{ $alerta->user?->name ?? '—' }}</span>
                        </div>
                        <div class="col-sm-6">
                            <span class="fs-11 fw-semibold text-muted text-uppercase d-block mb-1">Criado em</span>
                            <span class="fs-13 fw-semibold">
                                {{ $alerta->created_at?->format('d/m/Y H:i') ?? '—' }}
                                <span class="fw-normal text-muted">({{ $alerta->created_at?->diffForHumans() }})</span>
                            </span>
                        </div>
                        <div class="col-sm-6">
                            <span class="fs-11 fw-semibold text-muted text-uppercase d-block mb-1">Prazo</span>
                            <span class="fs-13 fw-semibold">{{ $alerta->prazo?->format('d/m/Y') ?? '—' }}</span>
                        </div>
                        <div class="col-12">
                            <span class="fs-11 fw-semibold text-muted text-uppercase d-block mb-1">Mensagem</span>
                            <p class="fs-13 fw-normal mb-0" style="white-space: pre-wrap;">{{ $alerta->mensagem }}</p>
                        </div>
                        @if ($alerta->entidade)
                            <div class="col-12">
                                <span class="fs-11 fw-semibold text-muted text-uppercase d-block mb-1">Entidade</span>
                                <span class="d-inline-flex align-items-center flex-wrap gap-2">
                                    @if ($alerta->entidade === 'opportunities')
                                        <span class="badge bg-soft-primary text-primary">Oportunidade</span>
                                    @elseif ($alerta->entidade === 'contracts')
                                        <span class="badge bg-soft-primary text-primary">Contrato</span>
                                    @elseif ($alerta->entidade === 'conciliactions')
                                        <span class="badge bg-soft-secondary text-secondary">Conciliação</span>
                                    @else
                                        <span class="badge bg-soft-secondary text-secondary">{{ $alerta->entidade }}</span>
                                    @endif
                                    @if ($linkRegisto)
                                        <a href="{{ $linkRegisto }}" class="fs-13 fw-semibold" wire:navigate>
                                            Ver registo <i class="feather-arrow-up-right fs-12"></i>
                                        </a>
                                    @endif
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    @if (! $alerta->lido)
                        <button type="button" class="btn btn-outline-primary btn-sm" wire:click="marcarLido({{ $alerta->id }})">
                            <i class="feather-check me-2"></i><span>Marcar como lido</span>
                        </button>
                    @endif
                    @if (! $alerta->resolvido)
                        <button type="button" class="btn btn-outline-success btn-sm" wire:click="marcarResolvido({{ $alerta->id }})">
                            <i class="feather-check-circle me-2"></i><span>Marcar como resolvido</span>
                        </button>
                    @endif
                    <button type="button" class="btn btn-outline-danger btn-sm"
                            @click.prevent="$store.confirmar.abrir({ titulo: 'Eliminar alerta', mensagem: 'Eliminar este alerta? Esta acção não pode ser desfeita.', confirmar: 'Eliminar' }).then(ok => ok && $wire.remove({{ $alerta->id }}))">
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
<div class="modal-backdrop fade {{ $alerta ? 'show' : '' }}"
     style="{{ $alerta ? 'backdrop-filter: blur(3px); -webkit-backdrop-filter: blur(3px);' : 'display: none;' }}"
     wire:click="fecharDetalhe"></div>
