<?php

namespace App\Livewire\Conciliactions;

use App\Enums\ConciliactionClassificacao;
use App\Models\Conciliaction;
use App\Services\ConciliactionService;
use Livewire\Component;

class ConciliactionList extends Component
{
    public string $search = '';

    public string $classificacaoFilter = '';

    public string $resolvidoFilter = '';

    public string $sort = 'desc';

    public function mount(): void
    {
        $classificacao = request()->query('classificacao');

        if (is_string($classificacao) && in_array($classificacao, array_column(ConciliactionClassificacao::cases(), 'value'), true)) {
            $this->classificacaoFilter = $classificacao;
        }

        $resolvido = request()->query('resolvido');

        if (is_string($resolvido) && in_array($resolvido, ['true', 'false'], true)) {
            $this->resolvidoFilter = $resolvido;
        }
    }

    public function filterClassificacao(string $classificacao): void
    {
        $this->classificacaoFilter = $this->classificacaoFilter === $classificacao ? '' : $classificacao;
    }

    public function filterResolvido(string $resolvido): void
    {
        $this->resolvidoFilter = $this->resolvidoFilter === $resolvido ? '' : $resolvido;
    }

    public function clearFilters(): void
    {
        $this->classificacaoFilter = '';
        $this->resolvidoFilter = '';
        $this->search = '';
    }

    public function resolver(int $id): void
    {
        $conciliaction = Conciliaction::find($id);

        if (! $conciliaction) {
            return;
        }

        app(ConciliactionService::class)->resolver($conciliaction);
    }

    public function classificar(int $id, string $classificacao): void
    {
        $conciliaction = Conciliaction::find($id);

        if (! $conciliaction) {
            return;
        }

        $enum = ConciliactionClassificacao::tryFrom($classificacao);

        if (! $enum) {
            return;
        }

        app(ConciliactionService::class)->classificar($conciliaction, $enum);
    }

    public function remove(int $id): void
    {
        $conciliaction = Conciliaction::find($id);

        if (! $conciliaction) {
            return;
        }

        app(ConciliactionService::class)->remove($conciliaction);

        $this->dispatch('conciliaction-removed');
    }

    public function render()
    {
        $conciliactions = Conciliaction::with(['opportunity', 'commercial'])
            ->when($this->search !== '', function ($query): void {
                $like = '%'.$this->search.'%';
                $query->where(fn ($q) => $q
                    ->where('observacoes', 'like', $like)
                    ->orWhereHas('opportunity', fn ($q2) => $q2->where('nome', 'like', $like))
                    ->orWhereHas('commercial', fn ($q2) => $q2->where('nome', 'like', $like)));
            })
            ->when($this->classificacaoFilter !== '', fn ($query) => $query->where('classificacao', $this->classificacaoFilter))
            ->when($this->resolvidoFilter !== '', fn ($query) => $query->where('resolvido', $this->resolvidoFilter === 'true'))
            ->orderBy('data', $this->sort === 'asc' ? 'asc' : 'desc')
            ->orderBy('id')
            ->get();

        return view('livewire.conciliactions.conciliaction-list', [
            'conciliactions' => $conciliactions,
            'classificacoes' => ConciliactionClassificacao::cases(),
        ]);
    }
}
