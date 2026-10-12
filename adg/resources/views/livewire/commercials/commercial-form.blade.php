{{-- Formulário de Comercial — Sprint 9 --}}
{{-- Raiz unica obrigatoria (Livewire); content-area ocupa toda a largura (sem content-sidebar) --}}
<div class="d-flex flex-grow-1">
    <div class="content-area">
        <div class="content-area-header sticky-top">
            <div class="page-header-left d-flex align-items-center gap-2">
                <div class="dropdown">
                    <a href="javascript:void(0)" class="btn btn-light-brand dropdown-toggle" data-bs-toggle="dropdown" data-bs-offset="0,16">
                        <i class="feather-users me-2"></i>
                        <span>{{ $editing ? 'Editar Comercial' : 'Novo Comercial' }}</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item" href="{{ route('commercials') }}" wire:navigate>
                            <i class="feather-list me-2"></i>
                            <span>Ver todos</span>
                        </a>
                        <a class="dropdown-item" href="{{ route('commercials.create') }}" wire:navigate>
                            <i class="feather-plus me-2"></i>
                            <span>Novo Comercial</span>
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
                                       wire:click="toggleStatus({{ $commercialId }})"
                                       @if($activo) style="display:none" @endif>
                                        <i class="feather-check-circle me-2"></i>
                                        <span>Activar</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item"
                                       href="javascript:void(0);"
                                       wire:click="toggleStatus({{ $commercialId }})"
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
                            <h5 class="card-title mb-0">{{ $editing ? 'Editar Comercial' : 'Novo Comercial' }}</h5>
                        </div>
                        <div class="card-body">
                            <form wire:submit="save" method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Nome <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" wire:model="nome" required
                                           placeholder="Ex.: Comercial Norte">
                                    @error('nome')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Descrição</label>
                                    <textarea class="form-control" rows="3" wire:model="descricao"
                                              placeholder="Descrição da carteira ou área de actuação..."></textarea>
                                    @error('descricao')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-3 form-check form-switch">
                                    <input class="form-check-input" type="checkbox" wire:model="activo" id="commercialActivo">
                                    <label class="form-check-label" for="commercialActivo">Activo</label>
                                    @error('activo')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="feather-save me-2"></i>{{ $editing ? 'Guardar alterações' : 'Criar Comercial' }}
                                    </button>
                                    <a href="{{ route('commercials') }}" class="btn btn-light-brand" wire:navigate>Cancelar</a>
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
                            <p class="mb-2"><i class="feather-info text-primary me-1"></i> Nome é obrigatório e deve ser único.</p>
                            <p class="mb-2"><i class="feather-info text-primary me-1"></i> Use a descrição para detalhar a região ou área de actuação.</p>
                            <p class="mb-0"><i class="feather-info text-primary me-1"></i> Comerciais inactivos não aparecem nas listas de selecção de oportunidades/produtos.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <x-ui.footer />
    </div>
</div>