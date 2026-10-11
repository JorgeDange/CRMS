@use('App\Services\OpportunityService')

{{-- Paineis copiados de leads-view.html + aba Relatorio de Visita (PROMPT_MASTER Sprint 3) --}}
<div id="opportunity-detail">
    <div class="tab-content">
        {{-- [ Resumo ] --}}
        <div class="tab-pane fade {{ $activeTab === 'profile' ? 'show active' : '' }}" id="profileTab" role="tabpanel">
            <div class="card card-body lead-info">
                <div class="mb-4 d-flex align-items-center justify-content-between">
                    <h5 class="fw-bold mb-0">
                        <span class="d-block mb-2">Dados da Oportunidade</span>
                    </h5>
                    <span class="badge bg-soft-{{ $opportunity->fase_atual->badgeVariant() }} text-{{ $opportunity->fase_atual->badgeVariant() }}">
                        {{ \App\Livewire\Opportunities\Pipeline::COLUMNS[$opportunity->fase_atual->value]['label'] ?? $opportunity->fase_atual->label() }}
                    </span>
                </div>
                <div class="row mb-4">
                    <div class="col-lg-3 fw-medium text-muted">Título</div>
                    <div class="col-lg-9 fw-semibold">{{ $opportunity->titulo }}</div>
                </div>
                <div class="row mb-4">
                    <div class="col-lg-3 fw-medium text-muted">Cliente</div>
                    <div class="col-lg-9">{{ $opportunity->empresa ?? $opportunity->nome }}</div>
                </div>
                <div class="row mb-4">
                    <div class="col-lg-3 fw-medium text-muted">Valor</div>
                    <div class="col-lg-9 fw-bold text-dark">{{ OpportunityService::formatKz($opportunity->valor_estimado) }}</div>
                </div>
                <div class="row mb-4">
                    <div class="col-lg-3 fw-medium text-muted">Probabilidade</div>
                    <div class="col-lg-9">
                        {{ $opportunity->probabilidade }}% · Ponderado:
                        <strong>{{ OpportunityService::formatKz($opportunity->valor_estimado * $opportunity->probabilidade / 100) }}</strong>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-lg-3 fw-medium text-muted">Data de fecho prevista</div>
                    <div class="col-lg-9">{{ $opportunity->data_fecho?->format('d/m/Y') ?? '—' }}</div>
                </div>
                <div class="row mb-4">
                    <div class="col-lg-3 fw-medium text-muted">Responsável</div>
                    <div class="col-lg-9 hstack gap-2">
                        <div class="avatar-image avatar-sm rounded-circle">
                            <img src="{{ asset('assets/images/avatar/1.png') }}" alt="" class="img-fluid">
                        </div>
                        {{ auth()->user()?->name ?? '—' }}
                    </div>
                </div>
                <div class="row mb-0">
                    <div class="col-lg-3 fw-medium text-muted">Criada em</div>
                    <div class="col-lg-9">{{ $opportunity->created_at?->format('d/m/Y') ?? '—' }}</div>
                </div>
            </div>
            <hr>
            <div class="card card-body general-info">
                <div class="mb-4">
                    <h5 class="fw-bold mb-0">
                        <span class="d-block mb-2">Descrição</span>
                        <span class="fs-12 fw-normal text-muted d-block">Contexto comercial</span>
                    </h5>
                </div>
                <p class="mb-0">{{ $opportunity->interesse ?: 'Sem descrição.' }}</p>
            </div>
        </div>

        {{-- [ Proposta ] --}}
        <div class="tab-pane fade {{ $activeTab === 'proposal' ? 'show active' : '' }}" id="proposalTab" role="tabpanel">
            <div class="card card-body">
                <div class="d-flex align-items-center justify-content-center" style="min-height: 300px">
                    <div class="text-center">
                        <h2 class="fs-16 fw-semibold">Ainda sem propostas!</h2>
                        <p class="fs-12 text-muted">Ainda não foram criadas propostas.</p>
                        <a href="javascript:void(0);" class="avatar-text bg-soft-primary text-primary mx-auto" data-bs-toggle="tooltip" title="Criar proposta">
                            <i class="feather-plus"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- [ Actividades ] — BLOCO 4: lista filtrada por oportunidade --}}
        <div class="tab-pane fade {{ $activeTab === 'tasks' ? 'show active' : '' }}" id="tasksTab" role="tabpanel">
            @livewire('opportunities.opportunity-activities', ['opportunityId' => $opportunity->id], key('opp-activities-'.$opportunity->id))
        </div>

        {{-- [ Notas ] — BLOCO 5 --}}
        <div class="tab-pane fade {{ $activeTab === 'notes' ? 'show active' : '' }}" id="notesTab" role="tabpanel">
            @livewire('notes.note-list', ['opportunityId' => $opportunity->id], key('opp-notes-'.$opportunity->id))
        </div>

        {{-- [ Relatório de Visita ] — aba nova (PROMPT_MASTER Sprint 3) --}}
        <div class="tab-pane fade {{ $activeTab === 'visit' ? 'show active' : '' }}" id="visitTab" role="tabpanel">
            @livewire('activities.visit-report-form', ['opportunityId' => $opportunity->id])

            @forelse ($reports as $report)
                <div class="card mb-3">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="card-title mb-0">Relatório de visita — {{ $report->data_visita?->format('d/m/Y') }}</h5>
                        <span class="fs-12 text-muted"><i class="feather-user me-1"></i>{{ $report->user?->name ?? '—' }}</span>
                    </div>
                    <div class="card-body">
                        @if ($report->objectivo)
                            <div class="row mb-3">
                                <div class="col-lg-3 fw-medium text-muted">Objectivo</div>
                                <div class="col-lg-9">{{ $report->objectivo }}</div>
                            </div>
                        @endif
                        @if ($report->pessoas_presentes)
                            <div class="row mb-3">
                                <div class="col-lg-3 fw-medium text-muted">Pessoas presentes</div>
                                <div class="col-lg-9">{{ $report->pessoas_presentes }}</div>
                            </div>
                        @endif
                        <div class="row mb-3">
                            <div class="col-lg-3 fw-medium text-muted">Assuntos tratados</div>
                            <div class="col-lg-9">{!! nl2br(e($report->assuntos_tratados)) !!}</div>
                        </div>
                        @if ($report->resultado)
                            <div class="row mb-3">
                                <div class="col-lg-3 fw-medium text-muted">Resultado</div>
                                <div class="col-lg-9">{{ $report->resultado }}</div>
                            </div>
                        @endif
                        @if ($report->proxima_accao)
                            <div class="row mb-0">
                                <div class="col-lg-3 fw-medium text-muted">Próxima acção</div>
                                <div class="col-lg-9">{{ $report->proxima_accao }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="card mb-3">
                    <div class="card-body text-center text-muted fs-13">
                        Ainda sem relatórios de visita.
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>
