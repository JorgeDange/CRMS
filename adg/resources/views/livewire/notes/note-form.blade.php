{{-- BLOCO 5: formulario de nota (titulo opcional, conteudo, tipo, anexos) --}}
<div class="card mb-3">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title mb-0">{{ $editing ? 'Editar nota' : 'Nova nota' }}</h5>
        <button type="button" class="btn-close" wire:click="cancel" aria-label="Fechar"></button>
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
                <div class="col-md-8 mb-3">
                    <label class="form-label">Título</label>
                    <input type="text" class="form-control" wire:model="titulo" maxlength="255"
                           placeholder="Opcional — resumo da nota">
                    @error('titulo')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Tipo <span class="text-danger">*</span></label>
                    <select class="form-control" wire:model="tipo" required>
                        @foreach ($tipos as $tipoCase)
                            <option value="{{ $tipoCase->value }}">{{ $tipoCase->label() }}</option>
                        @endforeach
                    </select>
                    @error('tipo')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Conteúdo <span class="text-danger">*</span></label>
                <textarea class="form-control" rows="5" wire:model="conteudo" required
                          placeholder="Texto da nota"></textarea>
                @error('conteudo')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Anexos</label>
                <input type="file" class="form-control" wire:model="anexos" multiple>
                <small class="text-muted">Ficheiros guardados em armazenamento privado. Máx. 10 MB por ficheiro.</small>
                @error('anexos.*')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                <div class="mt-2" wire:loading wire:target="anexos">
                    <span class="spinner-border spinner-border-sm me-2"></span>A carregar…
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="save">
                    <span wire:loading.remove wire:target="save">
                        <i class="feather-save me-2"></i>Guardar nota
                    </span>
                    <span wire:loading wire:target="save">
                        <span class="spinner-border spinner-border-sm me-2"></span>A guardar…
                    </span>
                </button>
                <button type="button" class="btn btn-light-brand" wire:click="cancel">Cancelar</button>
            </div>
        </form>
    </div>
</div>
