{{-- Formulario de actividade — copiado de crm-prototipo/apps-notes.html (Sprint 5, 8.4) --}}
{{-- Raiz unica obrigatoria (Livewire); envolve content-sidebar + content-area como no prototipo --}}
<div class="d-flex flex-grow-1" x-data="{ sidebarOpen: false }">
    <div class="content-sidebar content-sidebar-md" :class="sidebarOpen && 'app-sidebar-open'">
        <div class="content-sidebar-header bg-white sticky-top hstack justify-content-between">
            <h4 class="fw-bolder mb-0">Actividades</h4>
            <a href="javascript:void(0);" class="app-sidebar-close-trigger d-flex" @click="sidebarOpen = false">
                <i class="feather-x"></i>
            </a>
        </div>
        <div class="content-sidebar-header">
            <a href="{{ route('agenda.create') }}" class="btn btn-primary w-100" wire:navigate>
                <i class="feather-plus me-2"></i>
                <span>Nova Actividade</span>
            </a>
        </div>
        <div class="content-sidebar-body">
            <ul class="nav d-flex flex-column nxl-content-sidebar-item">
                <li class="nav-item">
                    <a href="{{ route('agenda') }}" class="nav-link active" wire:navigate>
                        <i class="feather-layers"></i>
                        <span>Todas</span>
                    </a>
                </li>
                @foreach ($tipos as $tipo)
                    <li class="nav-item">
                        <a href="{{ route('agenda', ['tipo' => $tipo->value]) }}" class="nav-link" wire:navigate>
                            <i class="{{ $tipo->icon() }}"></i>
                            <span>{{ $tipo->labelPlural() }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
            <ul class="nav d-flex flex-column nxl-content-sidebar-item">
                <li class="px-4 my-2 fs-10 fw-bold text-uppercase text-muted">Estado</li>
                <li class="nav-item">
                    <a href="{{ route('agenda', ['status' => 'pendente']) }}" class="nav-link" wire:navigate>
                        <i class="feather-clock"></i>
                        <span>Pendentes</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('agenda', ['status' => 'concluida']) }}" class="nav-link" wire:navigate>
                        <i class="feather-check-circle"></i>
                        <span>Concluídas</span>
                    </a>
                </li>
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
                    <a href="javascript:void(0)" class="btn btn-light-brand dropdown-toggle" data-bs-toggle="dropdown" data-bs-offset="0,18">Nova Actividade</a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('agenda') }}" wire:navigate>Ver todas as actividades</a></li>
                        <li><a class="dropdown-item active" href="{{ route('agenda.create') }}" wire:navigate>Nova actividade</a></li>
                        <li><a class="dropdown-item" href="{{ route('agenda.calendar') }}" wire:navigate>Calendário</a></li>
                    </ul>
                </div>
            </div>
            <div class="page-header-right ms-auto">
                <div class="page-header-right-items">
                    <a href="{{ route('agenda') }}" class="btn btn-light-brand" wire:navigate>
                        <i class="feather-list me-2"></i>
                        <span>Lista</span>
                    </a>
                    <a href="{{ route('agenda.calendar') }}" class="btn btn-light-brand" wire:navigate>
                        <i class="feather-calendar me-2"></i>
                        <span>Calendário</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="content-area-body pb-0">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card stretch stretch-full">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ $editing ? 'Editar actividade' : 'Nova Actividade' }}</h5>
                        </div>
                        <div class="card-body">
                            <form wire:submit="save">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Tipo <span class="text-danger">*</span></label>
                                        <select class="form-control" wire:model="tipo" required>
                                            @foreach ($tipos as $tipoCase)
                                                <option value="{{ $tipoCase->value }}">{{ $tipoCase->label() }}</option>
                                            @endforeach
                                        </select>
                                        @error('tipo')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Cliente <span class="text-danger">*</span></label>
                                        <select class="form-control" wire:model="opportunity_id" required>
                                            <option value="">Seleccionar cliente…</option>
                                            @foreach ($oportunidades as $oportunidade)
                                                <option value="{{ $oportunidade->id }}">{{ $oportunidade->nome }}</option>
                                            @endforeach
                                        </select>
                                        @error('opportunity_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Descrição <span class="text-danger">*</span></label>
                                    <textarea class="form-control" rows="3" wire:model="descricao" required
                                              placeholder="Objectivo / tema da actividade"></textarea>
                                    @error('descricao')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Resultado</label>
                                    <textarea class="form-control" rows="2" wire:model="resultado"
                                              placeholder="Desfecho (preencher ao concluir)"></textarea>
                                    @error('resultado')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>

                                <div class="card border mb-3">
                                    <div class="card-body py-3">
                                        <h6 class="fw-bold mb-3">
                                            <i class="feather-arrow-right text-primary me-2"></i>Próxima acção
                                            <span class="badge bg-soft-primary text-primary ms-2">Obrigatória</span>
                                        </h6>
                                        <div class="row">
                                            <div class="col-md-8 mb-3 mb-md-0">
                                                <label class="form-label">Descrição da próxima acção <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" wire:model="proxima_accao" required
                                                       placeholder="Ex.: Enviar proposta revisada">
                                                @error('proxima_accao')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Prazo <span class="text-danger">*</span></label>
                                                <input type="date" class="form-control" wire:model="proxima_accao_prazo" required>
                                                @error('proxima_accao_prazo')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                            </div>
                                        </div>
                                        <small class="text-muted">Alimenta as Pendências do dashboard e os Alertas de PRAZO.</small>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Status</label>
                                        <select class="form-control" wire:model="status">
                                            @foreach ($estados as $estado)
                                                <option value="{{ $estado->value }}">{{ $estado->label() }}</option>
                                            @endforeach
                                        </select>
                                        @error('status')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Data da actividade</label>
                                        <input type="date" class="form-control" wire:model="data">
                                        @error('data')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="feather-save me-2"></i>Guardar actividade
                                    </button>
                                    <a href="{{ route('agenda') }}" class="btn btn-light-brand" wire:navigate>Cancelar</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card stretch stretch-full mb-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Legenda de tipos</h5>
                        </div>
                        <div class="card-body fs-13">
                            @foreach ($tipos as $tipo)
                                <div class="d-flex align-items-center {{ $loop->last ? 'mb-0' : 'mb-2' }}">
                                    <span class="badge {{ $tipo->badge() }} me-2">{{ $tipo->label() }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="card stretch stretch-full">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Regras</h5>
                        </div>
                        <div class="card-body fs-13">
                            <p class="mb-2"><i class="feather-info text-primary me-1"></i> Descrição, Próxima acção e Prazo são obrigatórios.</p>
                            <p class="mb-2"><i class="feather-info text-primary me-1"></i> Status inicia sempre em <strong>Pendente</strong>.</p>
                            <p class="mb-0"><i class="feather-info text-primary me-1"></i> Prazo vencido gera alerta <span class="badge bg-soft-danger text-danger">PRAZO</span>.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <x-ui.footer />
    </div>
</div>
