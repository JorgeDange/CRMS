<?php

namespace App\Livewire\Activities;

use App\Models\VisitReport;
use Livewire\Component;

class VisitReportForm extends Component
{
    public int $opportunityId;

    public string $data_visita;

    public string $objectivo = '';

    public string $pessoas_presentes = '';

    public string $assuntos_tratados = '';

    public string $resultado = '';

    public string $proxima_accao = '';

    public function mount(int $opportunityId): void
    {
        $this->opportunityId = $opportunityId;
        $this->data_visita = now()->format('Y-m-d');
    }

    protected function rules(): array
    {
        return [
            'data_visita' => ['required', 'date'],
            'objectivo' => ['nullable', 'string', 'max:4000'],
            'pessoas_presentes' => ['nullable', 'string', 'max:4000'],
            'assuntos_tratados' => ['required', 'string', 'max:8000'],
            'resultado' => ['nullable', 'string', 'max:4000'],
            'proxima_accao' => ['nullable', 'string', 'max:4000'],
        ];
    }

    public function save(): void
    {
        $data = $this->validate();

        VisitReport::create([
            ...$data,
            'opportunity_id' => $this->opportunityId,
            'user_id' => auth()->id(),
        ]);

        $this->reset(['objectivo', 'pessoas_presentes', 'assuntos_tratados', 'resultado', 'proxima_accao']);

        $this->dispatch('visit-report-added');
    }

    public function render()
    {
        return view('livewire.activities.visit-report-form');
    }
}
