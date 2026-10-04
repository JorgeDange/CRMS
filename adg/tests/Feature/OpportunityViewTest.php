<?php

namespace Tests\Feature;

use App\Enums\OpportunityStage;
use App\Livewire\Activities\VisitReportForm;
use App\Livewire\Opportunities\OpportunityView;
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

    public function test_move_to_aprovado_updates_stage_and_history(): void
    {
        $user = User::factory()->create();
        $opportunity = Opportunity::factory()->create([
            'fase_atual' => OpportunityStage::APROVACAO,
            'probabilidade' => 60,
        ]);
        $this->actingAs($user);

        Livewire::test(OpportunityView::class, ['opportunity' => $opportunity])
            ->call('moveTo', 'aprovado')
            ->assertRedirect(route('opportunities.show', $opportunity));

        $opportunity->refresh();
        $this->assertSame(OpportunityStage::APROVADO, $opportunity->fase_atual);
        $this->assertSame(100, $opportunity->probabilidade);

        $this->assertDatabaseHas('opportunity_stage_history', [
            'opportunity_id' => $opportunity->id,
            'fase_anterior' => 'aprovacao',
            'fase_nova' => 'aprovado',
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
}
