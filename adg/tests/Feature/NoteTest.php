<?php

namespace Tests\Feature;

use App\Enums\NoteType;
use App\Livewire\Notes\NoteForm;
use App\Livewire\Notes\NoteList;
use App\Models\Note;
use App\Models\NoteAttachment;
use App\Models\Opportunity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class NoteTest extends TestCase
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

    public function test_notes_tab_renders_placeholder_when_empty(): void
    {
        $user = $this->user();
        $opportunity = $this->opportunity();

        $this->actingAs($user)
            ->get('/pipeline/'.$opportunity->id)
            ->assertOk()
            ->assertSee('Não existem notas para os filtros seleccionados.')
            ->assertSee('Adicionar nota');
    }

    public function test_note_is_created_with_title_content_and_type(): void
    {
        $user = $this->user();
        $opportunity = $this->opportunity();
        $this->actingAs($user);

        Livewire::test(NoteForm::class, ['opportunityId' => $opportunity->id])
            ->set('titulo', 'Condições comerciais')
            ->set('conteudo', 'O cliente aceitou o prazo de 30 dias.')
            ->set('tipo', 'decisao')
            ->call('save')
            ->assertDispatched('note-saved')
            ->assertSet('titulo', '')
            ->assertSet('conteudo', '');

        $this->assertDatabaseHas('notes', [
            'opportunity_id' => $opportunity->id,
            'user_id' => $user->id,
            'titulo' => 'Condições comerciais',
            'tipo' => 'decisao',
        ]);
    }

    public function test_note_content_is_required(): void
    {
        $this->actingAs($this->user());

        Livewire::test(NoteForm::class, ['opportunityId' => $this->opportunity()->id])
            ->call('save')
            ->assertHasErrors(['conteudo' => 'required']);

        $this->assertSame(0, Note::count());
    }

    public function test_note_with_empty_title_is_saved_as_null(): void
    {
        $user = $this->user();
        $opportunity = $this->opportunity();
        $this->actingAs($user);

        Livewire::test(NoteForm::class, ['opportunityId' => $opportunity->id])
            ->set('titulo', '   ')
            ->set('conteudo', 'Nota sem titulo.')
            ->call('save');

        $this->assertDatabaseHas('notes', [
            'opportunity_id' => $opportunity->id,
            'titulo' => null,
        ]);
    }

    public function test_note_edit_loads_and_saves(): void
    {
        $user = $this->user();
        $opportunity = $this->opportunity();
        $note = Note::factory()->create([
            'opportunity_id' => $opportunity->id,
            'user_id' => $user->id,
            'titulo' => 'Nota original',
            'conteudo' => 'Conteudo original.',
            'tipo' => NoteType::INFORMACAO,
        ]);
        $this->actingAs($user);

        Livewire::test(NoteForm::class, ['opportunityId' => $opportunity->id, 'noteId' => $note->id])
            ->assertSet('titulo', 'Nota original')
            ->assertSet('conteudo', 'Conteudo original.')
            ->assertSet('tipo', 'informacao')
            ->set('titulo', 'Nota actualizada')
            ->set('conteudo', 'Conteudo actualizado.')
            ->call('save')
            ->assertDispatched('note-saved');

        $this->assertDatabaseHas('notes', [
            'id' => $note->id,
            'titulo' => 'Nota actualizada',
            'conteudo' => 'Conteudo actualizado.',
        ]);
    }

    public function test_note_belongs_to_opportunity_only(): void
    {
        $user = $this->user();
        $opportunity = $this->opportunity();
        $outra = Opportunity::factory()->create(['nome' => 'Outro Cliente']);

        Note::factory()->create([
            'opportunity_id' => $opportunity->id,
            'user_id' => $user->id,
            'titulo' => 'Nota desta opp',
        ]);

        Note::factory()->create([
            'opportunity_id' => $outra->id,
            'user_id' => $user->id,
            'titulo' => 'Nota de outra opp',
        ]);

        $this->actingAs($user);

        Livewire::test(NoteList::class, ['opportunityId' => $opportunity->id])
            ->assertSee('Nota desta opp')
            ->assertDontSee('Nota de outra opp');
    }

    public function test_fixed_notes_appear_first(): void
    {
        $user = $this->user();
        $opportunity = $this->opportunity();

        Note::factory()->create([
            'opportunity_id' => $opportunity->id,
            'user_id' => $user->id,
            'titulo' => 'Nota antiga fixada',
            'fixada' => true,
            'created_at' => now()->subDays(10),
        ]);

        Note::factory()->create([
            'opportunity_id' => $opportunity->id,
            'user_id' => $user->id,
            'titulo' => 'Nota recente nao fixada',
            'fixada' => false,
        ]);

        $this->actingAs($user);

        $html = Livewire::test(NoteList::class, ['opportunityId' => $opportunity->id])->html();

        $posFixada = strpos($html, 'Nota antiga fixada');
        $posRecente = strpos($html, 'Nota recente nao fixada');

        $this->assertNotFalse($posFixada);
        $this->assertNotFalse($posRecente);
        $this->assertLessThan($posRecente, $posFixada, 'Nota fixada deve aparecer antes da não fixada.');
    }

    public function test_toggle_fix_and_remove_are_scoped_to_opportunity(): void
    {
        $user = $this->user();
        $opportunity = $this->opportunity();
        $outra = Opportunity::factory()->create(['nome' => 'Outro Cliente']);

        $note = Note::factory()->create([
            'opportunity_id' => $opportunity->id,
            'user_id' => $user->id,
            'fixada' => false,
        ]);

        $noteOutra = Note::factory()->create([
            'opportunity_id' => $outra->id,
            'user_id' => $user->id,
            'fixada' => false,
        ]);

        $this->actingAs($user);

        $component = Livewire::test(NoteList::class, ['opportunityId' => $opportunity->id]);

        // Toggle desta opp.
        $component->call('toggleFix', $note->id);
        $this->assertTrue($note->refresh()->fixada);

        // Id de outra opp e ignorado.
        $component->call('toggleFix', $noteOutra->id);
        $this->assertFalse($noteOutra->refresh()->fixada);

        // Remove desta opp.
        $component->call('remove', $note->id)->assertDispatched('note-removed');
        $this->assertDatabaseMissing('notes', ['id' => $note->id]);

        // Remove de outra opp e ignorado.
        $component->call('remove', $noteOutra->id);
        $this->assertDatabaseHas('notes', ['id' => $noteOutra->id]);
    }

    public function test_search_and_type_filter_work(): void
    {
        $user = $this->user();
        $opportunity = $this->opportunity();

        Note::factory()->create([
            'opportunity_id' => $opportunity->id,
            'user_id' => $user->id,
            'titulo' => 'Preços do cimento',
            'tipo' => NoteType::DECISAO,
        ]);

        Note::factory()->create([
            'opportunity_id' => $opportunity->id,
            'user_id' => $user->id,
            'titulo' => 'Lembrete de reunião',
            'tipo' => NoteType::LEMBRETE,
        ]);

        $this->actingAs($user);

        // Pesquisa.
        Livewire::test(NoteList::class, ['opportunityId' => $opportunity->id])
            ->set('search', 'cimento')
            ->assertSee('Preços do cimento')
            ->assertDontSee('Lembrete de reunião');

        // Filtro por tipo.
        Livewire::test(NoteList::class, ['opportunityId' => $opportunity->id])
            ->call('filterType', 'lembrete')
            ->assertSet('typeFilter', 'lembrete')
            ->assertSee('Lembrete de reunião')
            ->assertDontSee('Preços do cimento');
    }

    public function test_attachment_is_stored_in_private_disk_and_listed(): void
    {
        Storage::fake('local');

        $user = $this->user();
        $opportunity = $this->opportunity();
        $this->actingAs($user);

        $ficheiro = UploadedFile::fake()->create('proposta.pdf', 500, 'application/pdf');

        Livewire::test(NoteForm::class, ['opportunityId' => $opportunity->id])
            ->set('titulo', 'Nota com anexo')
            ->set('conteudo', 'Segue proposta em anexo.')
            ->set('anexos', [$ficheiro])
            ->call('save')
            ->assertDispatched('note-saved');

        $note = Note::where('titulo', 'Nota com anexo')->firstOrFail();

        $this->assertDatabaseHas('note_attachments', [
            'note_id' => $note->id,
            'nome_original' => 'proposta.pdf',
            'mime_type' => 'application/pdf',
        ]);

        Storage::disk('local')->assertExists(NoteAttachment::firstOrFail()->caminho);

        // Lista mostra o anexo.
        Livewire::test(NoteList::class, ['opportunityId' => $opportunity->id])
            ->assertSee('proposta.pdf')
            ->assertSee('Anexos (1)');
    }

    public function test_remove_deletes_physical_attachments(): void
    {
        Storage::fake('local');

        $user = $this->user();
        $opportunity = $this->opportunity();

        $note = Note::factory()->create([
            'opportunity_id' => $opportunity->id,
            'user_id' => $user->id,
        ]);

        $attachment = NoteAttachment::create([
            'note_id' => $note->id,
            'nome_original' => 'relatorio.docx',
            'caminho' => 'notes/'.$note->id.'/relatorio.docx',
            'tamanho' => 1024,
            'mime_type' => 'application/msword',
        ]);

        Storage::disk('local')->put($attachment->caminho, 'conteudo');

        $this->actingAs($user);

        Livewire::test(NoteList::class, ['opportunityId' => $opportunity->id])
            ->call('remove', $note->id);

        $this->assertDatabaseMissing('notes', ['id' => $note->id]);
        Storage::disk('local')->assertMissing($attachment->caminho);
    }

    public function test_form_show_and_close_flow(): void
    {
        $this->actingAs($this->user());
        $opportunity = $this->opportunity();

        $component = Livewire::test(NoteList::class, ['opportunityId' => $opportunity->id]);

        // Estado inicial: form escondido.
        $component->assertSet('showForm', false);

        // Abrir form.
        $component->call('openForm')->assertSet('showForm', true)->assertSet('editingNoteId', null);

        // Fechar via evento.
        $component->dispatch('note-form-closed')->assertSet('showForm', false);

        // Reabrir e fechar apos save.
        $component->call('openForm')->assertSet('showForm', true);
        $component->dispatch('note-saved')->assertSet('showForm', false)->assertSet('editingNoteId', null);
    }
}
