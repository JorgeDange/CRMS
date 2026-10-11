<?php

namespace App\Livewire\Notes;

use App\Enums\NoteType;
use App\Models\Note;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * BLOCO 5: lista cronologica de notas da oportunidade (fixadas no topo),
 * com pesquisa, filtro por tipo, fixar/desafixar e eliminar.
 */
class NoteList extends Component
{
    public int $opportunityId;

    public string $search = '';

    public string $typeFilter = '';

    public bool $showForm = false;

    public ?int $editingNoteId = null;

    public bool $flashSuccess = false;

    public function mount(int $opportunityId): void
    {
        $this->opportunityId = $opportunityId;
    }

    public function openForm(): void
    {
        $this->editingNoteId = null;
        $this->showForm = true;
    }

    public function edit(int $noteId): void
    {
        $exists = Note::query()
            ->where('opportunity_id', $this->opportunityId)
            ->where('id', $noteId)
            ->exists();

        if (! $exists) {
            return;
        }

        $this->editingNoteId = $noteId;
        $this->showForm = true;
    }

    #[On('note-saved')]
    public function closeFormAfterSave(): void
    {
        $this->showForm = false;
        $this->editingNoteId = null;
        $this->flashSuccess = true;
    }

    #[On('note-form-closed')]
    public function closeForm(): void
    {
        $this->showForm = false;
        $this->editingNoteId = null;
    }

    public function filterType(string $tipo): void
    {
        $this->typeFilter = $this->typeFilter === $tipo ? '' : $tipo;
    }

    public function toggleFix(int $noteId): void
    {
        $note = Note::query()
            ->where('opportunity_id', $this->opportunityId)
            ->find($noteId);

        if (! $note) {
            return;
        }

        $note->fixada = ! $note->fixada;
        $note->save();
    }

    public function remove(int $noteId): void
    {
        $note = Note::query()
            ->where('opportunity_id', $this->opportunityId)
            ->find($noteId);

        if (! $note) {
            return;
        }

        // Apagar anexos fisicos (storage privado).
        foreach ($note->attachments as $attachment) {
            Storage::disk('local')->delete($attachment->caminho);
        }

        $note->delete();

        $this->dispatch('note-removed');
    }

    public function render()
    {
        $notes = Note::with(['user', 'attachments'])
            ->where('opportunity_id', $this->opportunityId)
            ->when($this->search !== '', function ($query): void {
                $like = '%'.$this->search.'%';
                $query->where(fn ($q) => $q
                    ->where('titulo', 'like', $like)
                    ->orWhere('conteudo', 'like', $like));
            })
            ->when($this->typeFilter !== '', fn ($query) => $query->where('tipo', $this->typeFilter))
            ->orderByDesc('fixada')
            ->orderByDesc('created_at')
            ->get();

        return view('livewire.notes.note-list', [
            'notes' => $notes,
            'tipos' => NoteType::cases(),
        ]);
    }
}
