<?php

namespace Tests\Feature;

use App\Enums\ContractStatus;
use App\Enums\OpportunityStage;
use App\Models\Contract;
use App\Models\Opportunity;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private function user(): User
    {
        return User::factory()->create();
    }

    private function seedSales(): void
    {
        Opportunity::factory()->count(3)->create([
            'fase_atual' => OpportunityStage::CLIENTE_APROVADO,
            'data_aprovacao' => now(),
        ]);
        Opportunity::factory()->count(2)->create([
            'fase_atual' => OpportunityStage::PROPOSTA_NEGOCIACAO,
        ]);
        Contract::factory()->count(2)->create(['estado' => ContractStatus::ACTIVO]);
    }

    public function test_dashboard_and_analytics_require_authentication(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
        $this->get('/analytics')->assertRedirect('/login');
    }

    public function test_dashboard_renders_prototype_kpis_and_charts(): void
    {
        $this->seedSales();

        $response = $this->actingAs($this->user())->get('/dashboard');

        $response
            ->assertOk()
            // KPIs do prototype index.html
            ->assertSee('Clientes Activos')
            ->assertSee('Oportunidades Abertas')
            ->assertSee('Produtos Activos') // BLOCO 2: antes "Encomendas em Curso"
            ->assertSee('Pendências Hoje')
            // Gráfico principal (ApexCharts area) — BLOCO 2: Pipeline por Mês
            ->assertSee('Pipeline por Mês')
            ->assertDontSee('Facturação Mensal')
            ->assertSee('id="paymentRecordsChart"', false)
            // Stats cards no footer do gráfico
            ->assertSee('Pendente')
            ->assertSee('Recebido')
            ->assertSee('Cancelado')
            ->assertSee('Total')
            // BLOCO 2: 4 mini-cards do pipeline (antes 3 cards de actividades)
            ->assertSee('Pipeline Aberto')
            ->assertSee('Aprovado (mês)')
            ->assertSee('Ponderado')
            ->assertSee('Taxa Conversão')
            ->assertDontSee('Actividades Concluídas')
            ->assertDontSee('Novas Actividades')
            ->assertDontSee('Encomendas Entregues');

        // Verifica payload do gráfico principal (data-adg-chart no div)
        $this->assertChartPayload($response->getContent(), 'paymentRecordsChart');
    }

    /**
     * BLOCO 2.1: o gráfico principal conta oportunidades ABERTAS por mês
     * (created_at), não soma valores em Kz nem conta aprovadas.
     */
    public function test_pipeline_chart_counts_open_opportunities_by_month(): void
    {
        Opportunity::factory()->count(3)->create([
            'fase_atual' => OpportunityStage::PROPOSTA_NEGOCIACAO,
            'valor_estimado' => 150000,
            'created_at' => now(),
        ]);
        Opportunity::factory()->create([
            'fase_atual' => OpportunityStage::CLIENTE_APROVADO,
            'valor_estimado' => 900000,
            'data_aprovacao' => now(),
        ]);

        $response = $this->actingAs($this->user())->get('/dashboard');

        $matches = [];
        $this->assertSame(1, preg_match('/id="paymentRecordsChart"[^>]*data-adg-chart="([^"]+)"/', $response->getContent(), $matches));
        $payload = json_decode(html_entity_decode($matches[1], ENT_QUOTES | ENT_HTML5), true);

        $this->assertSame('Pipeline', $payload['series'][0]['name']);
        $this->assertFalse($payload['moeda']);
        // Mês actual: 3 abertas (aprovada fora) — contagem, não valor
        $this->assertSame(3.0, (float) end($payload['series'][0]['data']));
    }

    /**
     * BLOCO 2.3: KPI Produtos Activos = contagem de products.activo = true.
     */
    public function test_kpi_produtos_activos_counts_active_products(): void
    {
        Product::factory()->count(3)->create(['activo' => true]);
        Product::factory()->count(1)->create(['activo' => false]);

        $response = $this->actingAs($this->user())->get('/dashboard');

        $response
            ->assertOk()
            ->assertSee('<span class="counter">3</span>/<span class="counter">4</span>', false);
    }

    /**
     * Regressao: /analytics rebentava com "Undefined variable $vendasYTD"
     * porque o DashboardView calculava os KPIs de 7 blocos e a view de
     * Analytics usava variaveis que nunca foram passadas.
     */
    public function test_analytics_renders_prototype_kpis_and_charts(): void
    {
        $this->seedSales();

        $response = $this->actingAs($this->user())->get('/analytics');

        $response
            ->assertOk()
            ->assertSee('Painel Executivo')
            // KPIs do prototype analytics.html
            ->assertSee('Vendas YTD')
            ->assertSee('Pipeline Ponderado')
            ->assertSee('Encomendas em Curso')
            ->assertSee('Dívida Vencida')
            // 4 gráficos ApexCharts (divs, não canvas)
            ->assertSee('Vendas Mensais (Kz)')
            ->assertSee('Estado das Encomendas')
            ->assertSee('Pipeline por Estado')
            ->assertSee('Clientes por Segmento')
            ->assertSee('id="execSalesChart"', false)
            ->assertSee('id="execOrdersChart"', false)
            ->assertSee('id="execPipelineChart"', false)
            ->assertSee('id="execSegmentChart"', false)
            // Tabelas
            ->assertSee('Performance por Gestor')
            ->assertSee('Top Clientes por Facturação');

        // Verifica payloads dos 4 gráficos (data-adg-chart nas divs)
        $this->assertChartPayload($response->getContent(), 'execSalesChart');
        $this->assertChartPayload($response->getContent(), 'execOrdersChart');
        $this->assertChartPayload($response->getContent(), 'execPipelineChart');
        $this->assertChartPayload($response->getContent(), 'execSegmentChart');
    }

    public function test_dashboard_shows_executivo_link_in_sidebar(): void
    {
        $response = $this->actingAs($this->user())->get('/dashboard');

        $response
            ->assertOk()
            ->assertSee(route('analytics'), false)
            ->assertSee('Painel Executivo');
    }

    public function test_layout_renders_global_confirm_modal_instead_of_native_alerts(): void
    {
        $response = $this->actingAs($this->user())->get('/dashboard');

        $response
            ->assertOk()
            ->assertSee('id="modalConfirmar"', false)
            ->assertSee('$store.confirmar', false);

        // nenhuma view pode usar o window.confirm do browser
        $arquivos = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(resource_path('views'), \FilesystemIterator::SKIP_DOTS)
        );

        foreach ($arquivos as $file) {
            if ($file->isDir() || $file->getExtension() !== 'blade.php') {
                continue;
            }

            $html = file_get_contents($file->getPathname());
            $this->assertDoesNotMatchRegularExpression(
                '/[^.\w]confirm\(/',
                $html,
                'View '.$file->getFilename().' ainda usa window.confirm — usar $store.confirmar.'
            );
        }
    }

    /**
     * Extrai o data-adg-chart de um div e valida o payload do
     * DashboardService {type, labels, series, color} ou {type, labels, series, colors}
     * que o ApexCharts lê.
     */
    private function assertChartPayload(string $html, string $chartId): void
    {
        $pattern = '/id="'.$chartId.'"[^>]*data-adg-chart="([^"]+)"/';
        $matches = [];
        $this->assertSame(1, preg_match($pattern, $html, $matches), "Div {$chartId} sem data-adg-chart.");

        $json = html_entity_decode($matches[1], ENT_QUOTES | ENT_HTML5);
        $payload = json_decode($json, true);

        $this->assertIsArray($payload, "Payload de {$chartId} nao e JSON valido.");
        $this->assertNotEmpty($payload['type'] ?? null, "{$chartId} sem type.");
        $this->assertNotEmpty($payload['labels'] ?? null, "{$chartId} sem labels.");

        // series (area) ou values (donut) ou series array (area)
        $hasSeries = ! empty($payload['series'] ?? null);
        $hasValues = ! empty($payload['values'] ?? null);
        $hasSeriesArray = isset($payload['series']) && is_array($payload['series']) && count($payload['series']) > 0 && isset($payload['series'][0]['data']);

        $this->assertTrue($hasSeries || $hasValues || $hasSeriesArray, "{$chartId} sem series/values/data.");

        if ($hasSeriesArray) {
            $this->assertSame(
                count($payload['labels']),
                count($payload['series'][0]['data']),
                "labels/data de {$chartId} com tamanhos diferentes."
            );
        } elseif ($hasValues) {
            $this->assertSame(
                count($payload['labels']),
                count($payload['values']),
                "labels/values de {$chartId} com tamanhos diferentes."
            );
        }
        $this->assertContains($payload['type'], ['area', 'donut']);
    }
}
