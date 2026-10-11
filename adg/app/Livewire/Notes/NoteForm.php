<?php

namespace App\Livewire\Notes;

use App\Enums\NoteType;
use App\Models\Note;
use App\Models\NoteAttachment;
use App\Models\Opportunity;
use App\Models\User;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

/**
 * BLOCO 5 (mudancas.md): formulario de nota da aba Notas da oportunidade.
 * Nota = titulo opcional + conteudo obrigatorio + tipo + anexos privados
 * (storage/app/private/notes/{note_id}/).
 */
class NoteForm extends Component
{
    use WithFileUploads;

    public int $opportunityId;

    public ?int $noteId = null;

    public string $titulo = '';

    public string $conteudo = '';

    public string $tipo = 'informacao';

    /** @var array<int, TemporaryUploadedFile> */
    public array $anexos = [];

    public function mount(int $opportunityId, ?int $noteId = null): void
    {
        $this->opportunityId = $opportunityId;

        if ($noteId !== null) {
            $note = Note::query()
                ->where('opportunity_id', $opportunityId)
                ->findOrFail($noteId);

            $this->noteId = $note->id;
            $this->titulo = (string) $note->titulo;
            $this->conteudo = (string) $note->conteudo;
            $this->tipo = $note->tipo->value;
        }
    }

    protected function rules(): array
    {
        return [
            'titulo' => ['nullable', 'string', 'max:255'],
            'conteudo' => ['required', 'string', 'max:20000'],
            'tipo' => ['required', 'in:'.implode(',', array_column(NoteType::cases(), 'value'))],
            'anexos' => ['nullable', 'array'],
            'anexos.*' => ['file', 'max:10240'],
        ];
    }

    public function save(): void
    {
        $data = $this->validate();

        $data['titulo'] = trim($data['titulo']) !== '' ? $data['titulo'] : null;

        if ($this->noteId !== null) {
            $note = Note::query()
                ->where('opportunity_id', $this->opportunityId)
                ->findOrFail($this->noteId);
            $note->fill($data);
            $note->save();
        } else {
            $note = Note::create([
                ...$data,
                'opportunity_id' => $this->opportunityId,
                'user_id' => auth()->id() ?? User::query()->value('id'),
            ]);
        }

        // Anexos: guardados em storage privado notes/{note_id}/.
        foreach ($this->anexos as $upload) {
            $caminho = $upload->store('notes/'.$note->id, 'local');

            NoteAttachment::create([
                'note_id' => $note->id,
                'nome_original' => $upload->getClientOriginalName(),
                'caminho' => $caminho,
                'tamanho' => $upload->getSize(),
                'mime_type' => $upload->getMimeType(),
            ]);
        }

        // O alerta de sucesso e mostrado pela lista via evento note-saved
        // (propriedade flashSuccess) — session flash nao atravessa o
        // ciclo de requests do Livewire.
        $this->dispatch('note-saved');
        $this->resetForm();
    }

    public function cancel(): void
    {
        $this->resetForm();
        $this->dispatch('note-form-closed');
    }

    private function resetForm(): void
    {
        $this->noteId = null;
        $this->titulo = '';
        $this->conteudo = '';
        $this->tipo = 'informacao';
        $this->anexos = [];
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.notes.note-form', [
            'tipos' => NoteType::cases(),
            'editing' => $this->noteId !== null,
            'opportunity' => Opportunity::findOrFail($this->opportunityId),
        ]);
    }
}
