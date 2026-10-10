<?php

namespace Tests\Feature;

use App\Enums\ContractStatus;
use App\Livewire\Contracts\ContractList;
use App\Models\Contract;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
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

    public function test_list_shows_direct_action_icons_without_dropdown(): void
    {
        // BLOCO 1 (mudancas.md): acções em ícones directos, sem dropdown de 3 pontos.
        Contract::factory()->create(['estado' => ContractStatus::ACTIVO]);

        $response = $this->actingAs($this->user())->get('/contratos');

        $response
            ->assertOk()
            ->assertSee('action-icons', false)
            ->assertSee('feather-refresh-cw')     // Renovar (activo)
            ->assertSee('feather-x-circle')       // Cancelar
            ->assertSee('feather-trash-2')        // Eliminar
            ->assertDontSee('feather-more-horizontal');
    }

    public function test_remove_deletes_contract(): void
    {
        // BLOCO 1: Eliminar directo (novo ícone nos contratos).
        $contract = Contract::factory()->create();

        $this->actingAs($this->user());

        Livewire::test(ContractList::class)
            ->call('remove', $contract->id);

        $this->assertSoftDeleted('contracts', ['id' => $contract->id]);
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

    /**
     * Suposição §19.12: contrato com data_fim passada e estado activo
     * muda automaticamente para expirado (comando contracts:expire).
     */
    public function test_expire_command_changes_past_active_contracts_only(): void
    {
        $passado = Contract::factory()->create([
            'estado' => ContractStatus::ACTIVO,
            'data_fim' => now()->subDay(),
        ]);
        $futuro = Contract::factory()->create([
            'estado' => ContractStatus::ACTIVO,
            'data_fim' => now()->addDays(30),
        ]);
        $canceladoPassado = Contract::factory()->create([
            'estado' => ContractStatus::CANCELADO,
            'data_fim' => now()->subDay(),
        ]);

        // Dry-run não altera nada.
        $this->artisan('contracts:expire', ['--dry-run' => true])->assertSuccessful();
        $this->assertSame(ContractStatus::ACTIVO, $passado->fresh()->estado);

        $this->artisan('contracts:expire')->assertSuccessful();

        $this->assertSame(ContractStatus::EXPIRADO, $passado->fresh()->estado);
        $this->assertSame(ContractStatus::ACTIVO, $futuro->fresh()->estado);
        $this->assertSame(ContractStatus::CANCELADO, $canceladoPassado->fresh()->estado);
    }
}
