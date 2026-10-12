<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false));
    }
}; ?>

<div>
    <!-- Session Status -->
    @if (session('status'))
        <div class="guest-status-alert" role="alert">
            <i class="feather-check-circle"></i>
            {{ session('status') }}
        </div>
    @endif

    {{-- Frontend copiado de prototipo/ui/index.html — backend auth inalterado --}}
    {{-- method="POST" + @csrf OBRIGATÓRIOS: sem eles, se o JS/Livewire ainda não
         tiver carregado, o browser submete o form nativamente como GET e os
         dados de login ficam expostos na query string da URL (falha de
         segurança corrigida a 2026-10-12). --}}
    <form wire:submit="login" method="POST" action="{{ route('login') }}" class="form-stack" novalidate>
        @csrf
        @if ($errors->any() && !$errors->has('form.login') && !$errors->has('form.password'))
            <div class="field-error" style="margin-bottom: 16px;" role="alert">
                @foreach ($errors->all() as $erro)
                    <div>{{ $erro }}</div>
                @endforeach
            </div>
        @endif

        <!-- Campo Email / Username -->
        <div class="field-group">
            <label class="field-label" for="login">Email ou username</label>
            <div class="field-control-wrap">
                <input wire:model="form.login" type="text"
                       class="field-input @error('form.login') is-invalid @enderror"
                       placeholder="seu.email@empresa.com" id="login" name="login"
                       required autofocus autocomplete="username" spellcheck="false">
            </div>
            @error('form.login')
                <div class="field-error" role="alert">{{ $message }}</div>
            @enderror
        </div>

        <!-- Campo Senha -->
        <div class="field-group">
            <label class="field-label" for="password">Senha</label>
            <div class="field-control-wrap">
                <input wire:model="form.password" type="password"
                       class="field-input input-password @error('form.password') is-invalid @enderror"
                       placeholder="Digite a sua senha" id="password" name="password"
                       required autocomplete="current-password">
                <button type="button" class="btn-toggle-pwd" id="togglePwd"
                        aria-label="Mostrar ou ocultar senha" onclick="adgTogglePwd()">
                    <svg id="eyeIcon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                </button>
            </div>
            @error('form.password')
                <div class="field-error" role="alert">{{ $message }}</div>
            @enderror
        </div>

        <!-- Botão Principal de Login -->
        <button type="submit" class="btn-sign-in" id="btnSignIn" wire:loading.attr="disabled" wire:target="login">
            <span wire:loading.remove wire:target="login">Entrar</span>
            <span wire:loading wire:target="login">A entrar…</span>
        </button>

        <!-- Lembrar de mim e Esqueci minha senha -->
        <div class="subactions-row">
            <label class="checkbox-item" for="rememberMe">
                <input wire:model="form.remember" type="checkbox" class="checkbox-native" id="rememberMe" name="remember">
                <span class="checkbox-box">
                    <svg class="checkbox-icon" width="11" height="9" viewBox="0 0 11 9">
                        <path d="M1.5 4.5L4 7L9.5 1.5"/>
                    </svg>
                </span>
                <span>Lembrar de mim</span>
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="link-forgot" wire:navigate>Esqueceu a senha?</a>
            @endif
        </div>
    </form>
</div>

<script>
    function adgTogglePwd() {
        var pwd = document.getElementById('password');
        var eyeIcon = document.getElementById('eyeIcon');
        if (!pwd) return;

        var isPassword = pwd.getAttribute('type') === 'password';
        pwd.setAttribute('type', isPassword ? 'text' : 'password');

        if (eyeIcon) {
            eyeIcon.innerHTML = isPassword
                ? '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>'
                : '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
        }
    }
</script>
