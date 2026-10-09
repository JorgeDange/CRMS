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

        {{-- Encomendas em Curso --}}
        <div class="col-xxl-3 col-md-6">
            <div class="card stretch stretch-full">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between mb-4">
                        <div class="d-flex gap-4 align-items-center">
                            <div class="avatar-text avatar-lg bg-gray-200">
                                <i class="feather-briefcase"></i>
                            </div>
                            <div>
                                <div class="fs-4 fw-bold text-dark">
                                    <span class="counter">{{ $kpis['encomendasCurso']['entregues'] }}</span>/<span class="counter">{{ $kpis['encomendasCurso']['total'] }}</span>
                                </div>
                                <h3 class="fs-13 fw-semibold text-truncate-1-line">Encomendas em Curso</h3>
                            </div>
                        </div>
                        <a href="{{ route('contracts') }}">
                            <i class="feather-more-vertical"></i>
                        </a>
                    </div>
                    <div class="pt-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <a href="{{ route('contracts') }}" class="fs-12 fw-medium text-muted text-truncate-1-line">Encomendas em curso</a>
                            <div class="w-100 text-end">
                                <span class="fs-12 text-dark">{{ $kpis['encomendasCurso']['entregues'] }} entregues</span>
                                <span class="fs-11 text-muted">({{ $kpis['encomendasCurso']['percent'] }}%)</span>
                            </div>
                        </div>
                        <div class="progress mt-2 ht-3">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $kpis['encomendasCurso']['percent'] }}%"></div>
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

    {{-- Facturação Mensal — gráfico principal (ApexCharts area) --}}
    <div class="row">
        <div class="col-xxl-12">
            <div class="card stretch stretch-full">
                <div class="card-header">
                    <h5 class="card-title">Facturação Mensal</h5>
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
                    <div id="paymentRecordsChart" style="min-height: 300px;" data-adg-chart="{{ json_encode($faturacaoMensal) }}"></div>
                </div>
                <div class="card-footer">
                    <div class="row g-4">
                        @foreach ($statsCards as $card)
                        <div class="col-lg-3">
                            <div class="p-3 border border-dashed rounded">
                                <div class="fs-12 text-muted mb-1">{{ $card['titulo'] }}</div>
                                <h6 class="fw-bold text-dark">{{ $card['valor'] }}</h6>
                                <div class="progress mt-2 ht-3">
                                    <div class="progress-bar {{ $card['cor'] }}" role="progressbar" style="width: {{ $card['progress'] }}%"></div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- [Mini] — 3 cards juntos numa única row (protótipo index.html) --}}
    <div class="row">
        <div class="col-lg-4">
            <div class="card mb-4 stretch stretch-full">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="d-flex gap-3 align-items-center">
                        <div class="avatar-text">
                            <i class="feather-star"></i>
                        </div>
                        <div>
                            <div class="fw-semibold text-dark">Actividades Concluídas</div>
                            <div class="fs-12 text-muted">{{ $minis['taskCompleted'] }} concluídas</div>
                        </div>
                    </div>
                    <div class="fs-4 fw-bold text-dark">{{ $minis['taskCompleted'] }}</div>
                </div>
                <div class="card-body d-flex align-items-center justify-content-between gap-4">
                    <div id="taskCompletedChart" style="min-height: 120px; flex: 1 1 auto; min-width: 0;"></div>
                    <div class="fs-12 text-muted text-nowrap">
                        <span class="fw-semibold text-primary">28% mais</span><br />
                        <span>desde a semana passada</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-4 stretch stretch-full">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="d-flex gap-3 align-items-center">
                        <div class="avatar-text">
                            <i class="feather-file-text"></i>
                        </div>
                        <div>
                            <div class="fw-semibold text-dark">Novas Actividades</div>
                            <div class="fs-12 text-muted">{{ $minis['newTasks'] }} actividades</div>
                        </div>
                    </div>
                    <div class="fs-4 fw-bold text-dark">{{ $minis['newTasks'] }}</div>
                </div>
                <div class="card-body d-flex align-items-center justify-content-between gap-4">
                    <div id="newTasksChart" style="min-height: 120px; flex: 1 1 auto; min-width: 0;"></div>
                    <div class="fs-12 text-muted text-nowrap">
                        <span class="fw-semibold text-success">34% mais</span><br />
                        <span>desde a semana passada</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-4 stretch stretch-full">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="d-flex gap-3 align-items-center">
                        <div class="avatar-text">
                            <i class="feather-airplay"></i>
                        </div>
                        <div>
                            <div class="fw-semibold text-dark">Encomendas Entregues</div>
                            <div class="fs-12 text-muted">{{ $minis['projectDone'] }} encomendas</div>
                        </div>
                    </div>
                    <div class="fs-4 fw-bold text-dark">{{ $minis['projectDone'] }}</div>
                </div>
                <div class="card-body d-flex align-items-center justify-content-between gap-4">
                    <div id="projectDoneChart" style="min-height: 120px; flex: 1 1 auto; min-width: 0;"></div>
                    <div class="fs-12 text-muted text-nowrap">
                        <span class="fw-semibold text-danger">42% mais</span><br />
                        <span>desde a semana passada</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    window.adgSmallCharts = {
        taskCompleted: @json($taskCompleted),
        newTasks: @json($newTasks),
        projectDone: @json($projectDone),
    };
</script>
@endpush