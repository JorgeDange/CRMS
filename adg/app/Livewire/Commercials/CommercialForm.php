<?php

namespace App\Livewire\Commercials;

use App\Models\Commercial;
use Livewire\Component;

class CommercialForm extends Component
{
    public ?int $commercialId = null;

    public string $nome = '';

    public string $descricao = '';

    public bool $activo = true;

    public function mount(?string $commercial = null): void
    {
        $uuid = $commercial ?? request()->query('commercial');

        if (! is_string($uuid) || $uuid === '') {
            return;
        }

        $commercial = Commercial::query()->where('uuid', $uuid)->first();

        if (! $commercial) {
            return;
        }

        $this->commercialId = $commercial->id;
        $this->nome = $commercial->nome;
        $this->descricao = $commercial->descricao ?? '';
        $this->activo = $commercial->activo;
    }

    protected function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'max:100', 'unique:commercials,nome,'.$this->commercialId],
            'descricao' => ['nullable', 'string', 'max:2000'],
            'activo' => ['boolean'],
        ];
    }

    public function save(): void
    {
        $data = $this->validate();

        if ($this->commercialId !== null) {
            $commercial = Commercial::findOrFail($this->commercialId);
            $commercial->fill($data);
            $commercial->save();
        } else {
            Commercial::create($data);
        }

        $this->redirect(route('commercials'), navigate: true);
    }

    public function render()
    {
        return view('livewire.commercials.commercial-form', [
            'editing' => $this->commercialId !== null,
        ]);
    }
}
