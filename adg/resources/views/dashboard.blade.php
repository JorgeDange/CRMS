<x-app-layout>
    <x-slot name="header">Dashboard</x-slot>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Bem-vindo, {{ auth()->user()->name }}</h5>
                    <p class="card-text fs-12 text-muted mb-0">Sistema de gestão comercial B2B — {{ config('app.name') }}.</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
