{{-- BLOCO 4: aba Actividades do detalhe da oportunidade (modo copia da Agenda) --}}
<div>
    <div class="card card-body mb-3">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h5 class="fw-bold mb-1">Actividades</h5>
                <p class="fs-12 text-muted mb-0">{{ $activities->count() }} actividade(s) registada(s) nesta oportunidade.</p>
            </div>
            <a href="{{ route('agenda.create', ['oportunidade' => $opportunityId]) }}"
               class="btn btn-primary btn-sm" wire:navigate>
                <i class="feather-plus me-2"></i>Nova Actividade
            </a>
        </div>
    </div>

    @if ($activities->isEmpty())
        <div class="card card-body">
            <div class="d-flex align-items-center justify-content-center" style="min-height: 200px">
                <div class="text-center">
                    <h2 class="fs-16 fw-semibold">Ainda sem actividades!</h2>
                    <p class="fs-12 text-muted">Ainda não foram criadas actividades.</p>
                    <a href="{{ route('agenda.create', ['oportunidade' => $opportunityId]) }}"
                       class="avatar-text bg-soft-primary text-primary mx-auto" title="Criar actividade" wire:navigate>
                        <i class="feather-plus"></i>
                    </a>
                </div>
            </div>
        </div>
    @else
        <div class="card stretch stretch-full">
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    @foreach ($activities as $activity)
                        <li class="single-task-list p-3 mb-3 border border-dashed rounded-3 {{ $activity->status->value === 'concluida' ? 'task-completed' : '' }}"
                            wire:key="opp-activity-{{ $activity->id }}">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3 me-3">
                                    <div class="custom-control custom-checkbox me-2">
                                        <input type="checkbox" class="custom-control-input"
                                               id="oppActivityCheck{{ $activity->id }}"
                                               {{ $activity->status->value === 'concluida' ? 'checked' : '' }}
                                               wire:click="toggle({{ $activity->id }})">
                                        <label class="custom-control-label c-pointer" for="oppActivityCheck{{ $activity->id }}"></label>
                                    </div>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="lh-base"><i class="feather-star"></i></div>
                                        <a href="{{ route('agenda.create', ['atividade' => $activity->uuid]) }}"
                                           class="single-task-list-link" wire:navigate>
                                            <div class="fs-13 fw-bold text-truncate-1-line">
                                                {{ $activity->descricao }}
                                                <span class="ms-2 badge {{ $activity->status->badgeClass() }}">{{ $activity->status->label() }}</span>
                                            </div>
                                            <div class="fs-12 fw-normal text-muted text-truncate-1-line">
                                                {{ $activity->data->format('d M, Y') }}{{ $activity->horaCurta() ? ' · '.$activity->horaCurta() : '' }}{{ $activity->proxima_accao ? ' · '.$activity->proxima_accao : '' }}
                                            </div>
                                        </a>
                                    </div>
                                </div>
                                <div class="d-flex flex-shrink-0 align-items-center gap-3">
                                    @if ($activity->status->value !== 'concluida')
                                        <button type="button"
                                                class="btn btn-xs btn-outline-success d-none d-sm-inline-flex"
                                                title="Concluir actividade"
                                                wire:click="toggle({{ $activity->id }})">
                                            <i class="feather-check me-1"></i>Concluir
                                        </button>
                                    @endif
                                    <div class="badge {{ $activity->tipo->badge() }} d-md-inline-block d-none">{{ $activity->tipo === \App\Enums\ActivityType::OUTROS ? $activity->tipoDisplay() : $activity->tipo->labelPlural() }}</div>
                                    @if ($activity->proxima_accao_prazo)
                                        <div class="d-md-inline-block d-none me-3">{{ $activity->proxima_accao_prazo->format('d M, Y') }}</div>
                                    @endif
                                    <div class="dropdown">
                                        <a href="javascript:void(0);" class="avatar-text avatar-md" data-bs-toggle="dropdown">
                                            <i class="feather-more-vertical"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a class="dropdown-item"
                                               href="{{ route('agenda.create', ['atividade' => $activity->uuid]) }}"
                                               wire:navigate>Editar actividade</a>
                                            <a class="dropdown-item" href="javascript:void(0);"
                                               @click.prevent="$store.confirmar.abrir({ titulo: 'Eliminar actividade', mensagem: 'Eliminar esta actividade? Esta acção não pode ser desfeita.', confirmar: 'Eliminar' }).then(ok => ok && $wire.remove({{ $activity->id }}))">Eliminar actividade</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif
</div>
