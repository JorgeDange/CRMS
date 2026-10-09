<?php

namespace Tests\Feature;

use App\Models\Commercial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommercialsTest extends TestCase
{
    use RefreshDatabase;

    private function user(): User
    {
        return User::factory()->create();
    }

    public function test_commercial_routes_require_authentication(): void
    {
        $this->get('/comerciais')->assertRedirect('/login');
        $this->get('/comerciais/nova')->assertRedirect('/login');
    }

    public function test_commercial_list_renders_prototype_structure(): void
    {
        Commercial::factory()->count(2)->create();

        $response = $this->actingAs($this->user())->get('/comerciais');

        $response
            ->assertOk()
            ->assertSee('Comerciais')
            ->assertSee('wire:id', false)
            ->assertSee('feather-plus')
            ->assertSee('feather-filter')
            ->assertSee(route('commercials.create'), false)
            ->assertDontSee('content-sidebar', false)
            ->assertSee('content-area-header', false)
            ->assertSee('Filtros')
            ->assertSee('Novo Comercial')
            ->assertSee('Todos os estados');
    }

    public function test_commercial_create_renders_form(): void
    {
        $response = $this->actingAs($this->user())->get('/comerciais/nova');

        $response
            ->assertOk()
            ->assertSee('Novo Comercial')
            ->assertSee('wire:id', false);
    }

    public function test_commercial_edit_renders_and_prefills_form(): void
    {
        $commercial = Commercial::factory()->create(['nome' => 'Joana Manuel']);

        $this->actingAs($this->user())
            ->get(route('commercials.edit', ['commercial' => $commercial->uuid]))
            ->assertOk()
            ->assertSee('Editar Comercial')
            ->assertSee('Joana Manuel');
    }

    public function test_sidebar_shows_comerciais_acordeao_link(): void
    {
        $response = $this->actingAs($this->user())->get('/comerciais');

        $response
            ->assertOk()
            ->assertSee(route('commercials'), false)
            ->assertSee('Comerciais');
    }
}
