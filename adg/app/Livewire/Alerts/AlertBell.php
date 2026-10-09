<?php

namespace App\Livewire\Alerts;

use App\Models\Alert;
use Livewire\Component;

/**
 * Sino de notificacoes do header — badge, dropdown dos 5 alertas recentes
 * e modal de detalhe com as accoes (Sprint 14).
 */
class AlertBell extends Component
{
    public ?int $alertaActivoId = null;

    public function abrirAlerta(int $alertId): void
    {
        if (! Alert::query()->whereKey($alertId)->exists()) {
            return;
        }

        $this->alertaActivoId = $alertId;
        $this->dispatch('modal-alerta', aberto: true);
    }

    public function fecharDetalhe(): void
    {
        if ($this->alertaActivoId === null) {
            return;
        }

        $this->alertaActivoId = null;
        $this->dispatch('modal-alerta', aberto: false);
    }

    public function marcarLido(int $alertId): void
    {
        Alert::find($alertId)?->markAsRead();
    }

    public function marcarResolvido(int $alertId): void
    {
        Alert::find($alertId)?->markAsResolved();
    }

    public function remove(int $alertId): void
    {
        $alert = Alert::find($alertId);

        if (! $alert) {
            return;
        }

        $alert->delete();

        if ($this->alertaActivoId === $alertId) {
            $this->fecharDetalhe();
        }

        $this->dispatch('alert-removed');
    }

    public function render()
    {
        $userId = auth()->id();

        return view('livewire.alerts.alert-bell', [
            'unreadCount' => Alert::where('user_id', $userId)
                ->where('lido', false)
                ->where('resolvido', false)
                ->count(),
            'recentAlerts' => Alert::where('user_id', $userId)
                ->where('resolvido', false)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get(),
            'alertaActivo' => $this->alertaActivoId !== null
                ? Alert::find($this->alertaActivoId)
                : null,
        ]);
    }
}
