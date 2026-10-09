<?php

namespace Tests\Feature;

use App\Livewire\Clients\ClientTable;
use App\Models\Commercial;
use App\Models\Opportunity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Sprint 13 — Tabela de Clientes (seguir.md §11).
 */
class ClientTableTest extends TestCase
{
    use RefreshDatabase;

    private function user(): User
    {
        return User::factory()->create();
    }

    private function aprovado(array $atributos = []): Opportunity
    {
        return Opportunity::factory()->create(array_merge([
            'fase_atual' => 'cliente_aprovado',
        ], $atributos));
    }

    public function test_clients_route_requires_authentication(): void
    {
        $this->get('/clientes')->assertRedirect('/login');
    }

    public function test_clients_page_renders_structure(): void
    {
        $this->aprovado();

        $response = $this->actingAs($this->user())->get('/clientes');

        $response
            ->assertOk()
            ->assertSee('Lista de Clientes')
            ->assertSee('Nº')
            ->assertSee('CÓDIGO')
            ->assertSee('CLIENTE')
            ->assertSee('KAM')
            ->assertSee('COMPRAS')
            ->assertSee('ASSINATURA DO DIRECTOR')
            ->assertSee('ENVIADO/ENTREGUE')
            ->assertSee('CONCILIAÇÃO')
            ->assertSee('CONTRATOS')
            ->assertDontSee('content-sidebar', false)
            ->assertSee('content-area-header', false)
            ->assertSee('customerList', false);
    }

    public function test_only_approved_clients_are_listed(): void
    {
        $this->aprovado(['nome' => 'Cliente Aprovado Seguro']);
        Opportunity::factory()->create([
            'fase_atual' => 'lead',
            'nome' => 'Lead Por Aprovar',
        ]);

        $this->actingAs($this->user())
            ->get('/clientes')
            ->assertOk()
            ->assertSee('Cliente Aprovado Seguro')
            ->assertDontSee('Lead Por Aprovar');
    }

    public function test_row_shows_codigo_kam_and_badges(): void
    {
        $comercial = Commercial::factory()->create(['nome' => 'KAM Teste Cliente', 'activo' => true]);
        $this->aprovado([
            'nome' => 'Empresa Com Todos Os Campos',
            'codigo' => 'CLI999',
            'comercial_id' => $comercial->id,
            'tem_compras' => true,
            'assinatura_director' => true,
            'enviado_entregue' => true,
            'conciliacao_estado' => 'discordante',
            'contrato_estado' => 'caducidade',
        ]);

        $this->actingAs($this->user())
            ->get('/clientes')
            ->assertOk()
            ->assertSee('CLI999')
            ->assertSee('Empresa Com Todos Os Campos')
            ->assertSee('KAM Teste Cliente')
            ->assertSee('Discordante')
            ->assertSee('CADUCIDADE');
    }

    public function test_search_filters_by_name_and_codigo(): void
    {
        $this->aprovado(['nome' => 'Distribuidora Alpha', 'codigo' => 'CLI010']);
        $this->aprovado(['nome' => 'Construtora Beta', 'codigo' => 'CLI020']);

        Livewire::test(ClientTable::class)
            ->set('search', 'Alpha')
            ->assertSee('Distribuidora Alpha')
            ->assertDontSee('Construtora Beta');

        Livewire::test(ClientTable::class)
            ->set('search', 'CLI020')
            ->assertSee('Construtora Beta')
            ->assertDontSee('Distribuidora Alpha');
    }

    public function test_kam_filter_shows_only_selected_commercial(): void
    {
        $kamUm = Commercial::factory()->create(['nome' => 'KAM Um Cliente', 'activo' => true]);
        $kamDois = Commercial::factory()->create(['nome' => 'KAM Dois Cliente', 'activo' => true]);
        $this->aprovado(['nome' => 'Cliente Do Um', 'comercial_id' => $kamUm->id]);
        $this->aprovado(['nome' => 'Cliente Do Dois', 'comercial_id' => $kamDois->id]);

        Livewire::test(ClientTable::class)
            ->set('kamFilter', (string) $kamUm->id)
            ->assertSee('Cliente Do Um')
            ->assertDontSee('Cliente Do Dois');
    }

