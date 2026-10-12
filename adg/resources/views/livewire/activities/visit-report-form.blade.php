{{-- Formulario do Relatorio de Visita (PROMPT_MASTER 8.5 / Sprint 3) --}}
<div class="card mb-3">
    <div class="card-header">
        <h5 class="card-title mb-0">Novo Relatório de Visita</h5>
    </div>
    <div class="card-body">
        <form wire:submit="save" method="POST">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Data da visita <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" wire:model.defer="data_visita" required>
                    @error('data_visita')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-8 mb-3">
                    <label class="form-label">Pessoas presentes</label>
                    <input type="text" class="form-control" wire:model.defer="pessoas_presentes"
                           placeholder="Ex.: Cliente, responsável comercial…">
                    @error('pessoas_presentes')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Objectivo</label>
                <input type="text" class="form-control" wire:model.defer="objectivo"
                       placeholder="Objectivo da visita (opcional)">
                @error('objectivo')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Assuntos tratados <span class="text-danger">*</span></label>
                <textarea class="form-control" rows="3" wire:model.defer="assuntos_tratados"
                          placeholder="O que foi tratado na visita…"></textarea>
                @error('assuntos_tratados')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Resultado</label>
                    <input type="text" class="form-control" wire:model.defer="resultado"
                           placeholder="Resultado da visita (opcional)">
                    @error('resultado')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Próxima acção</label>
                    <input type="text" class="form-control" wire:model.defer="proxima_accao"
                           placeholder="Próxima acção (opcional)">
                    @error('proxima_accao')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="feather-save me-2"></i>Guardar Relatório
                </button>
            </div>
        </form>
    </div>
</div>
