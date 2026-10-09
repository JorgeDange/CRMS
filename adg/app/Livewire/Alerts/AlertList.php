<?php

namespace App\Livewire\Alerts;

use App\Enums\AlertType;
use App\Models\Alert;
use Livewire\Component;

class AlertList extends Component
{
    public string $search = '';

    public string $tipoFilter = '';

    public string $statusFilter = ''; // '', 'nao_lido', 'lido', 'resolvido'

    public string $sort = 'desc';

    public ?int $alertaActivoId = null;

    public function mount(): void
    {
        $tipo = request()->query('tipo');

        if (is_string($tipo) && in_array($tipo, array_column(AlertType::cases(), 'value'), true)) {
            $this->tipoFilter = $tipo;
        }

        $status = request()->query('status');

        if (is_string($status) && in_array($status, ['nao_lido', 'lido', 'resolvido'], true)) {
            $this->statusFilter = $status;
        }
    }

    public function filterTipo(string $tipo): void
    {
        $this->tipoFilter = $this->tipoFilter === $tipo ? '' : $tipo;
    }

    public function filterStatus(string $status): void
    {
        $this->statusFilter = $this->statusFilter === $status ? '' : $status;
    }

    public function clearFilters(): void
    {
        $this->tipoFilter = '';
        $this->statusFilter = '';
        $this->search = '';
    }

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
        $alert = Alert::find($alertId);

        if (! $alert) {
            return;
        }

        $alert->markAsRead();
    }

    public function marcarResolvido(int $alertId): void
    {
        $alert = Alert::find($alertId);

        if (! $alert) {
            return;
        }

        $alert->markAsResolved();
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
        $alerts = Alert::with('user')
            ->when($this->search !== '', function ($query): void {
                $like = '%'.$this->search.'%';
                $query->where(fn ($q) => $q
                    ->where('mensagem', 'like', $like)
                    ->orWhereHas('user', fn ($q2) => $q2->where('name', 'like', $like)));
            })
            ->when($this->tipoFilter !== '', fn ($query) => $query->where('tipo', $this->tipoFilter))
            ->when($this->statusFilter !== '', function ($query): void {
                if ($this->statusFilter === 'nao_lido') {
                    $query->where('lido', false);
                } elseif ($this->statusFilter === 'lido') {
                    $query->where('lido', true)->where('resolvido', false);
                } elseif ($this->statusFilter === 'resolvido') {
                    $query->where('resolvido', true);
                }
            })
            ->orderBy('created_at', $this->sort === 'asc' ? 'asc' : 'desc')
            ->paginate(20);

        $tipos = AlertType::cases();

        return view('livewire.alerts.alert-list', [
            'alerts' => $alerts,
            'tipos' => $tipos,
            'alertaActivo' => $this->alertaActivoId !== null
                ? Alert::find($this->alertaActivoId)
                : null,
        ]);
    }
}
