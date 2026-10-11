<?php

namespace Tests\Feature;

use App\Enums\ActivityStatus;
use App\Enums\OpportunityStage;
use App\Livewire\Activities\ActivityForm;
use App\Livewire\Activities\VisitReportForm;
use App\Livewire\Opportunities\OpportunityActivities;
use App\Livewire\Opportunities\OpportunityView;
use App\Models\Activity;
use App\Models\Opportunity;
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
            ->assertSee('Ainda sem propostas!')
            ->assertSee('Ainda sem actividades!')
            ->assertSee('Ainda sem notas!')
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
}
