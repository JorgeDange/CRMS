<?php

namespace Tests\Feature;

use App\Enums\OpportunityStage;
use App\Livewire\Opportunities\OpportunityCreate;
use App\Models\Opportunity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OpportunityCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_page_requires_authentication(): void
    {
        $this->get('/pipeline/nova')->assertRedirect('/login');
    }

    public function test_create_page_renders_prototype_form(): void
    {
        $user = User::factory()->create();
        Opportunity::factory()->create([
            'nome' => 'SIL – Sociedade Importadora',
            'empresa' => 'SIL – Sociedade Importadora',
        ]);

        $response = $this->actingAs($user)->get('/pipeline/nova');

        $response
            ->assertOk()
            ->assertSee('Nova Oportunidade')
            ->assertSee('Dados da Oportunidade')
            ->assertSee('id="oppForm"', false)
            ->assertSee('Seleccionar cliente')
            ->assertSee('Valor (Kz)')
            ->assertSee('Probabilidade')
            ->assertSee('Data de fecho')
            ->assertSee('Descrição')
            ->assertSee('Guardar rascunho')
            ->assertSee('Criar Oportunidade')
            ->assertSee('SIL – Sociedade Importadora')
            ->assertSee('>Leads<', false)
            ->assertSee('Qualificação')
            ->assertSee('Aprovação')
            ->assertSee('Cliente Aprovado')
            ->assertSee('Cliente Não Aprovado')
            ->assertSee('Dicas');
    }

    public function test_create_page_breadcrumb_matches_prototype(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/pipeline/nova')
            ->assertOk()
            ->assertSee('>Nova</li>', false);
    }

    public function test_sidebar_shows_nova_oportunidade_link(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/pipeline')
            ->assertOk()
            ->assertSee(route('opportunities.create'), false)
            ->assertSee('>Nova Oportunidade</a>', false)
            ->assertDontSee('>Nova Oportunidade <span class="badge', false);
    }

    public function test_validation_requires_client_title_and_value(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(OpportunityCreate::class)
            ->call('save')
            ->assertHasErrors([
                'cliente' => 'required',
                'titulo' => 'required',
                'valor' => 'required',
            ]);

        $this->assertSame(0, Opportunity::count());
    }

    public function test_valid_form_creates_opportunity_and_initial_history(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(OpportunityCreate::class)
            ->set('cliente', 'SIL – Sociedade Importadora')
            ->set('titulo', 'Renovação contrato 2026')
            ->set('valor', '12000000')
            ->set('fase_atual', 'lead')
            ->set('probabilidade', 30)
            ->set('data_fecho', '2026-11-30')
            ->set('descricao', 'Renovação anual do contrato de fornecimento.')
            ->call('save')
            ->assertRedirect(route('pipeline'));

        $opportunity = Opportunity::firstWhere('titulo', 'Renovação contrato 2026');

        $this->assertNotNull($opportunity);
        $this->assertSame('SIL – Sociedade Importadora', $opportunity->nome);
        $this->assertSame('SIL – Sociedade Importadora', $opportunity->empresa);
        $this->assertSame(OpportunityStage::LEAD, $opportunity->fase_atual);
        $this->assertSame(30, $opportunity->probabilidade);
        $this->assertSame('2026-11-30', $opportunity->data_fecho?->format('Y-m-d'));
        $this->assertSame('Renovação anual do contrato de fornecimento.', $opportunity->interesse);

        $this->assertDatabaseHas('opportunity_stage_history', [
            'opportunity_id' => $opportunity->id,
            'fase_anterior' => null,
            'fase_nova' => 'lead',
            'user_id' => $user->id,
        ]);
    }

    public function test_creating_in_cliente_aprovado_applies_final_state(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(OpportunityCreate::class)
            ->set('cliente', 'Maona Energy')
            ->set('titulo', 'Contrato aprovado')
            ->set('valor', '5000000')
            ->set('fase_atual', 'cliente_aprovado')
            ->set('probabilidade', 30)
            ->call('save');

        $opportunity = Opportunity::firstWhere('titulo', 'Contrato aprovado');

        $this->assertSame(100, $opportunity->probabilidade);
        $this->assertNotNull($opportunity->data_aprovacao);
    }
}
