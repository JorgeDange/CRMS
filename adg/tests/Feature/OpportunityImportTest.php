<?php

namespace Tests\Feature;

use App\Livewire\Opportunities\OpportunityImport;
use App\Models\Opportunity;
use App\Models\User;
use App\Services\ExcelImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Livewire\Livewire;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Excel as ExcelWriter;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class OpportunityImportTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Gera um .xlsx real (cabecalhos + linhas) para o upload.
     */
    private function makeFile(array $rows, ?array $headings = null): UploadedFile
    {
        $export = new class($headings ?? ExcelImportService::HEADINGS, $rows) implements FromCollection, WithHeadings
        {
            public function __construct(
                private array $headings,
                private array $rows,
            ) {}

            public function headings(): array
            {
                return $this->headings;
            }

            public function collection(): Collection
            {
                return collect($this->rows);
            }
        };

        $path = tempnam(sys_get_temp_dir(), 'adg-import').'.xlsx';
        file_put_contents($path, Excel::raw($export, ExcelWriter::XLSX));
        $binary = (string) file_get_contents($path);
        unlink($path);

        return UploadedFile::fake()->createWithContent('oportunidades.xlsx', $binary);
    }

    public function test_import_page_requires_authentication(): void
    {
        $this->get('/pipeline/importar')->assertRedirect('/login');
        $this->get('/pipeline/importar/template')->assertRedirect('/login');
    }

    public function test_import_page_renders(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/pipeline/importar')
            ->assertOk()
            ->assertSee('Importar Oportunidades')
            ->assertSee('Importar de Excel')
            ->assertSee('Pré-visualizar')
            ->assertSee('Descarregar template')
            ->assertSee('Como importar')
            ->assertSee('Nova Oportunidade')
            ->assertSee('<span>Nova Oportunidade</span>', false)
            ->assertSee('>Nova Oportunidade</a>', false);
    }

    public function test_create_page_links_to_import(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/pipeline/nova')
            ->assertOk()
            ->assertSee(route('opportunities.import'), false)
            ->assertSee('<span>Importar Excel</span>', false);
    }

    public function test_template_downloads_with_expected_headings(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('opportunities.import.template'));

        $response->assertOk();
        $this->assertStringContainsString(
            'template-oportunidades.xlsx',
            (string) $response->headers->get('content-disposition'),
        );

        // BinaryFileResponse nao expoe getContent() — ler o ficheiro de origem.
        $source = $response->baseResponse->getFile()->getPathname();
        $path = tempnam(sys_get_temp_dir(), 'adg-template').'.xlsx';
        copy($source, $path);

        $sheets = Excel::toCollection(null, $path);

        $this->assertSame(ExcelImportService::HEADINGS, $sheets->first()->first()->values()->all());
    }

    public function test_preview_validates_and_formats_rows(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $file = $this->makeFile([
            ['SIL – Sociedade Importadora', 'Renovação contrato 2026', '12.000.000', 'lead', '50', '30/12/2026', 'Fornecedor preferencial'],
            ['', 'Sem cliente', '100000', 'visita', '', '', ''],
        ]);

        $component = Livewire::test(OpportunityImport::class)
            ->set('file', $file)
            ->call('preview')
            ->assertHasNoErrors()
            ->assertSee('Kz 12.000.000')
            ->assertSee('Renovação contrato 2026')
            ->assertSee('Pronta')
            ->assertSee('Erro')
            ->assertSee('Cliente é obrigatório.')
            ->assertSee('Importar 1 oportunidade(s)');

        $rows = $component->get('rows');

        $this->assertCount(2, $rows);
        $this->assertSame('2026-12-30', $rows[0]['data_fecho']);
        $this->assertSame(50, $rows[0]['probabilidade']);
        $this->assertSame(12000000.0, $rows[0]['valor']);
        $this->assertSame([], $rows[0]['erros']);
        $this->assertSame(Opportunity::count(), 0);
    }

    public function test_preview_marks_duplicates_in_file_and_database(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Opportunity::factory()->create([
            'titulo' => 'Já existe no sistema',
            'nome' => 'Cliente Antigo',
            'empresa' => 'Cliente Antigo',
        ]);

        $file = $this->makeFile([
            ['Cliente Antigo', 'Já existe no sistema', '5000000', '', '', '', ''],
            ['Cliente Novo', 'Linha repetida', '1000000', '', '', '', ''],
            ['Cliente Novo', 'Linha repetida', '1000000', '', '', '', ''],
        ]);

        $rows = Livewire::test(OpportunityImport::class)
            ->set('file', $file)
            ->call('preview')
            ->assertHasNoErrors()
            ->get('rows');

        $this->assertTrue($rows[0]['duplicada']);
        $this->assertSame('Já existe no sistema.', $rows[0]['motivo_duplicado']);

        $this->assertFalse($rows[1]['duplicada']);

        $this->assertTrue($rows[2]['duplicada']);
        $this->assertStringContainsString('Repetida no ficheiro (linha 3)', $rows[2]['motivo_duplicado']);

        $this->assertSame(0, Opportunity::where('titulo', 'Linha repetida')->count());
    }

    public function test_import_creates_opportunities_and_shows_report(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $file = $this->makeFile([
            ['SIL – Sociedade Importadora', 'Renovação contrato 2026', '12.000.000', 'lead', '50', '30/12/2026', 'Fornecedor preferencial'],
            ['Maona Energy', 'Instalação de bombas', '4500000', 'qualificacao', '70', '', ''],
        ]);

        Livewire::test(OpportunityImport::class)
            ->set('file', $file)
            ->call('preview')
            ->call('import')
            ->assertHasNoErrors()
            ->assertSee('Relatório de importação')
            ->assertSee('Criadas: 2')
            ->assertSee('Duplicadas: 0')
            ->assertSee('Erros: 0')
            ->assertSee('Nova importação');

        $this->assertSame(2, Opportunity::count());

        $opportunity = Opportunity::firstWhere('titulo', 'Renovação contrato 2026');

        $this->assertSame(12000000, (int) $opportunity->valor_estimado);
        $this->assertSame('SIL – Sociedade Importadora', $opportunity->nome);
        $this->assertSame(50, $opportunity->probabilidade);
        $this->assertSame('2026-12-30', $opportunity->data_fecho?->format('Y-m-d'));
        $this->assertSame('Fornecedor preferencial', $opportunity->interesse);

        $this->assertDatabaseHas('opportunity_stage_history', [
            'opportunity_id' => $opportunity->id,
            'fase_anterior' => null,
            'fase_nova' => 'lead',
            'user_id' => $user->id,
        ]);
    }

    public function test_import_skips_invalid_and_duplicate_rows(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Opportunity::factory()->create([
            'titulo' => 'Existe',
            'nome' => 'Cliente A',
            'empresa' => 'Cliente A',
        ]);

        $file = $this->makeFile([
            ['Cliente B', 'Nova oportunidade', '2000000', '', '', '', ''],
            ['Cliente A', 'Existe', '3000000', '', '', '', ''],
            ['', 'Sem cliente', '1000', '', '', '', ''],
        ]);

        $component = Livewire::test(OpportunityImport::class)
            ->set('file', $file)
            ->call('preview')
            ->call('import')
            ->assertSee('Criadas: 1')
            ->assertSee('Duplicadas: 1')
            ->assertSee('Erros: 1');

        $this->assertSame(2, Opportunity::count());
        $this->assertNotNull(Opportunity::firstWhere('titulo', 'Nova oportunidade'));

        $report = $component->get('report');

        $this->assertSame(1, $report['criadas']);
        $this->assertSame(1, $report['duplicadas']);
        $this->assertSame(1, $report['erros']);
        $this->assertSame(3, $report['total']);

        $states = collect($component->get('rows'))->pluck('estado_importacao');

        $this->assertSame(['criada', 'duplicada', 'erro'], $states->all());
    }

    public function test_preview_rejects_file_without_required_columns(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $file = $this->makeFile([['SIL', '123']], ['Nome', 'Idade']);

        Livewire::test(OpportunityImport::class)
            ->set('file', $file)
            ->call('preview')
            ->assertHasErrors(['file'])
            ->assertSee('Colunas em falta no template');
    }

    public function test_preview_rejects_non_excel_files(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(OpportunityImport::class)
            ->set('file', UploadedFile::fake()->create('notas.txt', 10, 'text/plain'))
            ->call('preview')
            ->assertHasErrors(['file' => 'mimes']);
    }

    public function test_preview_requires_a_file(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(OpportunityImport::class)
            ->call('preview')
            ->assertHasErrors(['file' => 'required']);
    }
}
