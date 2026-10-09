@use('App\Services\OpportunityService')

{{-- Importacao Excel (PROMPT_MASTER Sprint 4) montada com os componentes do prototipo --}}
<div class="row">
    <div class="col-lg-8">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="card-title mb-0">Importar de Excel</h5>
            </div>
            <div class="card-body">
                <form wire:submit="preview" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Ficheiro (.xlsx) <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" wire:model="file" accept=".xlsx,.xls">
                        @error('file')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        <div class="form-text">Uma linha por oportunidade, tal como no template.</div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary" @disabled(! $file)>
                            <i class="feather-eye me-2"></i>Pré-visualizar
                        </button>
                        <a href="{{ route('opportunities.import.template') }}" class="btn btn-light-brand">
                            <i class="feather-download me-2"></i>Descarregar template
                        </a>
                    </div>
                </form>
            </div>
        </div>

        @if ($analysed)
            @if ($report)
                <div class="card stretch stretch-full mt-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Relatório de importação</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <x-ui.badge color="success">Criadas: {{ $report['criadas'] }}</x-ui.badge>
                            <x-ui.badge color="warning">Duplicadas: {{ $report['duplicadas'] }}</x-ui.badge>
                            <x-ui.badge color="danger">Erros: {{ $report['erros'] }}</x-ui.badge>
                            <x-ui.badge color="secondary">Total: {{ $report['total'] }}</x-ui.badge>
                        </div>
                        <button type="button" class="btn btn-light-brand" wire:click="startOver">
                            <i class="feather-refresh-cw me-2"></i>Nova importação
                        </button>
                    </div>
                </div>
            @endif

            <div class="card stretch stretch-full mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Pré-visualização</h5>
                </div>
                <div class="card-body">
                    @if ($rows === [])
                        <div class="alert alert-warning mb-0" role="alert">
                            <i class="feather-alert-triangle me-1"></i>Não há linhas para importar neste ficheiro.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Linha</th>
                                        <th>Cliente</th>
                                        <th>Título</th>
                                        <th>Valor (Kz)</th>
                                        <th>Estado</th>
                                        <th>Prob.</th>
                                        <th>Fecho</th>
                                        <th>Situação</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($rows as $row)
                                        <tr>
                                            <td>{{ $row['linha'] }}</td>
                                            <td>{{ $row['cliente'] ?? '—' }}</td>
                                            <td>{{ $row['titulo'] ?? '—' }}</td>
                                            <td>
                                                @if ($row['valor'] !== null)
                                                    {{ OpportunityService::formatKz($row['valor']) }}
                                                @else
                                                    —
                                                @endif
                                            </td>
                                            <td>{{ $row['estado_label'] }}</td>
                                            <td>
                                                @if ($row['probabilidade'] !== null)
                                                    {{ $row['probabilidade'] }}%
                                                @else
                                                    <span class="text-muted" title="Por omissão">30%</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $row['data_fecho'] !== null
                                                    ? \Illuminate\Support\Carbon::parse($row['data_fecho'])->format('d/m/Y')
                                                    : '—' }}
                                            </td>
                                            <td>
                                                @if ($row['erros'] !== [])
                                                    <x-ui.badge color="danger">Erro</x-ui.badge>
                                                    <div class="text-danger fs-12">{{ implode(' ', $row['erros']) }}</div>
                                                @elseif ($row['duplicada'])
                                                    <x-ui.badge color="warning">Duplicada</x-ui.badge>
                                                    <div class="text-muted fs-12">{{ $row['motivo_duplicado'] }}</div>
                                                @elseif ($row['estado_importacao'] === 'criada')
                                                    <x-ui.badge color="success">Criada</x-ui.badge>
                                                @else
                                                    <x-ui.badge color="info">Pronta</x-ui.badge>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if (! $report)
                            @php
                                $prontas = collect($rows)
                                    ->filter(fn (array $row): bool => $row['erros'] === [] && ! $row['duplicada'])
                                    ->count();
                            @endphp
                            <div class="d-flex gap-2 mt-3">
                                <button type="button" class="btn btn-primary" wire:click="import"
                                        @disabled($prontas === 0)>
                                    <i class="feather-upload me-2"></i>Importar {{ $prontas }} oportunidade(s)
                                </button>
                                <button type="button" class="btn btn-light-brand" wire:click="startOver">
                                    Descartar
                                </button>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        @endif
    </div>

    <div class="col-lg-4">
        <div class="card stretch stretch-full mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Como importar</h5>
            </div>
            <div class="card-body fs-13">
                <ol class="mb-0 ps-3">
                    <li class="mb-2">
                        <a href="{{ route('opportunities.import.template') }}">Descarregue o template</a>
                        com as colunas certas.
                    </li>
                    <li class="mb-2">Preencha uma linha por oportunidade — Cliente, Título e Valor são obrigatórios.</li>
                    <li class="mb-2">Estado: deixe vazio para <span class="fw-semibold">Leads</span> ou use os nomes do pipeline.</li>
                    <li class="mb-2">Carregue em <span class="fw-semibold">Pré-visualizar</span> e confirme as linhas.</li>
                    <li class="mb-0">Linhas com o mesmo Título e Cliente são ignoradas (duplicados).</li>
                </ol>
            </div>
        </div>

        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="card-title mb-0">Pipeline</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0 fs-13">
                    <li class="d-flex justify-content-between mb-2"><span>Leads</span><span class="text-muted">30%</span></li>
                    <li class="d-flex justify-content-between mb-2"><span>Visitas</span><span class="text-muted">50%</span></li>
                    <li class="d-flex justify-content-between mb-2"><span>Aprovação</span><span class="text-muted">70%</span></li>
                    <li class="d-flex justify-content-between mb-2"><span class="text-success fw-semibold">Aprovado</span><span class="text-success">100%</span></li>
                    <li class="d-flex justify-content-between mb-0"><span class="text-danger fw-semibold">Não Aprovado</span><span class="text-danger">0%</span></li>
                </ul>
            </div>
        </div>
    </div>
</div>
