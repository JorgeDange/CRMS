<?php

namespace Tests\Feature;

use App\Enums\ActivityStatus;
use App\Enums\ActivityType;
use App\Livewire\Activities\ActivityCalendar;
use App\Livewire\Activities\ActivityForm;
use App\Livewire\Activities\ActivityList;
use App\Models\Activity;
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
            ->assertSee('>Actividade</a>', false);

        // Oportunidades, Agenda Comercial, Conciliações e Contratos são acordeões (o resto continua "Em breve").
        $this->assertSame(4, substr_count($response->getContent(), 'nxl-hasmenu'));
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
            ->assertSee('content-sidebar', false)
            ->assertSee('content-area-header', false)
            ->assertSee('single-task-list', false)
            ->assertSee('As minhas actividades')
            ->assertSee('Nova Actividade')
            ->assertSee('Videoconferência com Importadora Kinaxixi')
            ->assertSee('SIL – Sociedade Importadora')
            ->assertSee('Enviar ata da reunião')
            ->assertSee('Hoje', false)
            ->assertSee('Pendente')
            ->assertSee('Chamadas')
            ->assertSee('Mais recentes');
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
            ->assertSee('Regras')
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
            ->assertSee('Legenda de tipos (ActivityType)')
            ->assertSee('Eventos e Agendamentos')
            ->assertSee('agenda-calendar', false)
            ->assertSee('id="renderRange"', false)
            ->assertSee('id="calendarList"', false)
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
}
