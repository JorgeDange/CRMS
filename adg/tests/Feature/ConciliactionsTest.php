<?php

namespace Tests\Feature;

use App\Models\Conciliaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConciliactionsTest extends TestCase
{
    use RefreshDatabase;

    private function user(): User
    {
        return User::factory()->create();
    }

    public function test_conciliaction_routes_require_authentication(): void
    {
        $this->get(route('conciliactions'))->assertRedirect('/login');
        $this->get(route('conciliactions.create'))->assertRedirect('/login');
    }

    public function test_conciliaction_list_renders_prototype_structure(): void
    {
        Conciliaction::factory()->count(2)->create();

        $response = $this->actingAs($this->user())->get(route('conciliactions'));

        $response
            ->assertOk()
            ->assertSee('Concilia', false)
            ->assertSee('wire:id', false)
            ->assertSee('feather-plus')
            ->assertSee('feather-clock')
            ->assertSee(route('conciliactions.create'), false)
            ->assertDontSee('content-sidebar', false)
            ->assertSee('content-area-header', false)
            ->assertSee('Filtros')
            ->assertSee('Todas as classificações')
            ->assertSee('Todos os estados')
            ->assertSee('Nova Conciliação');
    }

    public function test_conciliaction_create_renders_form(): void
    {
        $response = $this->actingAs($this->user())->get(route('conciliactions.create'));

        $response
            ->assertOk()
            ->assertSee('Nova Concilia', false)
            ->assertSee('wire:id', false);
    }

    public function test_conciliaction_edit_renders(): void
    {
        $conciliaction = Conciliaction::factory()->create();

        $response = $this->actingAs($this->user())
            ->get(route('conciliactions.edit', ['conciliaction' => $conciliaction->uuid]));

        $response
            ->assertOk()
            ->assertSee('Editar Concilia', false);
    }

    public function test_sidebar_shows_conciliactions_acordeao_link(): void
    {
        $response = $this->actingAs($this->user())->get(route('conciliactions'));

        $response
            ->assertOk()
            ->assertSee(route('conciliactions'), false)
            ->assertSee('Concilia', false);
    }
}
