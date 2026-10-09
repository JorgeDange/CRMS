{{-- Detalhe do Contrato — copiado de crm-prototipo/projects-view.html (Sprint 7) --}}
<div id="contract-detail">
    <div class="tab-content">
        {{-- [ Visão Geral ] --}}
        <div class="tab-pane fade {{ $activeTab === 'overview' ? 'show active' : '' }}" id="overviewTab" role="tabpanel">
            <div class="card card-body lead-info">
                <div class="mb-4 d-flex align-items-center justify-content-between">
                    <h5 class="fw-bold mb-0">
                        <span class="d-block mb-2">Dados do Contrato</span>
                    </h5>
                    <span class="badge {{ $contract->estado->badgeClass() }}">
                        <i class="{{ $contract->estado->icon() }} me-1"></i>
                        {{ $contract->estado->label() }}
                    </span>
                </div>
                <div class="row mb-4">
                    <div class="col-lg-3 fw-medium text-muted">Nº Contrato</div>
                    <div class="col-lg-9 fw-semibold">{{ $contract->numero_contrato }}</div>
                </div>
                <div class="row mb-4">
                    <div class="col-lg-3 fw-medium text-muted">Cliente</div>
                    <div class="col-lg-9">{{ $contract->opportunity?->nome ?? '—' }}</div>
                </div>
                <div class="row mb-4">
                    <div class="col-lg-3 fw-medium text-muted">Produto</div>
                    <div class="col-lg-9">{{ $contract->product->nome ?? '—' }}</div>
                </div>
                <div class="row mb-4">
                    <div class="col-lg-3 fw-medium text-muted">Tipo</div>
                    <div class="col-lg-9">{{ $contract->tipo ?? '—' }}</div>
                </div>
                <div class="row mb-4">
                    <div class="col-lg-3 fw-medium text-muted">Data Início</div>
                    <div class="col-lg-9">{{ $contract->data_inicio?->format('d/m/Y') ?? '—' }}</div>
                </div>
                <div class="row mb-4">
                    <div class="col-lg-3 fw-medium text-muted">Data Fim</div>
                    <div class="col-lg-9">{{ $contract->data_fim?->format('d/m/Y') ?? '—' }}</div>
                </div>
                <div class="row mb-4">
                    <div class="col-lg-3 fw-medium text-muted">Valor</div>
                    <div class="col-lg-9 fw-bold text-dark">{{ \App\Services\OpportunityService::formatKz($contract->valor) }}</div>
                </div>
                <div class="row mb-4">
                    <div class="col-lg-3 fw-medium text-muted">Comercial</div>
                    <div class="col-lg-9">{{ $contract->commercial->nome ?? '—' }}</div>
                </div>
                <div class="row mb-0">
                    <div class="col-lg-3 fw-medium text-muted">Documento</div>
                    <div class="col-lg-9">{{ $contract->documento ?? '—' }}</div>
                </div>
            </div>
            <hr>
            <div class="card card-body general-info">
                <div class="mb-4">
                    <h5 class="fw-bold mb-0">
                        <span class="d-block mb-2">Notas</span>
                        <span class="fs-12 fw-normal text-muted d-block">Observações do contrato</span>
                    </h5>
                </div>
                <p class="mb-0">{{ $contract->notas ?: 'Sem notas.' }}</p>
            </div>
        </div>

        {{-- [ Histórico / Timeline ] --}}
        <div class="tab-pane fade {{ $activeTab === 'history' ? 'show active' : '' }}" id="historyTab" role="tabpanel">
            <div class="card card-body">
                <h5 class="fw-bold mb-3">Histórico do Contrato</h5>
                <p class="text-muted">Implementar timeline de alterações de estado, renovações, etc.</p>
            </div>
        </div>

        {{-- [ Documentos ] --}}
        <div class="tab-pane fade {{ $activeTab === 'documents' ? 'show active' : '' }}" id="documentsTab" role="tabpanel">
            <div class="card card-body">
                <h5 class="fw-bold mb-3">Documentos</h5>
                <p class="text-muted">Implementar upload e gestão de documentos (PDF do contrato, anexos, etc.)</p>
            </div>
        </div>
    </div>
</div>