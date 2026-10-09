<?php

namespace Tests\Feature;

use App\Models\Alert;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AlertsTest extends TestCase
{
    use RefreshDatabase;

    private function user(): User
    {
        return User::factory()->create();
    }

    public function test_alerts_routes_require_authentication(): void
    {
        $this->get('/alertas')->assertRedirect('/login');
        $this->post('/alertas/marcar-todas')->assertRedirect('/login');
    }

    public function test_alerts_page_renders_prototype_structure(): void
    {
        $user = $this->user();
        Alert::factory()->count(3)->create(['user_id' => $user->id, 'lido' => false]);

        $response = $this->actingAs($user)->get('/alertas');

        $response
            ->assertOk()
            ->assertSee('Alertas')
            ->assertSee('Filtros')
            ->assertSee('feather-filter')
            ->assertSee('marcarLido')
            ->assertSee('marcarResolvido')
            ->assertDontSee('content-sidebar', false)
            ->assertSee('content-area-header', false)
            ->assertSee('Todos os tipos')
            ->assertSee('Todos os estados');
    }

    public function test_sidebar_shows_alertas_acordeao_link(): void
    {
        $response = $this->actingAs($this->user())->get('/alertas');

        $response
            ->assertOk()
            ->assertSee(route('alerts'), false)
            ->assertSee('Alertas');
    }

    public function test_mark_all_notifications_as_read(): void
    {
        $user = $this->user();
        Alert::factory()->count(4)->create(['user_id' => $user->id, 'lido' => false]);
        $other = User::factory()->create();
        $foreign = Alert::factory()->create(['user_id' => $other->id, 'lido' => false]);

        $response = $this->actingAs($user)
            ->from('/dashboard')
            ->post('/alertas/marcar-todas');

        $response->assertRedirect('/dashboard');
        $this->assertSame(0, Alert::where('user_id', $user->id)->where('lido', false)->count());
        $this->assertTrue($foreign->fresh()->lido === false, 'Alerta de outro utilizador nao pode ser alterado.');
    }

    /**
     * O sino do header tinha href="{{ route('alerts') }}" com
     * data-bs-toggle="dropdown" — Bootstrap nunca abria o dropdown porque
     * a pagina navegava primeiro. O prototipo usa href="#".
     */
    public function test_header_notifications_dropdown_does_not_navigate(): void
    {
        $response = $this->actingAs($this->user())->get('/dashboard');

        $response->assertOk()->assertSee('nxl-head-link', false);

        $html = $response->getContent();
        $this->assertMatchesRegularExpression(
            '/<a href="javascript:void\(0\);" class="nxl-head-link me-0 position-relative" data-bs-toggle="dropdown"/',
            $html
        );
        $this->assertDoesNotMatchRegularExpression(
            '/nxl-head-link[^>]*href="[^"]*alertas[^"]*"/',
            $html
        );
        $this->assertStringContainsString(route('alerts.markAll', [], false), $html);
    }
}
