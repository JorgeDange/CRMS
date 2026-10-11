<?php

namespace Tests\Feature;

use App\Enums\ActivityStatus;
use App\Enums\OpportunityStage;
use App\Livewire\Activities\ActivityForm;
use App\Livewire\Activities\VisitReportForm;
use App\Livewire\Opportunities\OpportunityActivities;
use App\Livewire\Opportunities\OpportunityProposal;
use App\Livewire\Opportunities\OpportunityView;
use App\Models\Activity;
use App\Models\Opportunity;
use App\Models\Product;
use App\Models\User;
use App\Models\VisitReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OpportunityViewTest extends TestCase
{
    use RefreshDatabase;

    private function makeOpportunity(): Opportunity
    {
        return Opportunity::factory()->create([
            'titulo' => 'Renovação contrato 2026',
            'nome' => 'SIL – Sociedade Importadora',
            'empresa' => 'SIL – Sociedade Importadora',
            'valor_estimado' => 12000000,
            'probabilidade' => 30,
            'fase_atual' => OpportunityStage::LEAD,
            'interesse' => 'Renovação anual do contrato de fornecimento de cimento e agregados.',
            'data_fecho' => '2026-11-30',
        ]);
    }

    public function test_show_page_requires_authentication(): void
    {
        $opportunity = $this->makeOpportunity();

        $this->get('/pipeline/'.$opportunity->id)->assertRedirect('/login');
    }

    public function test_show_page_renders_prototype_detail(): void
    {
        $user = User::factory()->create();
        $opportunity = $this->makeOpportunity();

        $response = $this->actingAs($user)->get('/pipeline/'.$opportunity->id);

        $response
            ->assertOk()
            ->assertSee('Renovação contrato 2026')
            ->assertSee('Dados da Oportunidade')
            ->assertSee('SIL – Sociedade Importadora')
            ->assertSee('Kz 12.000.000')
            ->assertSee('Kz 3.600.000')
            ->assertSee('30/11/2026')
            ->assertSee('Criada em')
            ->assertSee('Responsável')
            ->assertSee('>Leads<', false)
            ->assertSee('Contexto comercial')
            ->assertSee('Renovação anual do contrato de fornecimento de cimento e agregados.')
            ->assertSee('>Resumo</button>', false)
            ->assertSee('>Proposta</button>', false)
            ->assertSee('>Actividades</button>', false)
            ->assertSee('>Notas</button>', false)
            ->assertSee('>Relatório de Visita</button>', false)
            ->assertSee('Guardar proposta')
            ->assertSee('Ainda sem actividades!')
            ->assertSee('Não existem notas para os filtros seleccionados.')
            ->assertSee('Ainda sem relatórios de visita.')
            ->assertSee('>Oportunidades</li>', false)
            ->assertSee('>Nova Oportunidade</a>', false);
    }

    public function test_show_page_unknown_opportunity_returns_404(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/pipeline/99999')->assertNotFound();
    }

    public function test_move_to_cliente_aprovado_updates_stage_and_history(): void
    {
        $user = User::factory()->create();
        $opportunity = Opportunity::factory()->create([
            'fase_atual' => OpportunityStage::RESULTADO,
            'probabilidade' => 90,
        ]);
        $this->actingAs($user);

        Livewire::test(OpportunityView::class, ['opportunity' => $opportunity])
            ->call('moveTo', 'cliente_aprovado')
            ->assertRedirect(route('opportunities.show', $opportunity));

        $opportunity->refresh();
        $this->assertSame(OpportunityStage::CLIENTE_APROVADO, $opportunity->fase_atual);
        $this->assertSame(100, $opportunity->probabilidade);

        $this->assertDatabaseHas('opportunity_stage_history', [
            'opportunity_id' => $opportunity->id,
            'fase_anterior' => 'resultado',
            'fase_nova' => 'cliente_aprovado',
        ]);
    }

    public function test_visit_report_requires_assuntos_tratados(): void
    {
        $user = User::factory()->create();
        $opportunity = $this->makeOpportunity();
        $this->actingAs($user);

        Livewire::test(VisitReportForm::class, ['opportunityId' => $opportunity->id])
            ->call('save')
            ->assertHasErrors(['assuntos_tratados' => 'required']);

        $this->assertSame(0, VisitReport::count());
    }

    public function test_visit_report_is_saved_and_form_resets(): void
    {
        $user = User::factory()->create();
        $opportunity = $this->makeOpportunity();
        $this->actingAs($user);

        Livewire::test(VisitReportForm::class, ['opportunityId' => $opportunity->id])
            ->set('data_visita', '2026-10-02')
            ->set('objectivo', 'Fechar condições do contrato')
            ->set('pessoas_presentes', 'Gerente de compras, responsável comercial')
            ->set('assuntos_tratados', 'Revisámos preços e prazos de entrega.')
            ->set('resultado', 'Interesse confirmado')
            ->set('proxima_accao', 'Enviar proposta revisada')
            ->call('save')
            ->assertDispatched('visit-report-added')
            ->assertSet('assuntos_tratados', '');

        $this->assertDatabaseHas('visit_reports', [
            'opportunity_id' => $opportunity->id,
            'user_id' => $user->id,
            'assuntos_tratados' => 'Revisámos preços e prazos de entrega.',
            'proxima_accao' => 'Enviar proposta revisada',
        ]);
    }

    public function test_visit_report_is_listed_in_view_component(): void
    {
        $user = User::factory()->create();
        $opportunity = $this->makeOpportunity();
        VisitReport::factory()->create([
            'opportunity_id' => $opportunity->id,
            'user_id' => $user->id,
            'data_visita' => '2026-10-01',
            'assuntos_tratados' => 'Tratámos o plano de fornecimento para 2027.',
        ]);
        $this->actingAs($user);

        Livewire::test(OpportunityView::class, ['opportunity' => $opportunity])
            ->set('activeTab', 'visit')
            ->assertSee('Relatório de visita — 01/10/2026')
            ->assertSee('Tratámos o plano de fornecimento para 2027.');
    }

    public function test_visit_reports_are_deleted_with_opportunity(): void
    {
        $user = User::factory()->create();
        $opportunity = $this->makeOpportunity();
        VisitReport::factory()->create([
            'opportunity_id' => $opportunity->id,
            'user_id' => $user->id,
        ]);

        $opportunity->delete();

        // Soft delete preserva os relatorios (FK cascade corre no forceDelete).
        $this->assertSame(1, VisitReport::count());

        $opportunity->forceDelete();

        $this->assertSame(0, VisitReport::count());
    }

    // ------------------------------------------------------------------
    // BLOCO 4 — aba Actividades (modo copia da Agenda)
    // ------------------------------------------------------------------

    public function test_activities_tab_shows_only_opportunity_activities(): void
    {
        $user = User::factory()->create();
        $opportunity = $this->makeOpportunity();
        $outra = Opportunity::factory()->create(['nome' => 'Outro Cliente']);

        Activity::factory()->create([
            'opportunity_id' => $opportunity->id,
            'user_id' => $user->id,
            'descricao' => 'Actividade desta oportunidade',
            'proxima_accao' => 'Accao desta opp',
        ]);

        Activity::factory()->create([
            'opportunity_id' => $outra->id,
            'user_id' => $user->id,
            'descricao' => 'Actividade de outra oportunidade',
        ]);

        $this->actingAs($user);

        Livewire::test(OpportunityActivities::class, ['opportunityId' => $opportunity->id])
            ->assertSee('Actividade desta oportunidade')
            ->assertSee('Accao desta opp')
            ->assertDontSee('Actividade de outra oportunidade')
            ->assertSee('Nova Actividade')
            ->assertSee(route('agenda.create', ['oportunidade' => $opportunity->id]), false);
    }

    public function test_activities_tab_empty_state_shows_placeholder(): void
    {
        $user = User::factory()->create();
        $opportunity = $this->makeOpportunity();

        $this->actingAs($user);

        Livewire::test(OpportunityActivities::class, ['opportunityId' => $opportunity->id])
            ->assertSee('Ainda sem actividades!')
            ->assertSee('0 actividade(s)');
    }

    public function test_activities_tab_toggle_and_remove_scoped_to_opportunity(): void
    {
        $user = User::factory()->create();
        $opportunity = $this->makeOpportunity();
        $outra = Opportunity::factory()->create(['nome' => 'Outro Cliente']);

        $activity = Activity::factory()->pendente()->create([
            'opportunity_id' => $opportunity->id,
            'user_id' => $user->id,
            'descricao' => 'Actividade para alternar',
        ]);

        // Actividade de outra opp — nao deve ser affectada por ids cruzados.
        $activityOutra = Activity::factory()->pendente()->create([
            'opportunity_id' => $outra->id,
            'user_id' => $user->id,
            'descricao' => 'Actividade de outra opp',
        ]);

        $this->actingAs($user);

        $component = Livewire::test(OpportunityActivities::class, ['opportunityId' => $opportunity->id]);

        // Toggle desta opp funciona.
        $component->call('toggle', $activity->id);
        $this->assertSame(ActivityStatus::CONCLUIDA, $activity->refresh()->status);

        // Id de outra opp e ignorado (escopo where opportunity_id).
        $component->call('toggle', $activityOutra->id);
        $this->assertSame(ActivityStatus::PENDENTE, $activityOutra->refresh()->status);

        // Remove desta opp funciona.
        $component->call('remove', $activity->id)->assertDispatched('activity-removed');
        $this->assertDatabaseMissing('activities', ['id' => $activity->id]);

        // Id de outra opp no remove tambem e ignorado.
        $component->call('remove', $activityOutra->id);
        $this->assertDatabaseHas('activities', ['id' => $activityOutra->id]);
    }

    public function test_form_preselects_opportunity_from_query_and_returns_after_save(): void
    {
        $user = User::factory()->create();
        $opportunity = $this->makeOpportunity();
        $this->actingAs($user);

        // Aberto via ?oportunidade={id} — pagina renderiza com o form.
        $this->get(route('agenda.create', ['oportunidade' => $opportunity->id]))
            ->assertOk()
            ->assertSee('SIL – Sociedade Importadora');

        // Componente com fromOpportunity=true (estado do mount com a query)
        // — save volta ao detalhe da oportunidade em vez da agenda.
        Livewire::test(ActivityForm::class)
            ->set('opportunity_id', $opportunity->id)
            ->set('fromOpportunity', true)
            ->set('descricao', 'Actividade criada a partir da opp')
            ->set('proxima_accao', 'Enviar follow-up')
            ->set('proxima_accao_prazo', now()->addDays(2)->toDateString())
            ->set('data', now()->toDateString())
            ->call('save')
            ->assertRedirect(route('opportunities.show', $opportunity));

        $this->assertDatabaseHas('activities', [
            'opportunity_id' => $opportunity->id,
            'descricao' => 'Actividade criada a partir da opp',
        ]);
    }

    // ------------------------------------------------------------------
    // BLOCO 6 — aba Proposta (9 campos directos em opportunities)
    // ------------------------------------------------------------------

    public function test_proposal_tab_renders_form_with_all_fields(): void
    {
        $user = User::factory()->create();
        $opportunity = $this->makeOpportunity();

        $this->actingAs($user)
            ->get('/pipeline/'.$opportunity->id)
            ->assertOk()
            ->assertSee('Guardar proposta')
            ->assertSee('Produto')
            ->assertSee('Preço (Kz)')
            ->assertSee('Volume')
            ->assertSee('Unidade')
            ->assertSee('Período — Início')
            ->assertSee('Período — Fim')
            ->assertSee('Crédito (Kz)')
            ->assertSee('Facturação (Kz)')
            ->assertSee('Non-oil (Kz)');
    }

    public function test_proposal_is_saved_and_persisted(): void
    {
        $user = User::factory()->create();
        $opportunity = $this->makeOpportunity();
        $produto = Product::factory()->create(['activo' => true]);
        $this->actingAs($user);

        Livewire::test(OpportunityProposal::class, ['opportunityId' => $opportunity->id])
            ->set('proposta_produto_id', $produto->id)
            ->set('proposta_preco', '850.50')
            ->set('proposta_volume', '5000')
            ->set('proposta_volume_unidade', 'm³')
            ->set('proposta_periodo_inicio', '2026-11-01')
            ->set('proposta_periodo_fim', '2027-10-31')
            ->set('proposta_credito', '100000')
            ->set('proposta_facturacao', '2500000')
            ->set('proposta_non_oil', '300000')
            ->call('save')
            ->assertSet('saved', true)
            ->assertHasNoErrors();

        $opportunity->refresh();

        $this->assertSame($produto->id, $opportunity->proposta_produto_id);
        $this->assertEquals(850.50, (float) $opportunity->proposta_preco);
        $this->assertEquals(5000, (float) $opportunity->proposta_volume);
        $this->assertSame('m³', $opportunity->proposta_volume_unidade);
        $this->assertSame('2026-11-01', $opportunity->proposta_periodo_inicio->format('Y-m-d'));
        $this->assertSame('2027-10-31', $opportunity->proposta_periodo_fim->format('Y-m-d'));
        $this->assertEquals(100000, (float) $opportunity->proposta_credito);
        $this->assertEquals(2500000, (float) $opportunity->proposta_facturacao);
        $this->assertEquals(300000, (float) $opportunity->proposta_non_oil);
    }

    public function test_proposal_loads_existing_values_for_editing(): void
    {
        $user = User::factory()->create();
        $produto = Product::factory()->create(['activo' => true, 'nome' => 'Gasóleo']);

        $opportunity = $this->makeOpportunity();
        $opportunity->update([
            'proposta_produto_id' => $produto->id,
            'proposta_preco' => '900',
            'proposta_volume' => '2000',
            'proposta_volume_unidade' => 'L',
            'proposta_periodo_inicio' => '2026-12-01',
            'proposta_periodo_fim' => '2027-06-30',
            'proposta_credito' => '50000',
            'proposta_facturacao' => '1800000',
            'proposta_non_oil' => '150000',
        ]);

        $this->actingAs($user);

        Livewire::test(OpportunityProposal::class, ['opportunityId' => $opportunity->id])
            ->assertSet('proposta_produto_id', $produto->id)
            ->assertSet('proposta_preco', '900.00')
            ->assertSet('proposta_volume', '2000.00')
            ->assertSet('proposta_volume_unidade', 'L')
            ->assertSet('proposta_periodo_inicio', '2026-12-01')
            ->assertSet('proposta_periodo_fim', '2027-06-30')
            ->assertSet('proposta_credito', '50000.00')
            ->assertSet('proposta_facturacao', '1800000.00')
            ->assertSet('proposta_non_oil', '150000.00')
            ->assertSee('Resumo da proposta')
            ->assertSee('Gasóleo');
    }

    public function test_proposal_validation_rejects_invalid_data(): void
    {
        $user = User::factory()->create();
        $opportunity = $this->makeOpportunity();
        $this->actingAs($user);

        Livewire::test(OpportunityProposal::class, ['opportunityId' => $opportunity->id])
            ->set('proposta_volume_unidade', 'galoes')
            ->set('proposta_preco', '-5')
            ->set('proposta_periodo_inicio', '2027-01-01')
            ->set('proposta_periodo_fim', '2026-01-01')
            ->call('save')
            ->assertHasErrors([
                'proposta_volume_unidade' => 'in',
                'proposta_preco' => 'min',
                'proposta_periodo_fim' => 'after_or_equal',
            ]);

        $opportunity->refresh();
        $this->assertNull($opportunity->proposta_preco);
    }

    public function test_proposal_empty_fields_are_saved_as_null(): void
    {
        $user = User::factory()->create();
        $opportunity = $this->makeOpportunity();
        $this->actingAs($user);

        Livewire::test(OpportunityProposal::class, ['opportunityId' => $opportunity->id])
            ->set('proposta_volume_unidade', 'L')
            ->call('save')
            ->assertSet('saved', true);

        $opportunity->refresh();

        $this->assertNull($opportunity->proposta_produto_id);
        $this->assertNull($opportunity->proposta_preco);
        $this->assertNull($opportunity->proposta_volume);
        $this->assertNull($opportunity->proposta_credito);
        $this->assertNull($opportunity->proposta_facturacao);
        $this->assertNull($opportunity->proposta_non_oil);
    }

    public function test_proposal_product_relation_resolves_name(): void
    {
        $user = User::factory()->create();
        $produto = Product::factory()->create(['nome' => 'Gasolina']);
        $opportunity = $this->makeOpportunity();
        $opportunity->update(['proposta_produto_id' => $produto->id]);

        $this->actingAs($user);

        $this->assertSame('Gasolina', $opportunity->fresh()->propostaProduto->nome);
    }
}
