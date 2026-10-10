<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductsTest extends TestCase
{
    use RefreshDatabase;

    private function user(): User
    {
        return User::factory()->create();
    }

    public function test_product_routes_require_authentication(): void
    {
        $this->get('/produtos')->assertRedirect('/login');
        $this->get('/produtos/nova')->assertRedirect('/login');
    }

    public function test_product_list_renders_prototype_structure(): void
    {
        Product::factory()->count(2)->create();

        $response = $this->actingAs($this->user())->get('/produtos');

        $response
            ->assertOk()
            ->assertSee('Produtos')
            ->assertSee('wire:id', false)
            ->assertSee('feather-plus')
            ->assertSee('feather-check-circle')
            ->assertSee(route('products.create'), false)
            ->assertDontSee('content-sidebar', false)
            ->assertSee('content-area-header', false)
            ->assertSee('Filtros')
            ->assertSee('Novo Produto')
            ->assertSee('Todos os estados')
            ->assertSee('Todos os comerciais');
    }

    public function test_list_shows_direct_action_icons_without_dropdown(): void
    {
        // BLOCO 1 (mudancas.md): acções em ícones directos, sem dropdown de 3 pontos.
        Product::factory()->create(['activo' => true]);

        $response = $this->actingAs($this->user())->get('/produtos');

        $response
            ->assertOk()
            ->assertSee('action-icons', false)
            ->assertSee('feather-pause')       // Desactivar (activo)
            ->assertSee('feather-trash-2')     // Eliminar
            ->assertDontSee('feather-more-horizontal');
    }

    public function test_product_create_renders_form(): void
    {
        $response = $this->actingAs($this->user())->get('/produtos/nova');

        $response
            ->assertOk()
            ->assertSee('Novo Produto')
            ->assertSee('wire:id', false);
    }

    public function test_product_edit_renders_and_prefills_form(): void
    {
        $product = Product::factory()->create(['nome' => 'Painel Solar 450W']);

        $response = $this->actingAs($this->user())
            ->get(route('products.edit', ['produto' => $product->uuid]));

        $response
            ->assertOk()
            ->assertSee('Editar Produto')
            ->assertSee('Painel Solar 450W');
    }

    public function test_sidebar_shows_produtos_acordeao_link(): void
    {
        $response = $this->actingAs($this->user())->get('/produtos');

        $response
            ->assertOk()
            ->assertSee(route('products'), false)
            ->assertSee('Produtos');
    }
}
