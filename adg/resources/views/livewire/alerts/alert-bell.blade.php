{{-- Sino de notificacoes do header — markup original (AlertsTest valida o <a>)
     + item clicavel que abre a modal de detalhe (Sprint 14) --}}
<div class="dropdown nxl-h-item"
     x-on:modal-alerta.window="document.body.classList.toggle('modal-open', $event.detail.aberto === true)">
    {{-- href sem navegacao: com route() o dropdown nunca abria (prototipo usa href="#").
         Sem data-bs-auto-close: o dropdown fecha sozinho ao escolher um alerta. --}}
    <a href="javascript:void(0);" class="nxl-head-link me-0 position-relative" data-bs-toggle="dropdown" role="button">
        <i class="feather-bell fs-18 text-dark"></i>
        @if ($unreadCount > 0)
            <span class="badge bg-danger nxl-h-badge position-absolute top-0 start-100 translate-middle">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
        @endif
    </a>
    <div class="dropdown-menu dropdown-menu-end nxl-h-dropdown nxl-notifications-menu">
        <div class="d-flex justify-content-between align-items-center notifications-head">
            <h6 class="fw-bold text-dark mb-0">Notificações</h6>
            <form method="POST" action="{{ route('alerts.markAll') }}" class="ms-auto">
                @csrf
                <button type="submit" class="fs-11 text-success text-end border-0 bg-transparent p-0">
                    <i class="feather-check"></i>
                    <span>Marcar todas como lidas</span>
                </button>
            </form>
        </div>
        @if ($recentAlerts->isEmpty())
            <div class="text-center notifications-footer p-3">
                <i class="feather-bell fs-1 mb-3 text-muted"></i>
                <p class="text-muted mb-0">Sem notificações novas.</p>
            </div>
        @else
            @foreach ($recentAlerts as $alert)
                <div class="notifications-item" wire:key="notif-{{ $alert->id }}">
                    <div class="notifications-desc">
                        <a href="javascript:void(0);" class="font-body text-truncate-2-line"
                           wire:click="abrirAlerta({{ $alert->id }})">
                            <span class="fw-semibold text-dark">{{ $alert->tipo->label() }}</span>
                            <span class="text-muted ms-2">{{ $alert->mensagem }}</span>
                        </a>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="notifications-date text-muted border-bottom border-bottom-dashed">
                                {{ $alert->created_at?->diffForHumans() }}
                            </div>
                            @if (! $alert->lido)
                                <span class="d-block wd-8 ht-8 rounded-circle bg-gray-300" title="Não lido"></span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
        <div class="text-center notifications-footer">
            <a href="{{ route('alerts') }}" class="fs-13 fw-semibold text-dark" wire:navigate>Todas as notificações</a>
        </div>
    </div>

    @include('livewire.alerts.detail-modal', ['alerta' => $alertaActivo, 'modalId' => 'modalDetalheAlertaSino'])
</div>
