<?php

namespace App\Exports;

use App\Services\ExcelImportService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Template de importacao (Sprint 4): apenas os cabecalhos, com a
 * primeira linha em negrito. Uma linha por oportunidade.
 */
class OpportunityTemplateExport implements FromCollection, ShouldAutoSize, WithHeadings, WithStyles
{
    use Exportable;

    public function collection(): Collection
    {
        return collect();
    }

    public function headings(): array
    {
        return ExcelImportService::HEADINGS;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
