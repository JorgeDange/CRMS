{{-- Cartoes de estatisticas do collapseOne (leads.html) — raiz unica obrigatoria (Livewire) --}}
<div class="row">
<div class="col-xxl-3 col-md-6">
    <div class="card stretch stretch-full">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar-text avatar-xl rounded bg-soft-primary text-primary">
                        <i class="feather-funnel"></i>
                    </div>
                    <span class="fw-bold d-block">
                        <span class="d-block">Pipeline Aberto</span>
                        <span class="fs-24 fw-bolder d-block" title="{{ $pipelineValorCompleto }}">{{ $pipelineValor }}</span>
                    </span>
                </div>
                <span class="badge bg-soft-primary text-primary">{{ $pipelineCount }} opps</span>
            </div>
        </div>
    </div>
</div>
<div class="col-xxl-3 col-md-6">
    <div class="card stretch stretch-full">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar-text avatar-xl rounded bg-soft-success text-success">
                        <i class="feather-check-circle"></i>
                    </div>
                    <span class="fw-bold d-block">
                        <span class="d-block">Aprovado (mês)</span>
                        <span class="fs-24 fw-bolder d-block" title="{{ $ganhoValorCompleto }}">{{ $ganhoValor }}</span>
                    </span>
                </div>
                <span class="badge bg-soft-success text-success">{{ $ganhoCount }} opps</span>
            </div>
        </div>
    </div>
</div>
<div class="col-xxl-3 col-md-6">
    <div class="card stretch stretch-full">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar-text avatar-xl rounded bg-soft-warning text-warning">
                        <i class="feather-trending-up"></i>
                    </div>
                    <span class="fw-bold d-block">
                        <span class="d-block">Ponderado</span>
                        <span class="fs-24 fw-bolder d-block" title="{{ $ponderadoCompleto }}">{{ $ponderado }}</span>
                    </span>
                </div>
                <span class="badge bg-soft-warning text-warning">&times; prob.</span>
            </div>
        </div>
    </div>
</div>
<div class="col-xxl-3 col-md-6">
    <div class="card stretch stretch-full">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar-text avatar-xl rounded bg-soft-info text-info">
                        <i class="feather-activity"></i>
                    </div>
                    <span class="fw-bold d-block">
                        <span class="d-block">Taxa Conversão</span>
                        <span class="fs-24 fw-bolder d-block">{{ $conversao }}%</span>
                    </span>
                </div>
                <span class="badge bg-soft-info text-info">30 dias</span>
            </div>
        </div>
    </div>
</div>
</div>
