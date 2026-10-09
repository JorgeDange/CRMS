<?php

namespace Tests\Feature;

use App\Livewire\Alerts\AlertBell;
use App\Livewire\Alerts\AlertList;
use App\Models\Alert;
use App\Models\Opportunity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
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

    /**
     * O tipo AGENDA (lembrete diário, §11) tem de aparecer no filtro
     * de tipos da lista — o dropdown é 100% enum-driven.
     */
    public function test_type_filter_includes_agenda(): void
    {
        $response = $this->actingAs($this->user())->get('/alertas');

        $response
            ->assertOk()
            ->assertSee("filterTipo('agenda')", false)
            ->assertSee('Agenda');
    }

    /**
     * --dry-run conta os alertas que seriam criados sem persistir
     * nem enviar emails; a execução normal sim.
     */
    public function test_alerts_generate_dry_run_does_not_persist(): void
    {
        $user = $this->user();
        Opportunity::factory()->create([
            'fase_atual' => 'lead',
            'ultima_actividade_em' => now()->subDays(10),
        ]);

        $antes = Alert::count();

        $this->artisan('alerts:generate', ['--dry-run' => true])
            ->assertSuccessful();

        $this->assertSame($antes, Alert::count(), 'Dry-run nao pode criar alertas.');

        $this->artisan('alerts:generate')
            ->assertSuccessful();

        $this->assertSame($antes + 1, Alert::count(), 'Execucao normal cria o alerta candidato.');
    }

    /**
     * Clicar num alerta do sino abre a modal de detalhe (Sprint 14) com a
     * mensagem completa e as accoes funcionam a partir dela.
     */
    public function test_bell_opens_detail_modal_and_applies_actions(): void
    {
        $user = $this->user();
        $alerta = Alert::factory()->create([
            'user_id' => $user->id,
            'lido' => false,
            'resolvido' => false,
            'mensagem' => 'Mensagem completa para a modal de detalhe',
        ]);

        $this->actingAs($user);

        $component = Livewire::test(AlertBell::class);

        $component
            ->call('abrirAlerta', $alerta->id)
            ->assertSet('alertaActivoId', $alerta->id)
            ->assertSee('Mensagem completa para a modal de detalhe');

        $component->call('marcarLido', $alerta->id);
        $this->assertTrue($alerta->fresh()->lido, 'Marcar lido a partir da modal nao pode falhar.');

        $component->call('marcarResolvido', $alerta->id);
        $this->assertTrue($alerta->fresh()->resolvido, 'Marcar resolvido a partir da modal nao pode falhar.');

        $component->call('remove', $alerta->id);
        $component->assertSet('alertaActivoId', null, 'Eliminar o alerta activo fecha a modal.');
        $this->assertDatabaseMissing('alerts', ['id' => $alerta->id]);
    }

    public function test_bell_ignores_unknown_alert(): void
    {
        $this->actingAs($this->user());

        Livewire::test(AlertBell::class)
            ->call('abrirAlerta', 999999)
            ->assertSet('alertaActivoId', null);
    }

    /**
     * Na pagina /alertas clicar na mensagem abre a mesma modal de detalhe.
     */
    public function test_alert_list_opens_detail_modal(): void
    {
        $alerta = Alert::factory()->create([
            'user_id' => $this->user()->id,
            'mensagem' => 'Alerta aberto pela pagina de alertas',
        ]);

        Livewire::test(AlertList::class)
            ->call('abrirAlerta', $alerta->id)
            ->assertSet('alertaActivoId', $alerta->id)
            ->assertSee('Alerta aberto pela pagina de alertas');
    }
}
