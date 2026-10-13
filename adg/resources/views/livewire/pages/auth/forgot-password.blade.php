<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $email = '';

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            $this->only('email')
        );

        if ($status != Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));

            return;
        }

        $this->reset('email');

        session()->flash('status', __($status));
    }
}; ?>

<div>
    <h1 class="auth-title">Recuperar senha</h1>
    <p class="auth-subtitle">
        Esqueceu-se da senha? Sem problema — indique o seu email e enviaremos
        um link de redefinição que lhe permite escolher uma nova.
    </p>

    <!-- Estado da sessão (link enviado com sucesso) -->
    @if (session('status'))
        <div class="guest-status-alert" role="alert">
            <i class="feather-check-circle"></i>
            {{ session('status') }}
        </div>
    @endif

    {{-- method="POST" + @csrf obrigatórios (FormsSecurityTest): sem JS/Livewire,
         o form nunca pode submeter como GET e expor o email na query string. --}}
    <form wire:submit="sendPasswordResetLink" method="POST" class="form-stack" novalidate>
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

        <!-- Botão Principal -->
        <button type="submit" class="btn-sign-in" id="btnSendReset"
                wire:loading.attr="disabled" wire:target="sendPasswordResetLink">
            <span wire:loading.remove wire:target="sendPasswordResetLink">Enviar link de redefinição</span>
            <span wire:loading wire:target="sendPasswordResetLink">A enviar…</span>
        </button>
    </form>

    <!-- Voltar ao login -->
    <div class="subactions-row is-center">
        <a href="{{ route('login') }}" class="link-forgot" wire:navigate>&larr; Voltar ao login</a>
    </div>
</div>
