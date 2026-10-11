{{-- Formulário de Conciliação — baseado em payment.html (Sprint 6) --}}
{{-- Raiz unica obrigatoria (Livewire); content-area ocupa toda a largura (sem content-sidebar) --}}
<div class="d-flex flex-grow-1">
    <div class="content-area">
        <div class="content-area-header sticky-top">
            <div class="page-header-left d-flex align-items-center gap-2">
                <div class="dropdown">
                    <a href="javascript:void(0)" class="btn btn-light-brand dropdown-toggle" data-bs-toggle="dropdown" data-bs-offset="0,16">
                        <i class="feather-dollar-sign me-2"></i>
                        <span>{{ $editing ? 'Editar Conciliação' : 'Nova Conciliação' }}</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item" href="{{ route('conciliactions') }}" wire:navigate>
                            <i class="feather-list me-2"></i>
                            <span>Ver todas</span>
                        </a>
                        <a class="dropdown-item" href="{{ route('conciliactions.create') }}" wire:navigate>
                            <i class="feather-plus me-2"></i>
                            <span>Nova Conciliação</span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="page-header-right ms-auto">
                <div class="hstack gap-2">
                    @if ($editing)
                        <button type="button"
                                class="btn btn-success btn-sm"
                                wire:click="resolver"
                                @if ($resolvido) disabled @endif>
                            <i class="feather-check me-1"></i>Marcar Resolvida
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <div class="content-area-body pb-0">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card stretch stretch-full">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ $editing ? 'Editar Conciliação' : 'Nova Conciliação' }}</h5>
                        </div>
                        <div class="card-body">
                            <form wire:submit="save">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Data <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" wire:model="data" required>
                                        @error('data')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Cliente / Oportunidade <span class="text-danger">*</span></label>
                                        <select class="form-control" wire:model="opportunity_id" required>
                                            <option value="">Seleccionar oportunidade…</option>
                                            @foreach ($oportunidades as $oportunidade)
                                                <option value="{{ $oportunidade->id }}">{{ $oportunidade->nome }}</option>
                                            @endforeach
                                        </select>
                                        @error('opportunity_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                <div class="row">
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
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Valor (Kz) <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" wire:model="valor" min="0" step="0.01" required placeholder="0,00">
                                        @error('valor')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Classificação <span class="text-danger">*</span></label>
                                    <select class="form-control" wire:model="classificacao" required>
                                        @foreach ($classificacoes as $c)
                                            <option value="{{ $c->value }}">{{ $c->label() }}</option>
                                        @endforeach
                                    </select>
                                    @error('classificacao')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Observações</label>
                                    <textarea class="form-control" rows="3" wire:model="observacoes"
                                              placeholder="Detalhes da conciliação…"></textarea>
                                    @error('observacoes')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-3 form-check form-switch">
                                    <input class="form-check-input" type="checkbox" wire:model="resolvido" id="conciliactionResolvido">
                                    <label class="form-check-label" for="conciliactionResolvido">Marcar como resolvida</label>
                                    @error('resolvido')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="feather-save me-2"></i>{{ $editing ? 'Guardar alterações' : 'Criar Conciliação' }}
                                    </button>
                                    <a href="{{ route('conciliactions') }}" class="btn btn-light-brand" wire:navigate>Cancelar</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card stretch stretch-full mb-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Classificações</h5>
                        </div>
                        <div class="card-body fs-13">
                            @foreach ($classificacoes as $c)
                                <div class="d-flex align-items-center {{ $loop->last ? 'mb-0' : 'mb-2' }}">
                                    <span class="badge {{ $c->badgeClass() }} me-2">
                                        <i class="{{ $c->icon() }} me-1"></i>
                                        {{ $c->label() }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>

        <x-ui.footer />
    </div>
</div>