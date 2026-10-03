<?php

namespace Tests\Feature;

use App\Enums\OpportunityStage;
use App\Livewire\Opportunities\Pipeline;
use App\Livewire\Opportunities\Stats;
use App\Models\Opportunity;
use App\Models\OpportunityStageHistory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PipelineTest extends TestCase
{
    use RefreshDatabase;

    public function test_pipeline_requires_authentication(): void
    {
        $this->get('/pipeline')->assertRedirect('/login');
    }

    public function test_pipeline_page_renders_prototype_board(): void
    {
        $user = User::factory()->create();
        Opportunity::factory()->create([
            'titulo' => 'Renovação contrato 2026',
            'fase_atual' => OpportunityStage::LEAD,
        ]);

        $response = $this->actingAs($user)->get('/pipeline');

        $response
            ->assertOk()
            ->assertSee('kanban-board')
            ->assertSee('oppFilterBar')
            ->assertSee('id="collapseOne"', false)
            ->assertSee('data-stage="lead"', false)
            ->assertSee('data-stage="visita"', false)
            ->assertSee('data-stage="aprovacao"', false)
            ->assertSee('data-stage="aprovado"', false)
            ->assertSee('data-stage="nao_aprovado"', false)
            ->assertSee('>Leads<', false)
            ->assertSee('Visitas')
            ->assertSee('Aprovação')
            ->assertSee('Não Aprovado')
            ->assertSee('Renovação contrato 2026')
            ->assertSee('Nova Oportunidade')
            ->assertSee('Pipeline Aberto')
            ->assertSee('Taxa Conversão');
    }

    public function test_pipeline_moves_opportunity_and_records_history(): void
    {
        $user = User::factory()->create();
        $opportunity = Opportunity::factory()->create(['fase_atual' => OpportunityStage::LEAD]);
        $this->actingAs($user);

        Livewire::test(Pipeline::class)
            ->call('move', $opportunity->id, 'visita')
            ->assertDispatched('opportunity-moved');

        $this->assertDatabaseHas('opportunities', [
            'id' => $opportunity->id,
            'fase_atual' => 'visita',
        ]);

        $this->assertDatabaseHas('opportunity_stage_history', [
            'opportunity_id' => $opportunity->id,
            'fase_anterior' => 'lead',
            'fase_nova' => 'visita',
            'user_id' => $user->id,
        ]);
    }

    public function test_moving_to_same_stage_does_not_change_anything(): void
    {
        $user = User::factory()->create();
        $opportunity = Opportunity::factory()->create(['fase_atual' => OpportunityStage::VISITA]);
        $this->actingAs($user);

        Livewire::test(Pipeline::class)
            ->call('move', $opportunity->id, 'visita')
            ->assertDispatched('opportunity-moved');

        $this->assertSame(0, OpportunityStageHistory::count());
    }

    public function test_moving_to_aprovado_sets_final_state(): void
    {
        $user = User::factory()->create();
        $opportunity = Opportunity::factory()->create([
            'fase_atual' => OpportunityStage::APROVACAO,
            'probabilidade' => 60,
            'data_aprovacao' => null,
        ]);
        $this->actingAs($user);

        Livewire::test(Pipeline::class)->call('move', $opportunity->id, 'aprovado');

        $opportunity->refresh();
        $this->assertSame(OpportunityStage::APROVADO, $opportunity->fase_atual);
        $this->assertSame(100, $opportunity->probabilidade);
        $this->assertNotNull($opportunity->data_aprovacao);
    }

    public function test_moving_to_nao_aprovado_sets_final_state(): void
    {
        $user = User::factory()->create();
        $opportunity = Opportunity::factory()->create([
            'fase_atual' => OpportunityStage::VISITA,
            'probabilidade' => 40,
        ]);
        $this->actingAs($user);

        Livewire::test(Pipeline::class)->call('move', $opportunity->id, 'nao_aprovado');

        $opportunity->refresh();
        $this->assertSame(OpportunityStage::NAO_APROVADO, $opportunity->fase_atual);
        $this->assertSame(0, $opportunity->probabilidade);
        $this->assertNull($opportunity->data_aprovacao);
    }

    public function test_search_filters_the_board(): void
    {
        $user = User::factory()->create();
        Opportunity::factory()->create(['titulo' => 'Expansão armazém ZÉ']);
        Opportunity::factory()->create(['titulo' => 'Outra oportunidade YX']);

        $component = Livewire::actingAs($user)
            ->test(Pipeline::class)
            ->set('search', 'armazém ZÉ');

        $component->assertSee('Expansão armazém ZÉ');
        $component->assertDontSee('Outra oportunidade YX');
    }

    public function test_stage_filter_shows_only_selected_column(): void
    {
        $user = User::factory()->create();

        $component = Livewire::actingAs($user)
            ->test(Pipeline::class)
            ->set('stageFilter', 'lead');

        $component->assertSee('data-stage="lead"', false);
        $component->assertDontSee('data-stage="visita"', false);
        $component->assertDontSee('data-stage="nao_aprovado"', false);
    }

    public function test_stats_component_renders(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(Stats::class)
            ->assertSee('Pipeline Aberto')
            ->assertSee('Aprovado (mês)')
            ->assertSee('Ponderado')
            ->assertSee('Taxa Conversão');
    }
}
