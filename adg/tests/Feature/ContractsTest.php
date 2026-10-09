<?php

namespace Tests\Feature;

use App\Models\Contract;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContractsTest extends TestCase
{
    use RefreshDatabase;

    private function user(): User
    {
        return User::factory()->create();
    }

    public function test_contract_routes_require_authentication(): void
    {
        $this->get('/contratos')->assertRedirect('/login');
        $this->get('/contratos/nova')->assertRedirect('/login');
    }

    public function test_contract_list_renders_prototype_structure(): void
    {
        Contract::factory()->count(2)->create();

        $response = $this->actingAs($this->user())->get('/contratos');

        $response
            ->assertOk()
            ->assertSee('Contratos')
            ->assertSee('wire:id', false)
            ->assertSee('feather-plus')
            ->assertSee('feather-filter')
            ->assertSee(route('contracts.create'), false)
            ->assertDontSee('content-sidebar', false)
            ->assertSee('content-area-header', false)
            ->assertSee('Filtros')
            ->assertSee('Novo Contrato')
            ->assertSee('Todos os estados');
    }

    public function test_contract_create_renders_form(): void
    {
        $response = $this->actingAs($this->user())->get('/contratos/nova');

        $response
            ->assertOk()
            ->assertSee('Novo Contrato')
            ->assertSee('wire:id', false);
    }

    public function test_contract_show_renders_model(): void
    {
        $contract = Contract::factory()->create();

        $this->actingAs($this->user())
            ->get(route('contracts.show', $contract))
            ->assertOk()
            ->assertSee($contract->numero_contrato);
    }

    public function test_contract_edit_renders_and_prefills_form(): void
    {
        $contract = Contract::factory()->create(['notas' => 'Nota de teste']);

        $this->actingAs($this->user())
            ->get(route('contracts.edit', ['contract' => $contract->uuid]))
            ->assertOk()
            ->assertSee('Editar Contrato')
            ->assertSee($contract->numero_contrato);
    }

    public function test_sidebar_shows_contratos_acordeao_link(): void
    {
        $response = $this->actingAs($this->user())->get('/contratos');

        $response
            ->assertOk()
            ->assertSee(route('contracts'), false)
            ->assertSee('Contratos');
    }
}
