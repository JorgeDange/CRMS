@use('App\Livewire\Opportunities\Pipeline')

{{-- Formulario copiado de leads-create.html (PROMPT_MASTER Sprint 3) --}}
<div class="row">
    <div class="col-lg-8">
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="card-title mb-0">Dados da Oportunidade</h5>
            </div>
            <div class="card-body">
                <form id="oppForm" wire:submit="save">
                    <div class="mb-3">
                        <label class="form-label">Cliente <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" wire:model.defer="cliente" list="oppClientes"
                               required placeholder="Seleccionar cliente…" autocomplete="off">
                        <datalist id="oppClientes">
                            @foreach ($clientes as $opCliente)
                                <option value="{{ $opCliente }}"></option>
                            @endforeach
                        </datalist>
                        @error('cliente')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Título <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" wire:model.defer="titulo" required
                               placeholder="Ex.: Renovação contrato 2026">
                        @error('titulo')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Valor (Kz) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" wire:model.defer="valor" min="0" step="0.01"
                                   required placeholder="0,00">
                            @error('valor')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Estado</label>
                            <select class="form-control" wire:model.defer="fase_atual">
                                @foreach (Pipeline::COLUMNS as $stage => $config)
                                    <option value="{{ $stage }}">{{ $config['label'] }}</option>
                                @endforeach
                            </select>
                            @error('fase_atual')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Probabilidade</label>
                            <select class="form-control" wire:model.defer="probabilidade">
                                @for ($p = 10; $p <= 100; $p += 10)
                                    <option value="{{ $p }}">{{ $p }}%</option>
                                @endfor
                            </select>
                            @error('probabilidade')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Data de fecho</label>
                            <input type="date" class="form-control" wire:model.defer="data_fecho">
                            @error('data_fecho')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descrição</label>
                        <textarea class="form-control" rows="3" wire:model.defer="descricao"
                                  placeholder="Contexto da oportunidade (opcional)"></textarea>
                        @error('descricao')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="feather-save me-2"></i>Criar Oportunidade
                        </button>
                        <a href="{{ route('pipeline') }}" class="btn btn-light-brand" wire:navigate>Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card stretch stretch-full mb-3">
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
        <div class="card stretch stretch-full">
            <div class="card-header">
                <h5 class="card-title mb-0">Dicas</h5>
            </div>
            <div class="card-body fs-13">
                <p class="mb-2"><i class="feather-info text-primary me-1"></i> Cliente, Título e Valor são obrigatórios.</p>
                <p class="mb-2"><i class="feather-info text-primary me-1"></i> Probabilidade alimenta o valor ponderado do pipeline.</p>
                <p class="mb-0"><i class="feather-info text-primary me-1"></i> Data de fecho alimenta os alertas de prazo.</p>
            </div>
        </div>
    </div>
</div>
