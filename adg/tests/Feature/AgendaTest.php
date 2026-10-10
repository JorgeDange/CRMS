<?php

namespace Tests\Feature;

use App\Enums\ActivityStatus;
use App\Enums\ActivityType;
use App\Enums\AlertType;
use App\Livewire\Activities\ActivityCalendar;
use App\Livewire\Activities\ActivityForm;
use App\Livewire\Activities\ActivityList;
use App\Models\Activity;
use App\Models\Alert;
use App\Models\Opportunity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AgendaTest extends TestCase
{
    use RefreshDatabase;

    private function user(): User
    {
        return User::factory()->create();
    }

    private function opportunity(): Opportunity
    {
        return Opportunity::factory()->create([
            'nome' => 'SIL – Sociedade Importadora',
            'empresa' => 'SIL – Sociedade Importadora',
        ]);
    }

    public function test_agenda_routes_require_authentication(): void
    {
        $this->get('/agenda')->assertRedirect('/login');
        $this->get('/agenda/nova')->assertRedirect('/login');
        $this->get('/agenda/calendario')->assertRedirect('/login');
    }

    public function test_sidebar_shows_agenda_acordeao_links(): void
    {
        $response = $this->actingAs($this->user())->get('/agenda');

        $response
            ->assertOk()
            ->assertSee('Agenda Comercial')
            ->assertSee(route('agenda.calendar'), false)
            ->assertSee(route('agenda'), false)
            ->assertSee(route('agenda.create'), false)
            ->assertSee('>Calendário</a>', false)
            ->assertSee('>Actividades</a>', false)
            ->assertSee('>Nova Actividade</a>', false);

        // Oportunidades, Agenda Comercial, Conciliações, Contratos, Produtos, Comerciais, Alertas e Dashboard são acordeões (o resto continua "Em breve").
        $this->assertSame(8, substr_count($response->getContent(), 'nxl-hasmenu'));
    }

    public function test_list_page_renders_prototype_structure(): void
    {
        $user = $this->user();
        $opportunity = $this->opportunity();

        Activity::factory()->create([
            'opportunity_id' => $opportunity->id,
            'user_id' => $user->id,
            'tipo' => ActivityType::CHAMADA,
            'descricao' => 'Videoconferência com Importadora Kinaxixi',
            'data' => now()->toDateString(),
            'hora' => '09:00',
            'status' => ActivityStatus::PENDENTE,
            'proxima_accao' => 'Enviar ata da reunião',
            'proxima_accao_prazo' => now()->addDay()->toDateString(),
        ]);

        $response = $this->actingAs($user)->get('/agenda');

        $response
            ->assertOk()
            ->assertDontSee('content-sidebar', false)
            ->assertSee('content-area-header', false)
            ->assertSee('single-task-list', false)
            ->assertSee('Filtros')
            ->assertSee('Todos os estados')
            ->assertSee('Todos os tipos')
            ->assertSee('Nova Actividade')
            ->assertSee('Videoconferência com Importadora Kinaxixi')
            ->assertSee('SIL – Sociedade Importadora')
            ->assertSee('Enviar ata da reunião')
            ->assertSee('Hoje', false)
            ->assertSee('Pendente')
            ->assertSee('Chamadas')
            ->assertSee('Mais recentes');
    }

    public function test_list_page_survives_soft_deleted_opportunity(): void
    {
        // Regressao 2026-10-09: activities ligadas a opps soft-deleted
        // (demo-hist) rebentavam /agenda com 500 ("property nome on null").
        $user = $this->user();
        $opportunity = $this->opportunity();

        Activity::factory()->create([
            'opportunity_id' => $opportunity->id,
            'user_id' => $user->id,
            'tipo' => ActivityType::CHAMADA,
            'descricao' => 'Actividade de opp apagada',
            'data' => now()->toDateString(),
            'hora' => '09:00',
            'status' => ActivityStatus::PENDENTE,
        ]);

        $opportunity->delete();

        $this->actingAs($user)
            ->get('/agenda')
            ->assertOk()
            ->assertDontSee('Actividade de opp apagada');

        $this->actingAs($user)
            ->get('/agenda/calendario')
            ->assertOk()
            ->assertDontSee('Actividade de opp apagada');
    }

    public function test_list_page_shows_empty_state(): void
    {
        $this->actingAs($this->user())
            ->get('/agenda')
            ->assertOk()
            ->assertSee('Não existem actividades para os filtros seleccionados.');
    }

    public function test_status_filter_reads_query_string(): void
    {
        $user = $this->user();
        $opportunity = $this->opportunity();

        Activity::factory()->create([
            'opportunity_id' => $opportunity->id,
            'user_id' => $user->id,
            'descricao' => 'Actividade concluida para filtro',
            'status' => ActivityStatus::CONCLUIDA,
        ]);

        Activity::factory()->create([
            'opportunity_id' => $opportunity->id,
            'user_id' => $user->id,
            'descricao' => 'Actividade pendente para filtro',
            'status' => ActivityStatus::PENDENTE,
        ]);

        $this->actingAs($user)
            ->get('/agenda?status=concluida')
            ->assertOk()
            ->assertSee('Actividade concluida para filtro')
            ->assertDontSee('Actividade pendente para filtro');
    }

    public function test_type_filter_reads_query_string(): void
    {
        $user = $this->user();
        $opportunity = $this->opportunity();

        Activity::factory()->create([
            'opportunity_id' => $opportunity->id,
            'user_id' => $user->id,
            'tipo' => ActivityType::REUNIAO,
            'descricao' => 'Reuniao para filtro de tipo',
        ]);

        Activity::factory()->create([
            'opportunity_id' => $opportunity->id,
            'user_id' => $user->id,
            'tipo' => ActivityType::VISITA,
            'descricao' => 'Visita para filtro de tipo',
        ]);

        $this->actingAs($user)
            ->get('/agenda?tipo=reuniao')
            ->assertOk()
            ->assertSee('Reuniao para filtro de tipo')
            ->assertDontSee('Visita para filtro de tipo');
    }

    public function test_invalid_filters_are_ignored(): void
    {
        $user = $this->user();
        $opportunity = $this->opportunity();

        Activity::factory()->create([
            'opportunity_id' => $opportunity->id,
            'user_id' => $user->id,
            'descricao' => 'Actividade que tem de aparecer',
        ]);

        $this->actingAs($user)
            ->get('/agenda?status=bogus&tipo=bogus')
            ->assertOk()
            ->assertSee('Actividade que tem de aparecer');
    }

    public function test_search_filters_the_list(): void
    {
        $user = $this->user();
        $opportunity = $this->opportunity();

        Activity::factory()->create([
            'opportunity_id' => $opportunity->id,
            'user_id' => $user->id,
            'descricao' => 'Videoconferência Kinaxixi ZE',
        ]);

        Activity::factory()->create([
            'opportunity_id' => $opportunity->id,
            'user_id' => $user->id,
            'descricao' => 'Outra actividade YX',
        ]);

        Livewire::actingAs($user)
            ->test(ActivityList::class)
            ->set('search', 'Kinaxixi ZE')
            ->assertSee('Videoconferência Kinaxixi ZE')
            ->assertDontSee('Outra actividade YX');
    }

    public function test_toggle_marks_activity_completed_and_back(): void
    {
        $user = $this->user();
        $activity = Activity::factory()->pendente()->create([
            'user_id' => $user->id,
            'descricao' => 'Actividade para alternar',
        ]);

        $this->actingAs($user);

        Livewire::test(ActivityList::class)->call('toggle', $activity->id);
        $this->assertSame(ActivityStatus::CONCLUIDA, $activity->refresh()->status);

        Livewire::test(ActivityList::class)->call('toggle', $activity->id);
        $this->assertSame(ActivityStatus::PENDENTE, $activity->refresh()->status);
    }

    public function test_remove_deletes_activity_and_dispatches_event(): void
    {
        $user = $this->user();
        $activity = Activity::factory()->create([
            'user_id' => $user->id,
            'descricao' => 'Actividade para eliminar',
        ]);

        $this->actingAs($user);

        Livewire::test(ActivityList::class)
            ->call('remove', $activity->id)
            ->assertDispatched('activity-removed');

        $this->assertDatabaseMissing('activities', ['id' => $activity->id]);
    }

    public function test_create_page_renders_prototype_form(): void
    {
        $this->opportunity();

        $response = $this->actingAs($this->user())->get('/agenda/nova');

        $response
            ->assertOk()
            ->assertSee('Nova Actividade')
            ->assertSee('Guardar actividade')
            ->assertSee('Próxima acção')
            ->assertSee('Descrição da próxima acção')
            ->assertSee('Prazo')
            ->assertSee('Legenda de tipos')
            ->assertSee('Seleccionar cliente')
            ->assertSee('SIL – Sociedade Importadora');
    }

    public function test_create_page_loads_existing_activity_for_editing(): void
    {
        $user = $this->user();
        $opportunity = $this->opportunity();

        $activity = Activity::factory()->create([
            'opportunity_id' => $opportunity->id,
            'user_id' => $user->id,
            'descricao' => 'Actividade existente para editar',
            'proxima_accao' => 'Accao existente',
        ]);

        $this->actingAs($user)
            ->get('/agenda/nova?atividade='.$activity->uuid)
            ->assertOk()
            ->assertSee('Actividade existente para editar')
            ->assertSee('Accao existente')
            ->assertSee('Editar actividade');
    }

    public function test_validation_requires_opportunity_description_and_next_step(): void
    {
        $user = $this->user();
        $this->actingAs($user);

        Livewire::test(ActivityForm::class)
            ->call('save')
            ->assertHasErrors([
                'opportunity_id' => 'required',
                'descricao' => 'required',
                'proxima_accao' => 'required',
            ]);

        $this->assertSame(0, Activity::count());
    }

    public function test_valid_form_creates_pending_activity(): void
    {
        $user = $this->user();
        $opportunity = $this->opportunity();
        $this->actingAs($user);

        Livewire::test(ActivityForm::class)
            ->set('tipo', 'reuniao')
            ->set('opportunity_id', $opportunity->id)
            ->set('descricao', 'Reunião de objectivos com o cliente')
            ->set('proxima_accao', 'Enviar proposta revista')
            ->set('proxima_accao_prazo', now()->addDays(3)->toDateString())
            ->set('data', now()->toDateString())
            ->call('save')
            ->assertRedirect(route('agenda'));

        $this->assertTrue(session()->has('actividade_criada'));

        $this->assertDatabaseHas('activities', [
            'opportunity_id' => $opportunity->id,
            'user_id' => $user->id,
            'tipo' => 'reuniao',
            'descricao' => 'Reunião de objectivos com o cliente',
            'status' => 'pendente',
        ]);
    }

    public function test_calendar_page_renders_prototype_structure(): void
    {
        $this->actingAs($this->user())
            ->get('/agenda/calendario')
            ->assertOk()
            ->assertSee('Calendário')
            ->assertSee('Ver todas as actividades')
            ->assertSee('Legenda de tipos')
            ->assertSee('Eventos e Agendamentos')
            ->assertSee('agenda-calendar', false)
            ->assertSee('id="renderRange"', false)
            ->assertSee('id="viewAllSchedules"', false)
            ->assertSee('Próximos')
            ->assertSee('Nova Actividade')
            ->assertSee('Diária')
            ->assertSee('Mensal')
            ->assertSee('Dom', false)
            ->assertSee('Sáb', false)
            ->assertSee('Hoje');
    }

    public function test_calendar_month_grid_is_always_six_weeks(): void
    {
        $this->actingAs($this->user());

        $html = Livewire::test(ActivityCalendar::class)->html();

        $this->assertSame(42, substr_count($html, 'class="agenda-calendar-cell'));
    }

    public function test_calendar_switches_between_views(): void
    {
        $this->actingAs($this->user());

        $component = Livewire::test(ActivityCalendar::class);

        $component->call('setView', 'day');
        $this->assertSame(1, substr_count($component->html(), 'class="agenda-calendar-cell'));
        $this->assertStringContainsString('Diária', $component->html());

        $component->call('setView', 'week');
        $this->assertSame(7, substr_count($component->html(), 'class="agenda-calendar-cell'));

        $component->call('setView', 'weeks2');
        $this->assertSame(14, substr_count($component->html(), 'class="agenda-calendar-cell'));

        $component->call('setView', 'month');
        $this->assertSame(42, substr_count($component->html(), 'class="agenda-calendar-cell'));
    }

    public function test_calendar_unknown_view_falls_back_to_month(): void
    {
        $this->actingAs($this->user());

        $component = Livewire::test(ActivityCalendar::class)
            ->call('setView', 'bogus')
            ->assertSet('view', 'month');

        $this->assertSame(42, substr_count($component->html(), 'class="agenda-calendar-cell'));
    }

    public function test_calendar_shows_events_inside_the_range(): void
    {
        $user = $this->user();
        $opportunity = $this->opportunity();

        Activity::factory()->create([
            'opportunity_id' => $opportunity->id,
            'user_id' => $user->id,
            'descricao' => 'Evento dentro do calendario',
            'data' => now()->toDateString(),
            'hora' => '10:00',
        ]);

        $this->actingAs($user);

        $html = Livewire::test(ActivityCalendar::class)->html();

        $this->assertStringContainsString('agenda-event', $html);
        $this->assertStringContainsString('Evento dentro do calendario', $html);
    }

    public function test_calendar_type_filters_control_the_grid(): void
    {
        $user = $this->user();
        $opportunity = $this->opportunity();

        Activity::factory()->create([
            'opportunity_id' => $opportunity->id,
            'user_id' => $user->id,
            'tipo' => ActivityType::VISITA,
            'descricao' => 'Visita escondida pela legenda',
            'data' => now()->toDateString(),
        ]);

        $this->actingAs($user);

        $component = Livewire::test(ActivityCalendar::class);

        $this->assertSame(1, substr_count($component->html(), 'class="agenda-event"'));

        $component
            ->set('types', ['chamada'])
            ->assertSet('types', ['chamada'])
            ->assertSet('allVisible', false);

        $this->assertSame(0, substr_count($component->html(), 'class="agenda-event"'));

        $component
            ->set('allVisible', true)
            ->assertSet('allVisible', true);

        $this->assertSame(1, substr_count($component->html(), 'class="agenda-event"'));

        $component
            ->set('types', ['bogus', 'chamada'])
            ->assertSet('types', ['chamada']);
    }

    public function test_calendar_event_click_opens_details_modal(): void
    {
        $user = $this->user();
        $opportunity = $this->opportunity();

        $activity = Activity::factory()->create([
            'opportunity_id' => $opportunity->id,
            'user_id' => $user->id,
            'descricao' => 'Reuniao com cliente para detalhes',
            'data' => now()->toDateString(),
            'hora' => '10:30',
            'status' => ActivityStatus::PENDENTE,
            'proxima_accao' => 'Enviar proposta comercial',
            'proxima_accao_prazo' => now()->addDays(3)->toDateString(),
        ]);

        $this->actingAs($user);

        $component = Livewire::test(ActivityCalendar::class)
            ->call('abrirDetalhe', $activity->id)
            ->assertSet('detalheId', $activity->id)
            ->assertDispatched('modal-actividade', aberto: true)
            ->assertSee('id="modalDetalheActividade"', false)
            ->assertSee('Reuniao com cliente para detalhes')
            ->assertSee($opportunity->nome)
            ->assertSee('Enviar proposta comercial')
            ->assertSee('Editar')
            ->assertSee('Concluir')
            ->assertSee('Eliminar');

        $html = $component->html();
        $this->assertStringContainsString('class="modal fade show d-block"', $html);
        $this->assertStringContainsString('wire:click="abrirDetalhe(', $html);
        $this->assertStringContainsString('href="javascript:void(0);"', $html);

        $component
            ->call('abrirDetalhe', 999999)
            ->assertSet('detalheId', $activity->id);

        $component
            ->call('fecharDetalhe')
            ->assertSet('detalheId', null)
            ->assertDispatched('modal-actividade', aberto: false);

        $this->assertStringNotContainsString('class="modal fade show d-block"', $component->html());
    }

    public function test_calendar_detail_modal_concludes_and_deletes(): void
    {
        $user = $this->user();
        $opportunity = $this->opportunity();

        $activity = Activity::factory()->pendente()->create([
            'opportunity_id' => $opportunity->id,
            'user_id' => $user->id,
            'descricao' => 'Chamada para concluir pelo modal',
            'data' => now()->toDateString(),
        ]);

        $this->actingAs($user);

        $component = Livewire::test(ActivityCalendar::class)
            ->call('abrirDetalhe', $activity->id)
            ->assertSet('detalheId', $activity->id);

        $component->call('concluirDetalhe');
        $this->assertSame(ActivityStatus::CONCLUIDA, $activity->fresh()->status);
        $component->assertSet('detalheId', $activity->id);

        $component->call('eliminarDetalhe');
        $component->assertSet('detalheId', null);
        $component->assertDispatched('modal-actividade', aberto: false);
        $this->assertDatabaseMissing('activities', ['id' => $activity->id]);
    }

    /**
     * resposta.md §11: o lembrete das 20h tem de criar notificação
     * no sistema (tabela alerts), além do email — e sem duplicar
     * se o comando for re-executado para a mesma data.
     */
    public function test_reminder_creates_system_alert_and_avoids_duplicates(): void
    {
        $user = $this->user();
        $opportunity = $this->opportunity();
        $amanha = now()->addDay();

        Activity::factory()->pendente()->create([
            'opportunity_id' => $opportunity->id,
            'user_id' => $user->id,
            'descricao' => 'Visita de amanhã',
            'data' => $amanha->toDateString(),
        ]);

        $this->artisan('agenda:reminder', ['--date' => $amanha->toDateString()])
            ->assertSuccessful();

        $this->assertSame(1, Alert::where('tipo', AlertType::AGENDA)->count());
        $alerta = Alert::where('tipo', AlertType::AGENDA)->firstOrFail();
        $this->assertSame($user->id, $alerta->user_id);
        $this->assertFalse($alerta->lido);
        $this->assertStringContainsString($amanha->format('d/m/Y'), $alerta->mensagem);
        $this->assertStringContainsString('1 actividade(s)', $alerta->mensagem);
        $this->assertNull($alerta->entidade);

        // Re-execução para a mesma data não duplica a notificação.
        $this->artisan('agenda:reminder', ['--date' => $amanha->toDateString()])
            ->assertSuccessful();

        $this->assertSame(1, Alert::where('tipo', AlertType::AGENDA)->count());
    }

    public function test_reminder_dry_run_does_not_create_alert(): void
    {
        $user = $this->user();
        $opportunity = $this->opportunity();
        $amanha = now()->addDay();

        Activity::factory()->pendente()->create([
            'opportunity_id' => $opportunity->id,
            'user_id' => $user->id,
            'data' => $amanha->toDateString(),
        ]);

        $this->artisan('agenda:reminder', [
            '--date' => $amanha->toDateString(),
            '--dry-run' => true,
        ])->assertSuccessful();

        $this->assertSame(0, Alert::where('tipo', AlertType::AGENDA)->count());
    }

    // ------------------------------------------------------------------
    // BLOCO 3 — tipo "Outros" com texto personalizado (tipo_outros)
    // ------------------------------------------------------------------

    public function test_outros_type_requires_custom_text(): void
    {
        $user = $this->user();
        $opportunity = $this->opportunity();
        $this->actingAs($user);

        Livewire::test(ActivityForm::class)
            ->set('tipo', 'outros')
            ->set('opportunity_id', $opportunity->id)
            ->set('descricao', 'Actividade com tipo personalizado')
            ->set('proxima_accao', 'Accao qualquer')
            ->set('proxima_accao_prazo', now()->addDays(2)->toDateString())
            ->set('data', now()->toDateString())
            ->call('save')
            ->assertHasErrors(['tipo_outros']);

        $this->assertSame(0, Activity::count());
    }

    public function test_outros_type_saves_custom_text_and_shows_in_list(): void
    {
        $user = $this->user();
        $opportunity = $this->opportunity();
        $this->actingAs($user);

        Livewire::test(ActivityForm::class)
            ->set('tipo', 'outros')
            ->set('tipo_outros', 'Visita técnica à bomba')
            ->set('opportunity_id', $opportunity->id)
            ->set('descricao', 'Actividade com tipo Outros')
            ->set('proxima_accao', 'Ligar ao cliente')
            ->set('proxima_accao_prazo', now()->addDays(2)->toDateString())
            ->set('data', now()->toDateString())
            ->call('save')
            ->assertRedirect(route('agenda'));

        $this->assertDatabaseHas('activities', [
            'tipo' => 'outros',
            'tipo_outros' => 'Visita técnica à bomba',
            'descricao' => 'Actividade com tipo Outros',
        ]);

        // Lista mostra o texto personalizado (badge), nao "Outros".
        $this->get('/agenda')
            ->assertOk()
            ->assertSee('Visita técnica à bomba');
    }

    public function test_other_types_do_not_persist_tipo_outros(): void
    {
        $user = $this->user();
        $opportunity = $this->opportunity();
        $this->actingAs($user);

        Livewire::test(ActivityForm::class)
            ->set('tipo', 'chamada')
            ->set('tipo_outros', 'Texto que nao deve guardar')
            ->set('opportunity_id', $opportunity->id)
            ->set('descricao', 'Chamada normal')
            ->set('proxima_accao', 'Enviar email')
            ->set('proxima_accao_prazo', now()->addDays(2)->toDateString())
            ->set('data', now()->toDateString())
            ->call('save')
            ->assertRedirect(route('agenda'));

        $this->assertDatabaseHas('activities', [
            'tipo' => 'chamada',
            'tipo_outros' => null,
        ]);
    }

    public function test_calendar_detail_modal_shows_custom_outros_text(): void
    {
        $user = $this->user();
        $opportunity = $this->opportunity();

        $activity = Activity::factory()->create([
            'opportunity_id' => $opportunity->id,
            'user_id' => $user->id,
            'tipo' => ActivityType::OUTROS,
            'tipo_outros' => 'Workshop de segurança',
            'descricao' => 'Actividade Outros no calendario',
            'data' => now()->toDateString(),
            'hora' => '11:00',
        ]);

        $this->actingAs($user);

        Livewire::test(ActivityCalendar::class)
            ->call('abrirDetalhe', $activity->id)
            ->assertSee('Workshop de segurança');
    }

    public function test_outros_form_field_shows_and_hides_conditionally(): void
    {
        $this->opportunity();
        $this->actingAs($this->user());

        $component = Livewire::test(ActivityForm::class);

        // Por omissao (tipo=visita) nao mostra o campo.
        $component->assertDontSee('Especifique');

        // Quando tipo=outros, mostra o campo.
        $component
            ->set('tipo', 'outros')
            ->assertSee('Especifique');
    }
}
