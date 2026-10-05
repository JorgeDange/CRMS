@use('App\Livewire\Opportunities\Pipeline')
@use('App\Enums\OpportunityStage')
@use('App\Enums\TipoRelacao')

{{-- Formulario copiado de leads-create.html (PROMPT_MASTER Sprint 3) + novos campos --}}
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
                        <label class="form-label">Pessoa de Contacto</label>
                        <input type="text" class="form-control" wire:model.defer="pessoa_contacto"
                               placeholder="Nome da pessoa de contacto" autocomplete="off">
                        @error('pessoa_contacto')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
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
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tipo de Relação</label>
                            <select class="form-control" wire:model.defer="tipo_relacao">
                                <option value="">Seleccionar…</option>
                                @foreach (TipoRelacao::cases() as $tipo)
                                    <option value="{{ $tipo->value }}">{{ $tipo->label() }}</option>
                                @endforeach
                            </select>
                            @error('tipo_relacao')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Comercial</label>
                            <select class="form-control" wire:model.defer="comercial_id">
                                <option value="">Seleccionar…</option>
                                @foreach ($comerciais as $comercial)
                                    <option value="{{ $comercial->id }}">{{ $comercial->nome }}</option>
                                @endforeach
                            </select>
                            @error('comercial_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Produto</label>
                            <select class="form-control" wire:model.defer="produto_id">
                                <option value="">Seleccionar…</option>
                                @foreach ($produtos as $produto)
                                    <option value="{{ $produto->id }}">{{ $produto->nome }}</option>
                                @endforeach
                            </select>
                            @error('produto_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Origem</label>
                            <input type="text" class="form-control" wire:model.defer="origem"
                                   placeholder="Ex.: Website, Indicação, Feira">
                            @error('origem')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Sector</label>
                            <input type="text" class="form-control" wire:model.defer="sector"
                                   placeholder="Ex.: Construção, Indústria, Serviços">
                            @error('sector')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Localização</label>
                            <input type="text" class="form-control" wire:model.defer="localizacao"
                                   placeholder="Cidade / Província">
                            @error('localizacao')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Telefone</label>
                            <input type="tel" class="form-control" wire:model.defer="telefone"
                                   placeholder="+244 9XX XXX XXX">
                            @error('telefone')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" wire:model.defer="email"
                                   placeholder="email@exemplo.com">
                            @error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">NIF</label>
                            <input type="text" class="form-control" wire:model.defer="nif"
                                   placeholder="5000000000">
                            @error('nif')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Código SAP</label>
                            <input type="text" class="form-control" wire:model.defer="codigo_sap"
                                   placeholder="Código SAP (se aplicável)">
                            @error('codigo_sap')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
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
                <h5 class="card-title mb-0">Pipeline (9 fases)</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0 fs-13">
                    @foreach (Pipeline::COLUMNS as $stage => $config)
                        <li class="d-flex justify-content-between mb-2">
                            <span>{{ $config['label'] }}</span>
                            <span class="text-muted">{{ match ($stage) {
                                'lead' => '30%',
                                'qualificacao' => '40%',
                                'visita' => '50%',
                                'proposta_negociacao' => '60%',
                                'aprovacao' => '70%',
                                'bilhete' => '80%',
                                'resultado' => '90%',
                                'cliente_aprovado' => '100%',
                                'cliente_nao_aprovado' => '0%',
                            } }}</span>
                        </li>
                    @endforeach
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
                <p class="mb-2"><i class="feather-info text-primary me-1"></i> Data de fecho alimenta os alertas de prazo.</p>
                <p class="mb-0"><i class="feather-info text-primary me-1"></i> Comercial e Produto são opcionais mas recomendados para relatórios.</p>
            </div>
        </div>
    </div>
</div>