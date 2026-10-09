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
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
    @endif

    {{-- Formulario copiado de crm-prototipo/auth-login-cover.html (PROMPT_MASTER sec.2/8.1) --}}
    <form wire:submit="login">
        <div class="mb-4">
            <input wire:model="form.login" type="text"
                   class="form-control @error('form.login') is-invalid @enderror"
                   placeholder="Email ou username" id="login" name="login"
                   required autofocus autocomplete="username">
            @error('form.login')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <input wire:model="form.password" type="password"
                   class="form-control @error('form.password') is-invalid @enderror"
                   placeholder="Palavra-passe" id="password" name="password"
                   required autocomplete="current-password">
            @error('form.password')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
        <div class="d-flex align-items-center justify-content-between">
            <div class="custom-control custom-checkbox">
                <input wire:model="form.remember" type="checkbox" class="custom-control-input" id="rememberMe" name="remember">
                <label class="custom-control-label c-pointer" for="rememberMe">Lembrar-me</label>
            </div>
            <div>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="fs-11 text-primary" wire:navigate>Esqueceu a password?</a>
                @endif
            </div>
        </div>
        <div class="mt-5">
            <button type="submit" class="btn btn-lg btn-primary w-100">Entrar</button>
        </div>
    </form>
</div>
