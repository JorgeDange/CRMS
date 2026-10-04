<?php

namespace App\Livewire\Opportunities;

use App\Services\ExcelImportService;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * Importacao de oportunidades via Excel (Sprint 4):
 * upload -> preview -> confirmacao -> relatorio.
 */
class OpportunityImport extends Component
{
    use WithFileUploads;

    public $file = null;

    /** @var array<int, array<string, mixed>> */
    public array $rows = [];

    /** @var array<string, int>|null */
    public ?array $report = null;

    public bool $analysed = false;

    protected function rules(): array
    {
        return [
            'file' => ['required', 'file', 'mimes:xlsx,xls', 'max:10240'],
        ];
    }

    public function updatedFile(): void
    {
        $this->reset(['rows', 'report', 'analysed']);
    }

    public function preview(): void
    {
        $this->validate();

        $this->rows = app(ExcelImportService::class)->parse($this->file->getRealPath());
        $this->analysed = true;
        $this->report = null;
    }

    public function import(): void
    {
        if (! $this->analysed || $this->rows === []) {
            return;
        }

        $this->validate();

        $result = app(ExcelImportService::class)->import($this->rows);

        $this->rows = $result['rows'];
        $this->report = $result['report'];

        $this->file?->delete();
        $this->file = null;
        $this->resetValidation();
    }

    public function startOver(): void
    {
        $this->file?->delete();
        $this->reset(['file', 'rows', 'report', 'analysed']);
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.opportunities.import');
    }
}
