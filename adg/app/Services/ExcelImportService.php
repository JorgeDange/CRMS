<?php

namespace App\Services;

use App\Enums\OpportunityStage;
use App\Livewire\Opportunities\Pipeline;
use App\Models\Opportunity;
use DateTime;
use DateTimeInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Throwable;

/**
 * Importacao de oportunidades a partir de Excel (Sprint 4).
 *
 * - Template com cabecalhos fixos (HEADINGS)
 * - Preview com validacao por linha (erros)
 * - Duplicados: mesmo Titulo + Cliente (no ficheiro e na base de dados)
 * - Importacao com relatorio (criadas / duplicadas / erros)
 */
class ExcelImportService
{
    /** Cabecalhos exactos do template. */
    public const HEADINGS = [
        'Cliente',
        'Título',
        'Valor (Kz)',
        'Estado',
        'Probabilidade',
        'Data de fecho',
        'Descrição',
    ];

    public const MAX_ROWS = 1000;

    private const REQUIRED = ['cliente', 'titulo', 'valor'];

    /** Cabecalho normalizado => campo interno. */
    private const HEADING_MAP = [
        'cliente' => 'cliente',
        'titulo' => 'titulo',
        'valor' => 'valor',
        'valorkz' => 'valor',
        'montante' => 'valor',
        'estado' => 'estado',
        'fase' => 'estado',
        'probabilidade' => 'probabilidade',
        'prob' => 'probabilidade',
        'datadefecho' => 'data_fecho',
        'data' => 'data_fecho',
        'descricao' => 'descricao',
        'descricaodainteresse' => 'descricao',
        'interesse' => 'descricao',
    ];

    /**
     * Le o ficheiro e devolve as linhas validadas (preview).
     *
     *
     * @return array<int, array<string, mixed>>
     *
     * @throws ValidationException cabecalhos em falta, ficheiro vazio, etc.
     */
    public function parse(string $path): array
    {
        $sheets = Excel::toCollection(null, $path);
        $sheet = $sheets->first()?->values() ?? collect();

        if ($sheet->isEmpty()) {
            throw ValidationException::withMessages(['file' => 'O ficheiro está vazio.']);
        }

        $indexes = $this->mapHeadings($sheet->first());

        $dataRows = $sheet
            ->slice(1)
            ->values()
            ->filter(fn ($row): bool => $this->isRowFilled($row));

        if ($dataRows->isEmpty()) {
            throw ValidationException::withMessages(['file' => 'O ficheiro não tem linhas para importar.']);
        }

        if ($dataRows->count() > self::MAX_ROWS) {
            throw ValidationException::withMessages([
                'file' => 'Máximo de '.self::MAX_ROWS.' linhas por importação (o ficheiro tem '.$dataRows->count().').',
            ]);
        }

        $rows = [];
        foreach ($dataRows->values() as $offset => $row) {
            $rows[] = $this->parseRow($offset + 2, collect($row)->values(), $indexes);
        }

        return $this->markDuplicates($rows);
    }

    /**
     * Importa as linhas do preview e devolve o relatorio (Sprint 4).
     *
     * @param  array<int, array<string, mixed>>  $rows
     * @return array{rows: array<int, array<string, mixed>>, report: array<string, int>}
     */
    public function import(array $rows): array
    {
        $report = [
            'total' => count($rows),
            'criadas' => 0,
            'duplicadas' => 0,
            'erros' => 0,
        ];

        DB::transaction(function () use (&$rows, &$report): void {
            foreach ($rows as $index => $row) {
                if ($row['erros'] !== []) {
                    $rows[$index]['estado_importacao'] = 'erro';
                    $report['erros']++;

                    continue;
                }

                if ($row['duplicada']) {
                    $rows[$index]['estado_importacao'] = 'duplicada';
                    $report['duplicadas']++;

                    continue;
                }

                // Duplicado tambem pode surgir APOS o preview (novo registo entretanto).
                if (Opportunity::where('titulo', $row['titulo'])->where('nome', $row['cliente'])->exists()) {
                    $rows[$index]['duplicada'] = true;
                    $rows[$index]['motivo_duplicado'] = 'Já existe no sistema.';
                    $rows[$index]['estado_importacao'] = 'duplicada';
                    $report['duplicadas']++;

                    continue;
                }

                app(OpportunityService::class)->create([
                    'nome' => $row['cliente'],
                    'empresa' => $row['cliente'],
                    'titulo' => $row['titulo'],
                    'valor_estimado' => $row['valor'],
                    'fase_atual' => $row['fase_atual'],
                    'probabilidade' => $row['probabilidade'] ?? 30,
                    'data_fecho' => $row['data_fecho'],
                    'interesse' => $row['descricao'],
                ]);

                $rows[$index]['estado_importacao'] = 'criada';
                $report['criadas']++;
            }
        });

        return ['rows' => $rows, 'report' => $report];
    }

