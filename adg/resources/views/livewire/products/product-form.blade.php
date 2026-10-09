{{-- Formulário de Produto — copiado de crm-prototipo/customers-create.html (Sprint 8) --}}
{{-- Raiz unica obrigatoria (Livewire); content-area ocupa toda a largura (sem content-sidebar) --}}
<div class="d-flex flex-grow-1">
    <div class="content-area">
        <div class="content-area-header sticky-top">
            <div class="page-header-left d-flex align-items-center gap-2">
                <div class="dropdown">
                    <a href="javascript:void(0)" class="btn btn-light-brand dropdown-toggle" data-bs-toggle="dropdown" data-bs-offset="0,16">
                        <i class="feather-file-text me-2"></i>
                        <span>{{ $editing ? 'Editar Produto' : 'Novo Produto' }}</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item" href="{{ route('products') }}" wire:navigate>
                            <i class="feather-list me-2"></i>
                            <span>Ver todos</span>
                        </a>
                        <a class="dropdown-item" href="{{ route('products.create') }}" wire:navigate>
                            <i class="feather-plus me-2"></i>
                            <span>Novo Produto</span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="page-header-right ms-auto">
                <div class="hstack gap-2">
                    @if ($editing)
                        <div class="dropdown d-none d-sm-flex">
                            <a href="javascript:void(0)" class="btn btn-light-brand btn-sm rounded-pill dropdown-toggle"
                               data-bs-toggle="dropdown" data-bs-offset="0,23">
                                Estado
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item"
                                       href="javascript:void(0);"
                                       wire:click="toggleStatus({{ $productId }})"
                                       @if($activo) style="display:none" @endif>
                                        <i class="feather-check-circle me-2"></i>
                                        <span>Activar</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item"
                                       href="javascript:void(0);"
                                       wire:click="toggleStatus({{ $productId }})"
                                       @if(!$activo) style="display:none" @endif>
                                        <i class="feather-pause-circle me-2"></i>
                                        <span>Desactivar</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="content-area-body pb-0">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card stretch stretch-full">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ $editing ? 'Editar Produto' : 'Novo Produto' }}</h5>
                        </div>
                        <div class="card-body">
                            <form wire:submit="save">
                                <div class="mb-3">
                                    <label class="form-label">Nome <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" wire:model="nome" required
                                           placeholder="Ex.: Painel Solar 450W">
                                    @error('nome')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Código <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" wire:model="codigo" required
                                               placeholder="Ex.: PRD-001">
                                        @error('codigo')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nº Série</label>
                                        <input type="text" class="form-control" wire:model="numero_serie"
                                               placeholder="Ex.: SN-1234-ABCD">
                                        @error('numero_serie')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Valor (Kz) <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" wire:model="valor" min="0" step="0.01"
                                               required placeholder="0,00">
                                        @error('valor')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Comercial</label>
                                        <select class="form-control" wire:model="commercial_id">
                                            <option value="">Seleccionar comercial…</option>
                                            @foreach ($comerciais as $comercial)
                                                <option value="{{ $comercial->id }}">{{ $comercial->nome }}</option>
                                            @endforeach
                                        </select>
                                        @error('commercial_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                <div class="mb-3 form-check form-switch">
                                    <input class="form-check-input" type="checkbox" wire:model="activo" id="productActivo">
                                    <label class="form-check-label" for="productActivo">Activo</label>
                                    @error('activo')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="feather-save me-2"></i>{{ $editing ? 'Guardar alterações' : 'Criar Produto' }}
                                    </button>
                                    <a href="{{ route('products') }}" class="btn btn-light-brand" wire:navigate>Cancelar</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card stretch stretch-full mb-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Dicas</h5>
                        </div>
                        <div class="card-body fs-13">
                            <p class="mb-2"><i class="feather-info text-primary me-1"></i> Código e Nome são obrigatórios e o código deve ser único.</p>
                            <p class="mb-2"><i class="feather-info text-primary me-1"></i> Valor em Kz (ex.: 1200000 para 1.200.000 Kz).</p>
                            <p class="mb-0"><i class="feather-info text-primary me-1"></i> Produtos inactivos não aparecem nas listas de selecção de contratos.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <x-ui.footer />
    </div>
</div>