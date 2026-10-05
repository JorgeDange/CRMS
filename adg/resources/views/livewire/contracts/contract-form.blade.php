{{-- Formulário de Contrato — copiado de crm-prototipo/projects-create.html (Sprint 7) --}}
<div class="d-flex flex-grow-1" x-data="{ sidebarOpen: false }">
    <div class="content-sidebar content-sidebar-md" :class="sidebarOpen && 'app-sidebar-open'">
        <div class="content-sidebar-header bg-white sticky-top hstack justify-content-between">
            <h4 class="fw-bolder mb-0">Contratos</h4>
            <a href="javascript:void(0);" class="app-sidebar-close-trigger d-flex" @click="sidebarOpen = false">
                <i class="feather-x"></i>
            </a>
        </div>
        <div class="content-sidebar-header">
            <a href="{{ route('contracts.create') }}" class="btn btn-primary w-100" wire:navigate>
                <i class="feather-plus me-2"></i>
                <span>Novo Contrato</span>
            </a>
        </div>
        <div class="content-sidebar-body">
            <ul class="nav flex-column nxl-content-sidebar-item">
                <li class="px-4 my-2 fs-10 fw-bold text-uppercase text-muted text-spacing-1">Estado</li>
                @foreach ($estados as $estado)
                    <li class="nav-item">
                        <a class="nav-link {{ $statusFilter === $estado->value ? 'active' : '' }}"
                           href="javascript:void(0);" wire:click="filterStatus('{{ $estado->value }}')">
                            <i class="{{ $estado->icon() }}"></i>
                            <span>{{ $estado->label() }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="content-area">
        <div class="content-area-header sticky-top">
            <div class="page-header-left d-flex align-items-center gap-2">
                <a href="javascript:void(0);" class="app-sidebar-open-trigger me-2" @click="sidebarOpen = true">
                    <i class="feather-align-left fs-20"></i>
                </a>
                <div class="dropdown">
                    <a href="javascript:void(0)" class="btn btn-light-brand dropdown-toggle" data-bs-toggle="dropdown" data-bs-offset="0,16">
                        <i class="feather-file-text me-2"></i>
                        <span>{{ $editing ? 'Editar Contrato' : 'Novo Contrato' }}</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item" href="{{ route('contracts') }}" wire:navigate>
                            <i class="feather-list me-2"></i>
                            <span>Ver todos</span>
                        </a>
                        <a class="dropdown-item" href="{{ route('contracts.create') }}" wire:navigate>
                            <i class="feather-plus me-2"></i>
                            <span>Novo Contrato</span>
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
                                       wire:click="setStatus('activo')"
                                       @if($estado === 'activo') style="display:none" @endif>
                                        <i class="feather-check-circle me-2"></i>
                                        <span>Activo</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item"
                                       href="javascript:void(0);"
                                       wire:click="setStatus('expirado')"
                                       @if($estado === 'expirado') style="display:none" @endif>
                                        <i class="feather-alert-triangle me-2"></i>
                                        <span>Expirado</span>
                                    </a>
                                </li>
                                <li>
                                                                <a class="dropdown-item"
                                                                   href="javascript:void(0);"
                                                                   wire:click="setStatus('renovado')"
                                                                   @if($estado === 'renovado') style="display:none" @endif>
                                                                    <i class="feather-refresh-cw me-2"></i>
                                                                    <span>Renovado</span>
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item"
                                                                   href="javascript:void(0);"
                                                                   wire:click="cancelar()"
                                                                   @if($estado === 'cancelado') style="display:none" @endif>
                                                                    <i class="feather-x-circle me-2"></i>
                                                                    <span>Cancelar</span>
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
                                                        <h5 class="card-title mb-0">{{ $editing ? 'Editar Contrato' : 'Novo Contrato' }}</h5>
                                                    </div>
                                                    <div class="card-body">
                                                        <form wire:submit="save">
                                                            <div class="row">
                                                                <div class="col-md-6 mb-3">
                                                                    <label class="form-label">Oportunidade <span class="text-danger">*</span></label>
                                                                    <select class="form-control" wire:model="opportunity_id" required>
                                                                        <option value="">Seleccionar oportunidade…</option>
                                                                        @foreach ($oportunidades as $oportunidade)
                                                                            <option value="{{ $oportunidade->id }}">{{ $oportunidade->nome }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                    @error('opportunity_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                                                </div>
                                                                <div class="col-md-6 mb-3">
                                                                    <label class="form-label">Produto <span class="text-danger">*</span></label>
                                                                    <select class="form-control" wire:model="product_id" required>
                                                                        <option value="">Seleccionar produto…</option>
                                                                        @foreach ($produtos as $produto)
                                                                            <option value="{{ $produto->id }}">{{ $produto->nome }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                    @error('product_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                                                </div>
                                                            </div>

                                                            <div class="row">
                                                                <div class="col-md-6 mb-3">
                                                                    <label class="form-label">Número do Contrato <span class="text-danger">*</span></label>
                                                                    <input type="text" class="form-control" wire:model="numero_contrato" required placeholder="CT-2026-001">
                                                                    @error('numero_contrato')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
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

                                                            <div class="row">
                                                                <div class="col-md-6 mb-3">
                                                                    <label class="form-label">Tipo</label>
                                                                    <input type="text" class="form-control" wire:model="tipo" placeholder="Ex.: Fornecimento, Serviço, Manutenção">
                                                                    @error('tipo')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                                                </div>
                                                                <div class="col-md-6 mb-3">
                                                                    <label class="form-label">Estado</label>
                                                                    <select class="form-control" wire:model="estado">
                                                                        @foreach ($estados as $estado)
                                                                            <option value="{{ $estado->value }}">{{ $estado->label() }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                    @error('estado')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                                                </div>
                                                            </div>

                                                            <div class="row">
                                                                <div class="col-md-4 mb-3">
                                                                    <label class="form-label">Data Início <span class="text-danger">*</span></label>
                                                                    <input type="date" class="form-control" wire:model="data_inicio" required>
                                                                    @error('data_inicio')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                                                </div>
                                                                <div class="col-md-4 mb-3">
                                                                    <label class="form-label">Data Fim <span class="text-danger">*</span></label>
                                                                    <input type="date" class="form-control" wire:model="data_fim" required>
                                                                    @error('data_fim')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                                                </div>
                                                                <div class="col-md-4 mb-3">
                                                                    <label class="form-label">Valor (Kz) <span class="text-danger">*</span></label>
                                                                    <input type="number" class="form-control" wire:model="valor" min="0" step="0.01" required placeholder="0,00">
                                                                    @error('valor')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                                                </div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label">Documento</label>
                                                                <input type="text" class="form-control" wire:model="documento" placeholder="Ex.: DOC-CT-2026-001">
                                                                @error('documento')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label">Notas</label>
                                                                <textarea class="form-control" rows="3" wire:model="notas" placeholder="Observações…"></textarea>
                                                                @error('notas')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                                            </div>

                                                            <div class="d-flex gap-2">
                                                                <button type="submit" class="btn btn-primary">
                                                                    <i class="feather-save me-2"></i>{{ $editing ? 'Guardar alterações' : 'Criar Contrato' }}
                                                                </button>
                                                                <a href="{{ route('contracts') }}" class="btn btn-light-brand" wire:navigate>Cancelar</a>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="card stretch stretch-full mb-3">
                                                    <div class="card-header">
                                                        <h5 class="card-title mb-0">Estados</h5>
                                                    </div>
                                                    <div class="card-body fs-13">
                                                        @foreach ($estados as $estado)
                                                            <div class="d-flex align-items-center {{ $loop->last ? 'mb-0' : 'mb-2' }}">
                                                                <span class="badge {{ $estado->badgeClass() }} me-2">
                                                                    <i class="{{ $estado->icon() }} me-1"></i>
                                                                    {{ $estado->label() }}
                                                                </span>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                <div class="card stretch stretch-full">
                                                    <div class="card-header">
                                                        <h5 class="card-title mb-0">Dicas</h5>
                                                    </div>
                                                    <div class="card-body fs-13">
                                                        <p class="mb-2"><i class="feather-info text-primary me-1"></i> Oportunidade, Produto, Número do Contrato, Data Início, Data Fim e Valor são obrigatórios.</p>
                                                        <p class="mb-2"><i class="feather-info text-primary me-1"></i> Data Fim deve ser posterior ou igual à Data Início.</p>
                                                        <p class="mb-0"><i class="feather-info text-primary me-1"></i> Estados: <strong>Activo</strong> = em vigor; <strong>Expirado</strong> = data fim passada; <strong>Renovado</strong> = prolongado; <strong>Cancelado</strong> = anulado.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <x-ui.footer />
                                </div>
                            </div>