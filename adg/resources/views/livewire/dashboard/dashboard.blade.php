{{-- Dashboard — cópia EXATA de crm-prototipo/index.html (Sprint 11) --}}
<div class="main-content">
    {{-- KPIs — 4 cards idênticos ao prototype --}}
    <div class="row">
        {{-- Clientes Activos --}}
        <div class="col-xxl-3 col-md-6">
            <div class="card stretch stretch-full">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between mb-4">
                        <div class="d-flex gap-4 align-items-center">
                            <div class="avatar-text avatar-lg bg-gray-200">
                                <i class="feather-users"></i>
                            </div>
                            <div>
                                <div class="fs-4 fw-bold text-dark">
                                    <span class="counter">{{ $kpis['clientesActivos']['activos'] }}</span>/<span class="counter">{{ $kpis['clientesActivos']['total'] }}</span>
                                </div>
                                <h3 class="fs-13 fw-semibold text-truncate-1-line">Clientes Activos</h3>
                            </div>
                        </div>
                        <a href="{{ route('products') }}">
                            <i class="feather-more-vertical"></i>
                        </a>
                    </div>
                    <div class="pt-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <a href="{{ route('products') }}" class="fs-12 fw-medium text-muted text-truncate-1-line">Carteira de clientes</a>
                            <div class="w-100 text-end">
                                <span class="fs-12 text-dark">{{ $kpis['clientesActivos']['activos'] }} activos</span>
                                <span class="fs-11 text-muted">({{ $kpis['clientesActivos']['percent'] }}%)</span>
                            </div>
                        </div>
                        <div class="progress mt-2 ht-3">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $kpis['clientesActivos']['percent'] }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Oportunidades Abertas --}}
        <div class="col-xxl-3 col-md-6">
            <div class="card stretch stretch-full">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between mb-4">
                        <div class="d-flex gap-4 align-items-center">
                            <div class="avatar-text avatar-lg bg-gray-200">
                                <i class="feather-bar-chart-2"></i>
                            </div>
                            <div>
                                <div class="fs-4 fw-bold text-dark">
                                    <span class="counter">{{ $kpis['oportunidadesAbertas']['abertas'] }}</span>/<span class="counter">{{ $kpis['oportunidadesAbertas']['total'] }}</span>
                                </div>
                                <h3 class="fs-13 fw-semibold text-truncate-1-line">Oportunidades Abertas</h3>
                            </div>
                        </div>
                        <a href="{{ route('pipeline') }}">
                            <i class="feather-more-vertical"></i>
                        </a>
                    </div>
                    <div class="pt-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <a href="{{ route('pipeline') }}" class="fs-12 fw-medium text-muted text-truncate-1-line">Pipeline</a>
                            <div class="w-100 text-end">
                                <span class="fs-12 text-dark">{{ \App\Services\DashboardService::formatKz($kpis['oportunidadesAbertas']['valor']) }}</span>
                                <span class="fs-11 text-muted">({{ $kpis['oportunidadesAbertas']['percent'] }}%)</span>
                            </div>
                        </div>
                        <div class="progress mt-2 ht-3">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $kpis['oportunidadesAbertas']['percent'] }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Produtos Activos (BLOCO 2: antes "Encomendas em Curso") --}}
        <div class="col-xxl-3 col-md-6">
            <div class="card stretch stretch-full">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between mb-4">
                        <div class="d-flex gap-4 align-items-center">
                            <div class="avatar-text avatar-lg bg-gray-200">
                                <i class="feather-package"></i>
                            </div>
                            <div>
                                <div class="fs-4 fw-bold text-dark">
                                    <span class="counter">{{ $kpis['produtosActivos']['activos'] }}</span>/<span class="counter">{{ $kpis['produtosActivos']['total'] }}</span>
                                </div>
                                <h3 class="fs-13 fw-semibold text-truncate-1-line">Produtos Activos</h3>
                            </div>
                        </div>
                        <a href="{{ route('products') }}">
                            <i class="feather-more-vertical"></i>
                        </a>
                    </div>
                    <div class="pt-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <a href="{{ route('products') }}" class="fs-12 fw-medium text-muted text-truncate-1-line">Catálogo de produtos</a>
                            <div class="w-100 text-end">
                                <span class="fs-12 text-dark">{{ $kpis['produtosActivos']['activos'] }} activos</span>
                                <span class="fs-11 text-muted">({{ $kpis['produtosActivos']['percent'] }}%)</span>
                            </div>
                        </div>
                        <div class="progress mt-2 ht-3">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $kpis['produtosActivos']['percent'] }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pendências Hoje --}}
        <div class="col-xxl-3 col-md-6">
            <div class="card stretch stretch-full">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between mb-4">
                        <div class="d-flex gap-4 align-items-center">
                            <div class="avatar-text avatar-lg bg-gray-200">
                                <i class="feather-alert-circle"></i>
                            </div>
                            <div>
                                <div class="fs-4 fw-bold text-danger">
                                    <span class="counter">{{ $kpis['pendenciasHoje']['vencidas'] }}</span>
                                </div>
                                <h3 class="fs-13 fw-semibold text-truncate-1-line">Pendências Hoje</h3>
                            </div>
                        </div>
                        <a href="{{ route('agenda') }}">
                            <i class="feather-more-vertical"></i>
                        </a>
                    </div>
                    <div class="pt-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <a href="{{ route('agenda') }}" class="fs-12 fw-medium text-muted text-truncate-1-line">Prazo vencido / hoje</a>
                            <div class="w-100 text-end">
                                <span class="fs-12 text-danger">{{ $kpis['pendenciasHoje']['vencidas'] }} vencidas</span>
                                <span class="fs-11 text-muted">({{ $kpis['pendenciasHoje']['hoje'] }} hoje)</span>
                            </div>
                        </div>
                        <div class="progress mt-2 ht-3">
                            <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $kpis['pendenciasHoje']['percent'] }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Pipeline por Mês — gráfico principal (BLOCO 2: antes "Facturação Mensal") --}}
    <div class="row">
        <div class="col-xxl-12">
            <div class="card stretch stretch-full">
                <div class="card-header">
                    <h5 class="card-title">Pipeline por Mês</h5>
                    <div class="card-header-action">
                        <div class="card-header-btn">
                            <div data-bs-toggle="tooltip" title="Remover">
                                <a href="javascript:void(0);" class="avatar-text avatar-xs bg-danger" data-bs-toggle="remove"> </a>
                            </div>
                            <div data-bs-toggle="tooltip" title="Actualizar">
                                <a href="javascript:void(0);" class="avatar-text avatar-xs bg-warning" data-bs-toggle="refresh"> </a>
                            </div>
                            <div data-bs-toggle="tooltip" title="Maximizar/Minimizar">
                                <a href="javascript:void(0);" class="avatar-text avatar-xs bg-success" data-bs-toggle="expand"> </a>
                            </div>
                        </div>
                        <div class="dropdown">
                            <a href="javascript:void(0);" class="avatar-text avatar-sm" data-bs-toggle="dropdown" data-bs-offset="25, 25">
                                <div data-bs-toggle="tooltip" title="Opções">
                                    <i class="feather-more-vertical"></i>
                                </div>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a href="javascript:void(0);" class="dropdown-item"><i class="feather-at-sign"></i>Novo</a>
                                <a href="javascript:void(0);" class="dropdown-item"><i class="feather-calendar"></i>Actividade</a>
                                <a href="javascript:void(0);" class="dropdown-item"><i class="feather-bell"></i>Adiado</a>
                                <a href="javascript:void(0);" class="dropdown-item"><i class="feather-trash-2"></i>Eliminado</a>
                                <div class="dropdown-divider"></div>
                                <a href="javascript:void(0);" class="dropdown-item"><i class="feather-settings"></i>Definições</a>
                                <a href="javascript:void(0);" class="dropdown-item"><i class="feather-life-buoy"></i>Dicas e truques</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body custom-card-action p-0">
                    <div id="paymentRecordsChart" style="min-height: 300px;" data-adg-chart="{{ json_encode($pipelineMensal) }}"></div>
                </div>
                <div class="card-footer">
                    <div class="row g-4">
                        @foreach ($statsCards as $card)
                        <div class="col-lg-3">
                            <a href="{{ $card['link'] }}" class="text-decoration-none">
                                <div class="p-3 border border-dashed rounded h-100">
                                    <div class="fs-12 text-muted mb-1">{{ $card['titulo'] }}</div>
                                    <h6 class="fw-bold text-dark">{{ $card['valor'] }} {{ \Illuminate\Support\Str::plural('oportunidade', $card['valor']) }}</h6>
                                    <div class="progress mt-2 ht-3">
                                        <div class="progress-bar {{ $card['cor'] }}" role="progressbar" style="width: {{ $card['progress'] }}%" title="{{ $card['progress'] }}% do total"></div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- BLOCO 2: 4 mini-cards do pipeline (Pipeline Aberto, Aprovado (mês),
         Ponderado, Taxa Conversão) — valores do Opportunities\Stats --}}
    {{-- Estilo dos mini-cards: o tema força .card .card-header a display:flex
         (theme-extract.css L549), o que mantinha o valor ao lado do titulo e
         transbordava em cards estreitos (col-lg-3 = 4 por linha, ~200px).
         mini-stat-stack empilha (display:block) com especificidade (0,3,0) para
         sobrepor ao tema (0,2,0); o valor passa à propria linha, com largura
         total do card. Escopo local — sem necessidade de rebuild do Vite. --}}
    <style>
        .card .card-header.mini-stat-stack { display: block; }
    </style>
    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="card mb-4 stretch stretch-full">
                <div class="card-header mini-stat-stack">
                    <div class="d-flex gap-3 align-items-center">
                        <div class="avatar-text">
                            <i class="feather-bar-chart-2"></i>
                        </div>
                        <div>
                            <div class="fw-semibold text-dark">Pipeline Aberto</div>
                            <div class="fs-12 text-muted">{{ $pipelineMinis['pipelineAberto']['count'] }} oportunidades</div>
                        </div>
                    </div>
                    <div class="kpi-money fw-bold text-dark mt-2" title="{{ $pipelineMinis['pipelineAberto']['valorCompleto'] }}">{{ $pipelineMinis['pipelineAberto']['valor'] }}</div>
                </div>
                <div class="card-body pt-0">
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="fs-12 text-muted">Valor total em aberto</span>
                        <span class="fs-12 fw-semibold text-primary">Kz</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card mb-4 stretch stretch-full">
                <div class="card-header mini-stat-stack">
                    <div class="d-flex gap-3 align-items-center">
                        <div class="avatar-text">
                            <i class="feather-check-circle"></i>
                        </div>
                        <div>
                            <div class="fw-semibold text-dark">Aprovado (mês)</div>
                            <div class="fs-12 text-muted">{{ $pipelineMinis['aprovadoMes']['count'] }} aprovadas</div>
                        </div>
                    </div>
                    <div class="kpi-money fw-bold text-dark mt-2" title="{{ $pipelineMinis['aprovadoMes']['valorCompleto'] }}">{{ $pipelineMinis['aprovadoMes']['valor'] }}</div>
                </div>
                <div class="card-body pt-0">
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="fs-12 text-muted">Facturado no mês corrente</span>
                        <span class="fs-12 fw-semibold text-success">Kz</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card mb-4 stretch stretch-full">
                <div class="card-header mini-stat-stack">
                    <div class="d-flex gap-3 align-items-center">
                        <div class="avatar-text">
                            <i class="feather-percent"></i>
                        </div>
                        <div>
                            <div class="fw-semibold text-dark">Ponderado</div>
                            <div class="fs-12 text-muted">valor × probabilidade</div>
                        </div>
                    </div>
                    <div class="kpi-money fw-bold text-dark mt-2" title="{{ $pipelineMinis['ponderado']['valorCompleto'] }}">{{ $pipelineMinis['ponderado']['valor'] }}</div>
                </div>
                <div class="card-body pt-0">
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="fs-12 text-muted">Pipeline ajustado ao risco</span>
                        <span class="fs-12 fw-semibold text-warning">Kz</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card mb-4 stretch stretch-full">
                <div class="card-header mini-stat-stack">
                    <div class="d-flex gap-3 align-items-center">
                        <div class="avatar-text">
                            <i class="feather-trending-up"></i>
                        </div>
                        <div>
                            <div class="fw-semibold text-dark">Taxa Conversão</div>
                            <div class="fs-12 text-muted">últimos 30 dias</div>
                        </div>
                    </div>
                    <div class="kpi-money fw-bold text-dark mt-2">{{ $pipelineMinis['conversao']['percent'] }}%</div>
                </div>
                <div class="card-body pt-0">
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="fs-12 text-muted">Aprovadas / finalizadas</span>
                        <span class="fs-12 fw-semibold text-info">%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>