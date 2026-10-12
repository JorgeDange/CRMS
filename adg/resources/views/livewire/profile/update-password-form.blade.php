<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;

new class extends Component
{
    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    /** Bandeira de sucesso — mostra o alerta "palavra-passe alterada". */
    public bool $guardado = false;

    /**
     * Esconde o alerta de sucesso assim que o utilizador volta a editar.
     */
    public function updated(): void
    {
        $this->guardado = false;
    }

    /**
     * Altera a palavra-passe do utilizador autenticado.
     */
    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');

            throw $e;
        }

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->guardado = true;
    }
}; ?>

<section>
    <div class="card stretch stretch-full mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Alterar palavra-passe</h5>
        </div>
        <div class="card-body">
            <p class="fs-13 text-muted mb-4">Garanta que a sua conta usa uma palavra-passe longa e segura.</p>

            @if ($guardado)
                <div class="alert alert-success d-flex align-items-center" role="alert">
                    <i class="feather-check-circle me-2"></i>
                    <span>A sua palavra-passe foi alterada com sucesso.</span>
                </div>
            @endif

            <form wire:submit="updatePassword" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label" for="pw-current">Palavra-passe actual <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" wire:model="current_password" id="pw-current"
                           required autocomplete="current-password">
                    @error('current_password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="pw-new">Nova palavra-passe <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" wire:model="password" id="pw-new"
                           required autocomplete="new-password">
                    @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="pw-confirm">Confirmar nova palavra-passe <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" wire:model="password_confirmation" id="pw-confirm"
                           required autocomplete="new-password">
                    @error('password_confirmation')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>

                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="updatePassword">
                    <i class="feather-key me-2"></i>Alterar palavra-passe
                </button>
            </form>
        </div>
    </div>
</section>
