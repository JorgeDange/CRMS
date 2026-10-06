<?php

namespace App\Livewire\Commercials;

use App\Models\Commercial;
use Livewire\Component;

class CommercialList extends Component
{
    public string $search = '';

    public string $statusFilter = '';

    public string $sort = 'desc';

    public function mount(): void
    {
        $status = request()->query('status');

        if (is_string($status) && in_array($status, ['activo', 'inactivo'], true)) {
            $this->statusFilter = $status;
        }
    }

    public function filterStatus(string $status): void
    {
        $this->statusFilter = $this->statusFilter === $status ? '' : $status;
    }

    public function clearFilters(): void
    {
        $this->statusFilter = '';
        $this->search = '';
    }

    public function toggleStatus(int $commercialId): void
    {
        $commercial = Commercial::find($commercialId);

        if (! $commercial) {
            return;
        }

        $commercial->activo = ! $commercial->activo;
        $commercial->save();
    }

    public function remove(int $commercialId): void
    {
        $commercial = Commercial::find($commercialId);

        if (! $commercial) {
            return;
        }

        $commercial->delete();

        $this->dispatch('commercial-removed');
    }

    public function render()
    {
        $comercials = Commercial::query()
            ->when($this->search !== '', function ($query): void {
                $like = '%'.$this->search.'%';
                $query->where(fn ($q) => $q
                    ->where('nome', 'like', $like)
                    ->orWhere('descricao', 'like', $like));
            })
            ->when($this->statusFilter !== '', fn ($query) => $query->where('activo', $this->statusFilter === 'activo'))
            ->orderBy('nome', $this->sort === 'asc' ? 'asc' : 'desc')
            ->get();

        return view('livewire.commercials.commercial-list', [
            'comerciais' => $comercials,
        ]);
    }
}
