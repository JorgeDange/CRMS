<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Volt\Component;

new class extends Component
{
    public string $name = '';

    public string $username = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    /** Bandeira de sucesso — mostra o alerta "utilizador criado". */
    public bool $criado = false;

    /** Username do último utilizador criado (para a mensagem de sucesso). */
    public ?string $ultimoUsername = null;

    /**
     * Esconde o alerta de sucesso assim que o formulário volta a ser editado.
     */
    public function updated(): void
    {
        $this->criado = false;
    }

    /**
     * Cria uma nova conta de utilizador.
     */
    public function createUser(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'min:3', 'max:50', 'regex:/^[A-Za-z0-9._-]+$/', 'unique:users,username'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', Password::defaults(), 'confirmed'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $this->ultimoUsername = $user->username;

        $this->reset('name', 'username', 'email', 'password', 'password_confirmation');

        $this->criado = true;
    }
}; ?>

<section>
    <div class="card stretch stretch-full mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Criar conta de utilizador</h5>
        </div>
        <div class="card-body">
            <p class="fs-13 text-muted mb-4">
                Crie uma nova conta de acesso ao CRM. O novo utilizador consegue entrar com o email ou com o nome de utilizador.
            </p>

            @if ($criado)
                <div class="alert alert-success d-flex align-items-center" role="alert">
                    <i class="feather-check-circle me-2"></i>
                    <span>Utilizador <strong>{{ $ultimoUsername }}</strong> criado com sucesso.</span>
                </div>
            @endif

            <form wire:submit="createUser" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label" for="new-name">Nome <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" wire:model="name" id="new-name"
                           required placeholder="Ex.: Maria Silva">
                    @error('name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="new-username">Nome de utilizador <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" wire:model="username" id="new-username"
                           required placeholder="Ex.: maria.silva">
                    <div class="form-text">Letras, números, ponto (.), hífen (-) ou sublinhado (_). Deve ser único.</div>
                    @error('username')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="new-email">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" wire:model="email" id="new-email"
                           required placeholder="Ex.: maria@csangroup.tech">
                    @error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="new-password">Palavra-passe <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" wire:model="password" id="new-password"
                           required autocomplete="new-password">
                    @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="new-password-confirmation">Confirmar palavra-passe <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" wire:model="password_confirmation" id="new-password-confirmation"
                           required autocomplete="new-password">
                    @error('password_confirmation')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>

                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="createUser">
                    <i class="feather-user-plus me-2"></i>Criar conta
                </button>
            </form>
        </div>
    </div>
</section>