    /**
     * Normaliza a linha de cabecalhos e devolve campo => indice.
     *
     * @throws ValidationException
     */
    private function mapHeadings(mixed $headerRow): array
    {
        $indexes = [];

        foreach (collect($headerRow)->values() as $position => $heading) {
            $key = $this->normalizeKey($heading);

            if ($key !== '' && isset(self::HEADING_MAP[$key]) && ! isset($indexes[self::HEADING_MAP[$key]])) {
                $indexes[self::HEADING_MAP[$key]] = (int) $position;
            }
        }

        $missing = array_diff(self::REQUIRED, array_keys($indexes));

        if ($missing !== []) {
            $labels = [
                'cliente' => 'Cliente',
                'titulo' => 'Título',
                'valor' => 'Valor (Kz)',
            ];

            throw ValidationException::withMessages([
                'file' => 'Colunas em falta no template: '.implode(', ', array_map(fn (string $field): string => $labels[$field], $missing)).'.',
            ]);
        }

        return $indexes;
    }

    /**
     * @param  array<string, int>  $indexes
     * @return array<string, mixed>
     */
    private function parseRow(int $line, Collection $row, array $indexes): array
    {
        $get = fn (string $field): mixed => isset($indexes[$field]) && $indexes[$field] < $row->count()
            ? $row[$indexes[$field]]
            : null;

        $erros = [];

        $cliente = trim((string) ($get('cliente') ?? ''));
        if ($cliente === '') {
            $erros[] = 'Cliente é obrigatório.';
        } elseif (mb_strlen($cliente) > 255) {
            $erros[] = 'Cliente demasiado longo (máx. 255 caracteres).';
        }

        $titulo = trim((string) ($get('titulo') ?? ''));
        if ($titulo === '') {
            $erros[] = 'Título é obrigatório.';
        } elseif (mb_strlen($titulo) > 255) {
            $erros[] = 'Título demasiado longo (máx. 255 caracteres).';
        }

        [$valor, $valorErro] = $this->normalizeValor($get('valor'));
        if ($valorErro !== null) {
            $erros[] = $valorErro;
        }

        [$fase, $estadoErro] = $this->normalizeEstado($get('estado'));
        if ($estadoErro !== null) {
            $erros[] = $estadoErro;
        }

        [$probabilidade, $probabilidadeErro] = $this->normalizeProbabilidade($get('probabilidade'));
        if ($probabilidadeErro !== null) {
            $erros[] = $probabilidadeErro;
        }

        [$dataFecho, $dataErro] = $this->normalizeData($get('data_fecho'));
        if ($dataErro !== null) {
            $erros[] = $dataErro;
        }

        $descricao = trim((string) ($get('descricao') ?? ''));
        if (mb_strlen($descricao) > 4000) {
            $erros[] = 'Descrição demasiado longa (máx. 4000 caracteres).';
        }

        return [
            'linha' => $line,
            'cliente' => $cliente !== '' ? $cliente : null,
            'titulo' => $titulo !== '' ? $titulo : null,
            'valor' => $valor,
            'fase_atual' => $fase,
            'estado_label' => Pipeline::COLUMNS[$fase]['label'],
            'probabilidade' => $probabilidade,
            'data_fecho' => $dataFecho,
            'descricao' => $descricao !== '' ? $descricao : null,
            'erros' => $erros,
            'duplicada' => false,
            'motivo_duplicado' => null,
            'estado_importacao' => null,
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     * @return array<int, array<string, mixed>>
     */
    private function markDuplicates(array $rows): array
    {
        $valid = array_values(array_filter($rows, fn (array $row): bool => $row['erros'] === []));

        $existentes = Opportunity::query()
            ->whereIn('titulo', array_column($valid, 'titulo'))
            ->whereIn('nome', array_column($valid, 'cliente'))
            ->get(['titulo', 'nome']);

        $noSistema = [];
        foreach ($existentes as $opportunity) {
            $noSistema[$this->duplicateKey($opportunity->titulo, $opportunity->nome)] = true;
        }

        $noFicheiro = [];

        foreach ($rows as $index => $row) {
            if ($row['erros'] !== []) {
                continue;
            }

            $key = $this->duplicateKey($row['titulo'], $row['cliente']);

            if (isset($noSistema[$key])) {
                $rows[$index]['duplicada'] = true;
                $rows[$index]['motivo_duplicado'] = 'Já existe no sistema.';

                continue;
            }

            if (isset($noFicheiro[$key])) {
                $rows[$index]['duplicada'] = true;
                $rows[$index]['motivo_duplicado'] = 'Repetida no ficheiro (linha '.$noFicheiro[$key].').';

                continue;
            }

            $noFicheiro[$key] = $row['linha'];
        }

        return $rows;
    }

    /** Chave de duplicado: Titulo + Cliente sem maiusculas/acentos de espaco. */
    private function duplicateKey(?string $titulo, ?string $cliente): string
    {
        return Str::lower(trim((string) $titulo).'|'.trim((string) $cliente));
    }

    /** Cliente / Título / valor / etc. — vazio => nao preenchido. */
    private function isRowFilled(mixed $row): bool
    {
        foreach (collect($row)->values() as $cell) {
            if ($cell === null) {
                continue;
            }

            if (! is_scalar($cell)) {
                return true;
            }

            if (trim((string) $cell) !== '') {
                return true;
            }
        }

        return false;
    }

    /** Normaliza um cabecalho: minusculas, sem acentos, so alfanumericos. */
    private function normalizeKey(mixed $heading): string
    {
        $text = is_scalar($heading) ? (string) $heading : '';

        return preg_replace('/[^a-z0-9]/', '', Str::lower(Str::ascii($text))) ?? '';
    }

    /**
     * Valor em Kz: aceita 12000000, "12.000.000", "12,000,000" (milhares),
     * "12,5" / "12.5" (decimal) e texto com "Kz".
     *
     * @return array{0: float|null, 1: string|null}
     */
    private function normalizeValor(mixed $value): array
    {
        if (is_int($value) || is_float($value)) {
            $number = (float) $value;
        } else {
            $raw = trim((string) ($value ?? ''));
            $raw = str_replace(["\u{00A0}", ' '], '', $raw);
            $raw = Str::of($raw)->replaceMatches('/kz/i', '')->toString();

            if ($raw === '') {
                return [null, 'Valor é obrigatório.'];
            }

            if (str_contains($raw, '.') && str_contains($raw, ',')) {
                // Formato "12.000.000,50": ponto = milhares, virgula = decimal.
                $raw = str_replace(',', '.', str_replace('.', '', $raw));
            } elseif (str_contains($raw, ',')) {
                if (preg_match('/^-?\d{1,3}(,\d{3})+$/', $raw)) {
                    $raw = str_replace(',', '', $raw);
                } elseif (preg_match('/^-?\d+,\d{1,2}$/', $raw)) {
                    $raw = str_replace(',', '.', $raw);
                } else {
                    return [null, 'Valor inválido ("'.trim((string) $value).'").'];
                }
            } elseif (preg_match('/^-?\d{1,3}(\.\d{3})+$/', $raw)) {
                $raw = str_replace('.', '', $raw);
            }

            if (! is_numeric($raw)) {
                return [null, 'Valor inválido ("'.trim((string) $value).'").'];
            }

            $number = (float) $raw;
        }

        if ($number < 0) {
            return [null, 'Valor não pode ser negativo.'];
        }

        return [$number, null];
    }

    /**
     * Estado: aceita os nomes do pipeline (Leads, Visitas, ...), as
     * variaveis do enum ou vazio (= Leads por omissao).
     *
     * @return array{0: string, 1: string|null}
     */
    private function normalizeEstado(mixed $value): array
    {
        $raw = trim((string) ($value ?? ''));

        if ($raw === '') {
            return [OpportunityStage::LEAD->value, null];
        }

        $key = $this->normalizeKey($raw);

        foreach (OpportunityStage::cases() as $stage) {
            if ($this->normalizeKey($stage->value) === $key) {
                return [$stage->value, null];
            }
        }

        foreach (Pipeline::COLUMNS as $stageValue => $config) {
            if ($this->normalizeKey($config['label']) === $key) {
                return [$stageValue, null];
            }
        }

        return [
            OpportunityStage::LEAD->value,
            'Estado inválido ("'.$raw.'"). Use: Leads, Qualificação, Visitas, Proposta/Negociação, Aprovação, Bilhete, Resultado, Cliente Aprovado ou Cliente Não Aprovado.',
        ];
    }

    /**
     * @return array{0: int|null, 1: string|null}
     */
    private function normalizeProbabilidade(mixed $value): array
    {
        if ($value === null || trim((string) $value) === '') {
            return [null, null];
        }

        $raw = str_replace(['%', ' '], '', trim((string) $value));

        if (! preg_match('/^-?\d+(\.\d+)?$/', $raw)) {
            return [null, 'Probabilidade inválida ("'.trim((string) $value).'"). Use um valor de 0 a 100.'];
        }

        $number = (float) $raw;

        if ($number < 0 || $number > 100) {
            return [null, 'Probabilidade fora do intervalo de 0 a 100.'];
        }

        return [(int) round($number), null];
    }

    /**
     * Data de fecho: data de Excel, "30/12/2026", "2026-12-30", etc.
     *
     * @return array{0: string|null, 1: string|null}
     */
    private function normalizeData(mixed $value): array
    {
        if ($value === null || (is_string($value) && trim($value) === '')) {
            return [null, null];
        }

        if ($value instanceof DateTimeInterface) {
            return [(new DateTime($value->format('Y-m-d')))->format('Y-m-d'), null];
        }

        if (is_int($value) || is_float($value)) {
            // Numero serial do Excel (celula com formato de data).
            if ($value < 1 || $value > 2958465) {
                return [null, 'Data de fecho inválida. Use DD/MM/AAAA.'];
            }

            try {
                $date = ExcelDate::excelToDateTimeObject((float) $value);
            } catch (Throwable) {
                return [null, 'Data de fecho inválida. Use DD/MM/AAAA.'];
            }

            if ((int) $date->format('Y') < 1900 || (int) $date->format('Y') > 2200) {
                return [null, 'Data de fecho inválida. Use DD/MM/AAAA.'];
            }

            return [$date->format('Y-m-d'), null];
        }

        $text = trim((string) $value);

        foreach (['d/m/Y', 'd-m-Y', 'Y-m-d', 'd/m/y', 'd-m-y'] as $format) {
            $parsed = DateTime::createFromFormat('!'.$format, $text);

            if ($parsed !== false && $parsed->format($format) === $text) {
                if ((int) $parsed->format('Y') < 1900 || (int) $parsed->format('Y') > 2200) {
                    return [null, 'Data de fecho inválida. Use DD/MM/AAAA.'];
                }

                return [$parsed->format('Y-m-d'), null];
            }
        }

        return [null, 'Data de fecho inválida ("'.$text.'"). Use DD/MM/AAAA.'];
    }
}
