{{-- BLOCO 5: lista cronologica de notas da oportunidade --}}
<div>
    <div class="card mb-3">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h5 class="card-title mb-0">Notas <span class="text-muted fs-13">({{ $notes->count() }})</span></h5>
            <div class="hstack gap-2">
                <div class="dropdown">
                    <a href="javascript:void(0)" class="btn btn-light-brand btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="feather-filter me-1"></i>Filtrar
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <h6 class="dropdown-header">Tipo</h6>
                        <a class="dropdown-item {{ $typeFilter === '' ? 'active' : '' }}" href="javascript:void(0)" wire:click="filterType('')">
                            <span>Todas</span>
                        </a>
                        @foreach ($tipos as $tipo)
                            <a class="dropdown-item {{ $typeFilter === $tipo->value ? 'active' : '' }}"
                               href="javascript:void(0)" wire:click="filterType('{{ $tipo->value }}')">
                                <i class="{{ $tipo->icon() }} me-2"></i>
                                <span>{{ $tipo->label() }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
                <div class="search-form-inner">
                    <input type="search" class="form-control form-control-sm" placeholder="Pesquisar notas…"
                           wire:model.live.debounce.300ms="search">
                </div>
                @if (! $showForm)
                    <button type="button" class="btn btn-primary btn-sm" wire:click="openForm">
                        <i class="feather-plus me-1"></i>Adicionar nota
                    </button>
                @endif
            </div>
        </div>
    </div>

    @if ($showForm)
        @livewire('notes.note-form', ['opportunityId' => $opportunityId, 'noteId' => $editingNoteId], key('note-form-'.$opportunityId.'-'.($editingNoteId ?? 'new')))
    @endif

    @if ($flashSuccess)
        <div class="alert alert-success d-flex align-items-center justify-content-between" role="alert">
            <span><i class="feather-check-circle me-2"></i>Nota guardada com sucesso.</span>
            <button type="button" class="btn-close" aria-label="Fechar" wire:click="$set('flashSuccess', false)"></button>
        </div>
    @endif

    @forelse ($notes as $note)
        <div class="card mb-3 {{ $note->fixada ? 'border-primary' : '' }}" wire:key="note-{{ $note->id }}">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge {{ $note->tipo->badge() }}">
                        <i class="{{ $note->tipo->icon() }} me-1"></i>{{ $note->tipo->label() }}
                    </span>
                    @if ($note->fixada)
                        <span class="badge bg-soft-warning text-warning"><i class="feather-star me-1"></i>Fixada</span>
                    @endif
                    <h6 class="card-title mb-0">{{ $note->titulo ?: 'Sem título' }}</h6>
                </div>
                <div class="hstack gap-1">
                    <button type="button" class="btn btn-xs btn-light-brand"
                            title="{{ $note->fixada ? 'Desafixar' : 'Fixar no topo' }}"
                            wire:click="toggleFix({{ $note->id }})">
                        <i class="feather-star {{ $note->fixada ? 'text-warning' : '' }}"></i>
                    </button>
                    <button type="button" class="btn btn-xs btn-light-brand" title="Editar"
                            wire:click="edit({{ $note->id }})">
                        <i class="feather-edit-2"></i>
                    </button>
                    <button type="button" class="btn btn-xs btn-light-danger" title="Eliminar"
                            @click.prevent="$store.confirmar.abrir({ titulo: 'Eliminar nota', mensagem: 'Eliminar esta nota e os seus anexos? Esta acção não pode ser desfeita.', confirmar: 'Eliminar' }).then(ok => ok && $wire.remove({{ $note->id }}))">
                        <i class="feather-trash-2"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <p class="mb-2">{!! nl2br(e($note->conteudo)) !!}</p>
                @if ($note->attachments->isNotEmpty())
                    <div class="mt-3 pt-3 border-top">
                        <span class="fs-12 fw-semibold text-muted d-block mb-2">
                            <i class="feather-paperclip me-1"></i>Anexos ({{ $note->attachments->count() }})
                        </span>
                        <div class="hstack gap-2 flex-wrap">
                            @foreach ($note->attachments as $attachment)
                                <span class="badge bg-soft-gray text-muted">
                                    <i class="feather-file me-1"></i>{{ $attachment->nome_original }}
                                    <span class="ms-1">({{ round($attachment->tamanho / 1024, 1) }} KB)</span>
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif
                <div class="fs-12 text-muted mt-3">
                    <i class="feather-user me-1"></i>{{ $note->user?->name ?? '—' }}
                    · {{ $note->created_at?->format('d/m/Y H:i') ?? '—' }}
                    @if ($note->updated_at && ! $note->created_at?->eq($note->updated_at))
                        · editada {{ $note->updated_at->format('d/m/Y H:i') }}
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="feather-file-text fs-1 text-muted"></i>
                <p class="text-muted mt-3 mb-3">Não existem notas para os filtros seleccionados.</p>
                @if (! $showForm)
                    <button type="button" class="btn btn-primary btn-sm" wire:click="openForm">
                        <i class="feather-plus me-1"></i>Adicionar nota
                    </button>
                @endif
            </div>
        </div>
    @endforelse
</div>
