{{-- Formulario de actividade — copiado de crm-prototipo/apps-notes.html (Sprint 5, 8.4) --}}
{{-- Raiz unica obrigatoria (Livewire); content-area ocupa toda a largura (sem content-sidebar) --}}
<div class="d-flex flex-grow-1">
    <div class="content-area">
        <div class="content-area-header sticky-top">
            <div class="page-header-left d-flex align-items-center gap-2">
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
                <div class="hstack gap-2">
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
                                        <label class="form-label">Tipo <span class="text-danger">*</span></label>
                                        <select class="form-control" wire:model.live="tipo" required>
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

                                {{-- BLOCO 3: campo condicional quando o tipo e "Outros" --}}
                                @if ($tipo === 'outros')
                                    <div class="mb-3">
                                        <label class="form-label">Especifique <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" wire:model="tipo_outros" maxlength="100"
                                               placeholder="Descrição personalizada do tipo de actividade" required>
                                        @error('tipo_outros')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                @endif

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
                                    <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="save">
                                        <span wire:loading.remove wire:target="save">
                                            <i class="feather-save me-2"></i>Guardar actividade
                                        </span>
                                        <span wire:loading wire:target="save">
                                            <span class="spinner-border spinner-border-sm me-2"></span>A guardar…
                                        </span>
                                    </button>
                                    <a href="{{ route('agenda') }}" class="btn btn-light-brand" wire:navigate>Cancelar</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card stretch stretch-full">
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
                </div>
            </div>
        </div>

        <x-ui.footer />
    </div>
</div>
