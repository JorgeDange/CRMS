<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component
{
    public string $password = '';

    /**
     * Elimina a conta do utilizador autenticado (após confirmar a palavra-passe).
     */
    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => ['required', 'string', 'current_password'],
        ]);

        tap(Auth::user(), $logout(...))->delete();

        $this->redirect('/', navigate: true);
    }
}; ?>

<section>
    <div class="card stretch stretch-full border-danger-subtle">
        <div class="card-header">
            <h5 class="card-title mb-0 text-danger">Eliminar conta</h5>
        </div>
        <div class="card-body">
            <p class="fs-13 text-muted mb-4">
                Ao eliminar a sua conta, todos os seus dados serão apagados de forma permanente.
                Introduza a sua palavra-passe para confirmar.
            </p>

            <form wire:submit="deleteUser" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label" for="del-password">Palavra-passe <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" wire:model="password" id="del-password"
                           required autocomplete="current-password" placeholder="Confirme com a sua palavra-passe">
                    @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>

                <button type="submit" class="btn btn-danger" wire:loading.attr="disabled" wire:target="deleteUser">
                    <i class="feather-trash-2 me-2"></i>Eliminar conta
                </button>
            </form>
        </div>
    </div>
</section>
