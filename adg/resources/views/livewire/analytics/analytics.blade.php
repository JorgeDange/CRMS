{{-- Painel Executivo — copiado de crm-prototipo/analytics.html (Sprint 11) --}}
<div class="d-flex flex-grow-1">
    <div class="content-area">
        <div class="content-area-header sticky-top">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Painel Executivo</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" wire:navigate>Início</a></li>
                    <li class="breadcrumb-item">Painel Executivo</li>
                </ul>
            </div>
            <div class="page-header-right ms-auto">
                <div class="page-header-right-items">
                    <div class="d-flex d-md-none">
                        <a href="javascript:void(0)" class="page-header-right-close-toggle">
                            <i class="feather-arrow-left me-2"></i>
                            <span>Voltar</span>
                        </a>
                    </div>
                    <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                        <span class="badge bg-soft-secondary text-secondary fs-12 px-3 py-2">
                            <i class="feather-eye me-1"></i>Somente leitura
                        </span>
                    </div>
                </div>
                <div class="d-md-none d-flex align-items-center">
                    <a href="javascript:void(0)" class="page-header-right-open-toggle">
                        <i class="feather-align-right fs-20"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="content-area-body">
            {{-- KPIs --}}
            <div class="row">
                <div class="col-xxl-3 col-md-6">
                    <div class="card stretch stretch-full">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between mb-4">
                                <div class="d-flex gap-4 align-items-center">
                                    <div class="avatar-text avatar-lg bg-soft-success text-success">
                                        <i class="feather-trending-up"></i>
                                    </div>
                                    <div>
                                        <div class="fs-4 fw-bold text-dark">{{ \App\Services\OpportunityService::formatKz($vendasYTD) }}</div>
                                        <h3 class="fs-13 fw-semibold">Vendas YTD</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="fs-12 text-success mb-0">▲ 18% vs ano anterior</div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-3 col-md-6">
                    <div class="card stretch stretch-full">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between mb-4">
                                <div class="d-flex gap-4 align-items-center">
                                    <div class="avatar-text avatar-lg bg-soft-primary text-primary">
                                        <i class="feather-bar-chart-2"></i>
                                    </div>
                                    <div>
                                        <div class="fs-4 fw-bold text-dark">{{ \App\Services\OpportunityService::formatKz($pipelinePonderado) }}</div>
                                        <h3 class="fs-13 fw-semibold">Pipeline Ponderado</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="fs-12 text-muted mb-0">{{ $totalOportunidades }} oportunidades abertas</div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-3 col-md-6">
                    <div class="card stretch stretch-full">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between mb-4">
                                <div class="d-flex gap-4 align-items-center">
                                    <div class="avatar-text avatar-lg bg-soft-warning text-warning">
                                        <i class="feather-shopping-cart"></i>
                                    </div>
                                    <div>
                                        <div class="fs-4 fw-bold text-dark">{{ $encomendasEmCurso }}</div>
                                        <h3 class="fs-13 fw-semibold">Encomendas em Curso</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="fs-12 text-danger mb-0">{{ $encomendasAtraso }} com atraso</div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-3 col-md-6">
                    <div class="card stretch stretch-full">
                        <div class="card-body">
                            <div class="d-flex align-items-start justify-content-between mb-4">
                                <div class="d-flex gap-4 align-items-center">
                                    <div class="avatar-text avatar-lg bg-soft-danger text-danger">
                                        <i class="feather-alert-triangle"></i>
                                    </div>
                                    <div>
                                        <div class="fs-4 fw-bold text-dark">{{ \App\Services\OpportunityService::formatKz($dividaVencida) }}</div>
                                        <h3 class="fs-13 fw-semibold">Dívida Vencida</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="fs-12 text-danger mb-0">{{ $clientesBloqueados }} clientes bloqueados</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Gráficos --}}
            <div class="row">
                <div class="col-xxl-8 col-xl-7">
                    <div class="card stretch stretch-full">
                        <div class="card-header">
                            <div class="d-flex align-items-center justify-content-between w-100">
                                <h5 class="card-title mb-0">Vendas Mensais (Kz)</h5>
                                <span class="fs-12 text-muted">{{ now()->year }}</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="execSalesChart" style="min-height: 280px;"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-4 col-xl-5">
                    <div class="card stretch stretch-full">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Estado das Encomendas</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="execOrdersChart" style="min-height: 280px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Pipeline doughnut + Clientes por segmento --}}
            <div class="row">
                <div class="col-xxl-4 col-md-6">
                    <div class="card stretch stretch-full">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Pipeline por Estado</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="execPipelineChart" style="min-height: 280px;"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-4 col-md-6">
                    <div class="card stretch stretch-full">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Clientes por Segmento</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="execSegmentChart" style="min-height: 280px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tabelas --}}
            <div class="row">
                <div class="col-xxl-6">
                    <div class="card stretch stretch-full">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Performance por Gestor</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Gestor</th>
                                            <th class="text-end">Oportunidades</th>
                                            <th class="text-end">Ganhas</th>
                                            <th class="text-end">Valor (Kz)</th>
                                            <th class="text-end">Taxa</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($gestores as $gestor)
                                            <tr>
                                                <td class="fw-semibold">{{ $gestor['nome'] }}</td>
                                                <td class="text-end">{{ $gestor['total'] }}</td>
                                                <td class="text-end">{{ $gestor['ganhas'] }}</td>
                                                <td class="text-end">{{ \App\Services\OpportunityService::formatKz($gestor['valor']) }}</td>
                                                <td class="text-end"><span class="badge {{ $gestor['taxa'] >= 50 ? 'bg-soft-success text-success' : ($gestor['taxa'] >= 25 ? 'bg-soft-primary text-primary' : 'bg-soft-warning text-warning') }}">{{ $gestor['taxa'] }}%</span></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-6">
                    <div class="card stretch stretch-full">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Top Clientes por Facturação</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Cliente</th>
                                            <th class="text-end">Encomendas</th>
                                            <th class="text-end">Facturação (Kz)</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($topClientes as $cliente)
                                            <tr>
                                                <td>
                                                    @if ($cliente['opportunity_id'])
                                                        <a href="{{ route('opportunities.show', $cliente['opportunity_id']) }}" class="fw-semibold" wire:navigate>{{ $cliente['nome'] }}</a>
                                                    @else
                                                        <span class="fw-semibold">{{ $cliente['nome'] }}</span>
                                                    @endif
                                                </td>
                                                <td class="text-end">{{ $cliente['encomendas'] }}</td>
                                                <td class="text-end">{{ \App\Services\OpportunityService::formatKz($cliente['facturacao']) }}</td>
                                                <td><span class="badge {{ $cliente['estado_badge'] }}">{{ $cliente['estado'] }}</span></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card stretch stretch-full">
                        <div class="card-body">
                            <p class="fs-12 text-muted mb-0">
                                <i class="feather-eye text-primary me-1"></i>
                                <strong>Painel Executivo</strong> — vista somente leitura para a Direcção.
                                Métricas consolidadas de vendas, pipeline, encomendas e crédito.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>