<?php

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    #[Locked]
    public string $token = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Mount the component.
     */
    public function mount(string $token): void
    {
        $this->token = $token;

        $this->email = request()->string('email');
    }

    /**
     * Reset the password for the given user.
     */
    public function resetPassword(): void
    {
        $this->validate([
            'token' => ['required'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = Password::reset(
            $this->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) {
                $user->forceFill([
                    'password' => Hash::make($this->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        if ($status != Password::PASSWORD_RESET) {
            $this->addError('email', __($status));

            return;
        }

        Session::flash('status', __($status));

        $this->redirectRoute('login', navigate: true);
    }
}; ?>

<div>
    <h1 class="auth-title">Redefinir senha</h1>
    <p class="auth-subtitle">
        Defina uma nova senha para a sua conta. Depois de guardar,
        será encaminhado para o login.
    </p>

    {{-- method="POST" + @csrf obrigatórios (FormsSecurityTest): sem JS/Livewire,
         o form nunca pode submeter como GET e expor dados na query string. --}}
    <form wire:submit="resetPassword" method="POST" class="form-stack" novalidate>
        @csrf

        <!-- Campo Email -->
        <div class="field-group">
            <label class="field-label" for="email">Email</label>
            <div class="field-control-wrap">
                <input wire:model="email" type="email"
                       class="field-input @error('email') is-invalid @enderror"
                       placeholder="seu.email@empresa.com" id="email" name="email"
                       required autofocus autocomplete="username" spellcheck="false">
            </div>
            @error('email')
                <div class="field-error" role="alert">{{ $message }}</div>
            @enderror
        </div>

        <!-- Campo Nova Senha -->
        <div class="field-group">
            <label class="field-label" for="password">Nova senha</label>
            <div class="field-control-wrap">
                <input wire:model="password" type="password"
                       class="field-input input-password @error('password') is-invalid @enderror"
                       placeholder="Digite a nova senha" id="password" name="password"
                       required autocomplete="new-password">
                <button type="button" class="btn-toggle-pwd"
                        aria-label="Mostrar ou ocultar senha"
                        onclick="(function(b){var i=b.parentNode.querySelector('input');var v=i.type==='password';i.type=v?'text':'password';b.querySelector('.eye-open').style.display=v?'none':'';b.querySelector('.eye-off').style.display=v?'':'none';})(this)">
                    <svg class="eye-open" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                    <svg class="eye-off" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none">
                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                        <line x1="1" y1="1" x2="23" y2="23"></line>
                    </svg>
                </button>
            </div>
            @error('password')
                <div class="field-error" role="alert">{{ $message }}</div>
            @enderror
        </div>

        <!-- Campo Confirmar Nova Senha -->
        <div class="field-group">
            <label class="field-label" for="password_confirmation">Confirmar nova senha</label>
            <div class="field-control-wrap">
                <input wire:model="password_confirmation" type="password"
                       class="field-input input-password @error('password_confirmation') is-invalid @enderror"
                       placeholder="Repita a nova senha" id="password_confirmation" name="password_confirmation"
                       required autocomplete="new-password">
                <button type="button" class="btn-toggle-pwd"
                        aria-label="Mostrar ou ocultar senha"
                        onclick="(function(b){var i=b.parentNode.querySelector('input');var v=i.type==='password';i.type=v?'text':'password';b.querySelector('.eye-open').style.display=v?'none':'';b.querySelector('.eye-off').style.display=v?'':'none';})(this)">
                    <svg class="eye-open" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                    <svg class="eye-off" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none">
                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                        <line x1="1" y1="1" x2="23" y2="23"></line>
                    </svg>
                </button>
            </div>
            @error('password_confirmation')
                <div class="field-error" role="alert">{{ $message }}</div>
            @enderror
        </div>

        <!-- Botão Principal -->
        <button type="submit" class="btn-sign-in" id="btnResetPwd"
                wire:loading.attr="disabled" wire:target="resetPassword">
            <span wire:loading.remove wire:target="resetPassword">Redefinir senha</span>
            <span wire:loading wire:target="resetPassword">A guardar…</span>
        </button>
    </form>

    <!-- Voltar ao login -->
    <div class="subactions-row is-center">
        <a href="{{ route('login') }}" class="link-forgot" wire:navigate>&larr; Voltar ao login</a>
    </div>
</div>
