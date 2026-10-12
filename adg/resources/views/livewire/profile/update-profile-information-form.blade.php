<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component
{
    public string $name = '';

    public string $username = '';

    public string $email = '';

    /** Bandeira de sucesso — mostra o alerta "dados actualizados". */
    public bool $guardado = false;

    /**
     * Prefill com os dados do utilizador autenticado.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->username = Auth::user()->username;
        $this->email = Auth::user()->email;
    }

    /**
     * Esconde o alerta de sucesso assim que o utilizador volta a editar.
     */
    public function updated(): void
    {
        $this->guardado = false;
    }

    /**
     * Actualiza os dados do utilizador autenticado.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'min:3', 'max:50', 'regex:/^[A-Za-z0-9._-]+$/', Rule::unique(User::class, 'username')->ignore($user->id)],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class, 'email')->ignore($user->id)],
        ]);

        $user->fill($validated);

        // Se o email mudou, invalida a verificação (mantém comportamento Breeze).
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->guardado = true;
    }
}; ?>

<section>
    <div class="card stretch stretch-full mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Os meus dados</h5>
        </div>
        <div class="card-body">
            <p class="fs-13 text-muted mb-4">Actualize o seu nome, nome de utilizador e endereço de email.</p>

            @if ($guardado)
                <div class="alert alert-success d-flex align-items-center" role="alert">
                    <i class="feather-check-circle me-2"></i>
                    <span>Os seus dados foram actualizados com sucesso.</span>
                </div>
            @endif

            <form wire:submit="updateProfileInformation" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label" for="profile-name">Nome <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" wire:model="name" id="profile-name"
                           required autocomplete="name" placeholder="Ex.: Adailson Nogueira">
                    @error('name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="profile-username">Nome de utilizador <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" wire:model="username" id="profile-username"
                           required autocomplete="username" placeholder="Ex.: adailson">
                    <div class="form-text">Usado para iniciar sessão (junto com o email). Letras, números, ponto (.), hífen (-) ou sublinhado (_).</div>
                    @error('username')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="profile-email">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" wire:model="email" id="profile-email"
                           required autocomplete="username" placeholder="Ex.: adailson@csangroup.tech">
                    @error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>

                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="updateProfileInformation">
                    <i class="feather-save me-2"></i>Guardar alterações
                </button>
            </form>
        </div>
    </div>
</section>
