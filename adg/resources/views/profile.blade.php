{{-- Perfil do utilizador — reconstruído no tema do sistema (Bootstrap) e em
     PT-PT. Substitui o scaffold Breeze/Tailwind original (que não combinava
     com o resto do CRM). Quatro blocos: os meus dados, alterar palavra-passe,
     criar conta de utilizador (novo) e eliminar conta. --}}
<x-app-layout :pageHeaderBreadcrumb="['Definições', 'Meu Perfil']">
    <x-slot name="header">Meu Perfil</x-slot>

    <div class="row">
        {{-- Coluna esquerda: definições pessoais --}}
        <div class="col-xl-7">
            <livewire:profile.update-profile-information-form />
            <livewire:profile.update-password-form />
        </div>

        {{-- Coluna direita: gestão de utilizadores --}}
        <div class="col-xl-5">
            <livewire:profile.create-user-form />
            <livewire:profile.delete-user-form />
        </div>
    </div>
</x-app-layout>