    public function test_compras_filter_shows_only_sim(): void
    {
        $this->aprovado(['nome' => 'Cliente Com Compras', 'tem_compras' => true]);
        $this->aprovado(['nome' => 'Cliente Sem Compras', 'tem_compras' => false]);

        Livewire::test(ClientTable::class)
            ->set('comprasFilter', 'sim')
            ->assertSee('Cliente Com Compras')
            ->assertDontSee('Cliente Sem Compras');
    }

    public function test_conciliacao_filter_shows_only_concordante(): void
    {
        $this->aprovado(['nome' => 'Cliente Concordante', 'conciliacao_estado' => 'concordante']);
        $this->aprovado(['nome' => 'Cliente Discordante', 'conciliacao_estado' => 'discordante']);

        Livewire::test(ClientTable::class)
            ->set('conciliacaoFilter', 'concordante')
            ->assertSee('Cliente Concordante')
            ->assertDontSee('Cliente Discordante');
    }

    public function test_contrato_filter_shows_only_caducidade(): void
    {
        $this->aprovado(['nome' => 'Cliente Caducidade', 'contrato_estado' => 'caducidade']);
        $this->aprovado(['nome' => 'Cliente Contrato Sim', 'contrato_estado' => 'sim']);

        Livewire::test(ClientTable::class)
            ->set('contratoFilter', 'caducidade')
            ->assertSee('Cliente Caducidade')
            ->assertDontSee('Cliente Contrato Sim');
    }

    public function test_reset_filters_restores_all_clients(): void
    {
        $kam = Commercial::factory()->create(['activo' => true]);
        $this->aprovado(['nome' => 'Cliente Visivel Um', 'codigo' => 'CLI700', 'tem_compras' => true, 'comercial_id' => $kam->id]);
        $this->aprovado(['nome' => 'Cliente Visivel Dois', 'codigo' => 'CLI701', 'tem_compras' => false]);

        Livewire::test(ClientTable::class)
            ->set('search', 'CLI700')
            ->set('kamFilter', (string) $kam->id)
            ->set('comprasFilter', 'sim')
            ->set('conciliacaoFilter', 'concordante')
            ->set('contratoFilter', 'sim')
            ->call('resetFilters')
            ->assertSet('search', '')
            ->assertSet('kamFilter', '')
            ->assertSet('comprasFilter', '')
            ->assertSet('conciliacaoFilter', '')
            ->assertSet('contratoFilter', '')
            ->assertSee('Cliente Visivel Um')
            ->assertSee('Cliente Visivel Dois');
    }

    public function test_sort_by_codigo_toggles_direction(): void
    {
        Livewire::test(ClientTable::class)
            ->call('sortBy', 'codigo')
            ->assertSet('sortField', 'codigo')
            ->assertSet('sortDirection', 'asc')
            ->call('sortBy', 'codigo')
            ->assertSet('sortDirection', 'desc');
    }

    public function test_pagination_appears_with_more_than_20_clients(): void
    {
        Opportunity::factory()->count(25)->create(['fase_atual' => 'cliente_aprovado']);

        $component = Livewire::test(ClientTable::class);

        $this->assertTrue($component->viewData('clients')->hasPages(), 'Mais de 20 registos tem de paginar.');
        $this->assertSame(20, $component->viewData('clients')->count());
    }

    public function test_ver_button_links_to_opportunity_detail(): void
    {
        $opportunity = $this->aprovado(['nome' => 'Empresa Link Detalhe']);

        $this->actingAs($this->user())
            ->get('/clientes')
            ->assertOk()
            ->assertSee(route('opportunities.show', $opportunity), false);
    }

    public function test_sidebar_shows_clientes_item_active(): void
    {
        $response = $this->actingAs($this->user())->get('/clientes');

        $response
            ->assertOk()
            ->assertSee(route('clients.index'), false)
            ->assertSee('feather-users', false)
            ->assertSee('<li class="nxl-item active">', false);
    }

    public function test_empty_state_message_appears_without_clients(): void
    {
        $this->actingAs($this->user())
            ->get('/clientes')
            ->assertOk()
            ->assertSee('Nenhum cliente encontrado');
    }
}
