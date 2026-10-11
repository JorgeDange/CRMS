{{-- BLOCO 6: aba Proposta — 9 campos directos em opportunities --}}
<div>
    <div class="card mb-3">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="card-title mb-0">Proposta</h5>
            @if ($opportunity->proposta_preco !== null || $opportunity->proposta_produto_id !== null)
                <span class="badge bg-soft-success text-success"><i class="feather-check-circle me-1"></i>Preenchida</span>
            @endif
        </div>
        <div class="card-body">
            @if ($saved)
                <div class="alert alert-success d-flex align-items-center justify-content-between" role="alert">
                    <span><i class="feather-check-circle me-2"></i>Proposta guardada com sucesso.</span>
                    <button type="button" class="btn-close" aria-label="Fechar" wire:click="$set('saved', false)"></button>
                </div>
            @endif

            <form wire:submit="save">
                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <h6 class="alert-heading fw-bold mb-1">Não foi possível guardar:</h6>
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $erro)
                                <li>{{ $erro }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Produto</label>
                        <select class="form-control" wire:model="proposta_produto_id">
                            <option value="">Seleccionar produto…</option>
                            @foreach ($produtos as $produto)
                                <option value="{{ $produto->id }}">{{ $produto->nome }}</option>
                            @endforeach
                        </select>
                        @error('proposta_produto_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Preço (Kz)</label>
                        <input type="number" step="0.01" min="0" class="form-control" wire:model="proposta_preco"
                               placeholder="Ex.: 850.00">
                        @error('proposta_preco')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Volume</label>
                        <input type="number" step="0.01" min="0" class="form-control" wire:model="proposta_volume"
                               placeholder="Ex.: 5000">
                        @error('proposta_volume')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Unidade <span class="text-danger">*</span></label>
                        <select class="form-control" wire:model="proposta_volume_unidade" required>
                            <option value="L">Litros (L)</option>
                            <option value="m³">Metros cúbicos (m³)</option>
                        </select>
                        @error('proposta_volume_unidade')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Produto (existente)</label>
                        <input type="text" class="form-control" value="{{ $opportunity->product?->nome ?? '—' }}" disabled>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Período — Início</label>
                        <input type="date" class="form-control" wire:model="proposta_periodo_inicio">
                        @error('proposta_periodo_inicio')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Período — Fim</label>
                        <input type="date" class="form-control" wire:model="proposta_periodo_fim">
                        @error('proposta_periodo_fim')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Crédito (Kz)</label>
                        <input type="number" step="0.01" min="0" class="form-control" wire:model="proposta_credito"
                               placeholder="Vendas a prazo">
                        @error('proposta_credito')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Facturação (Kz)</label>
                        <input type="number" step="0.01" min="0" class="form-control" wire:model="proposta_facturacao"
                               placeholder="Valor bruto/líquido">
                        @error('proposta_facturacao')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Non-oil (Kz)</label>
                        <input type="number" step="0.01" min="0" class="form-control" wire:model="proposta_non_oil"
                               placeholder="Loja de conveniência">
                        @error('proposta_non_oil')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="save">
                        <span wire:loading.remove wire:target="save">
                            <i class="feather-save me-2"></i>Guardar proposta
                        </span>
                        <span wire:loading wire:target="save">
                            <span class="spinner-border spinner-border-sm me-2"></span>A guardar…
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Resumo da proposta guardada --}}
    @if ($opportunity->proposta_preco !== null || $opportunity->proposta_produto_id !== null)
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Resumo da proposta</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-lg-3 fw-medium text-muted">Produto</div>
                    <div class="col-lg-9">{{ $opportunity->propostaProduto?->nome ?? '—' }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-lg-3 fw-medium text-muted">Preço</div>
                    <div class="col-lg-9 fw-bold">{{ $opportunity->proposta_preco !== null ? 'Kz '.number_format((float) $opportunity->proposta_preco, 0, ',', '.') : '—' }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-lg-3 fw-medium text-muted">Volume</div>
                    <div class="col-lg-9">{{ $opportunity->proposta_volume !== null ? number_format((float) $opportunity->proposta_volume, 2, ',', '.').' '.$opportunity->proposta_volume_unidade : '—' }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-lg-3 fw-medium text-muted">Período</div>
                    <div class="col-lg-9">
                        {{ $opportunity->proposta_periodo_inicio?->format('d/m/Y') ?? '—' }}
                        até
                        {{ $opportunity->proposta_periodo_fim?->format('d/m/Y') ?? '—' }}
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-lg-3 fw-medium text-muted">Crédito</div>
                    <div class="col-lg-9">{{ $opportunity->proposta_credito !== null ? 'Kz '.number_format((float) $opportunity->proposta_credito, 0, ',', '.') : '—' }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-lg-3 fw-medium text-muted">Facturação</div>
                    <div class="col-lg-9">{{ $opportunity->proposta_facturacao !== null ? 'Kz '.number_format((float) $opportunity->proposta_facturacao, 0, ',', '.') : '—' }}</div>
                </div>
                <div class="row mb-0">
                    <div class="col-lg-3 fw-medium text-muted">Non-oil</div>
                    <div class="col-lg-9">{{ $opportunity->proposta_non_oil !== null ? 'Kz '.number_format((float) $opportunity->proposta_non_oil, 0, ',', '.') : '—' }}</div>
                </div>
            </div>
        </div>
    @endif
</div>
