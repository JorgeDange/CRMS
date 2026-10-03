{{-- Sidebar — copiado de crm-prototipo/index.html (PROMPT_MASTER sec.2, sec.7, sec.11) --}}
<nav class="nxl-navigation">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="{{ route('dashboard') }}" class="b-brand" wire:navigate>
                <img src="{{ asset('images/logo-d.png') }}" alt="{{ config('app.name') }}" class="logo logo-lg d-none d-lg-inline" style="height:50px;width:auto;vertical-align:middle">
                <img src="{{ asset('assets/images/logo-icon.png') }}" alt="{{ config('app.name') }}" class="logo logo-sm d-lg-none" style="width:42px;height:auto;vertical-align:middle">
            </a>
        </div>
        <div class="navbar-content">
            {{-- Menu conforme PROMPT_MASTER sec.7 — itens por entregar ficam "Em breve" --}}
            <ul class="nxl-navbar">
                <li class="nxl-item nxl-caption">
                    <label>Menu</label>
                </li>
                <li class="nxl-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}" class="nxl-link" wire:navigate>
                        <span class="nxl-micon"><i class="feather-airplay"></i></span>
                        <span class="nxl-mtext">Dashboard</span>
                    </a>
                </li>
                <li class="nxl-item nxl-hasmenu" x-data="{ open: {{ in_array(request()->route()?->getName(), ['pipeline']) ? 'true' : 'false'}} }" :class="open && 'nxl-trigger'">
                    <a href="javascript:void(0);" class="nxl-link" @click.prevent="open = !open">
                        <span class="nxl-micon"><i class="feather-bar-chart-2"></i></span>
                        <span class="nxl-mtext">Oportunidades</span>
                        <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                    </a>
                    {{-- Ordem das sub-opcoes: 1. Nova Oportunidade 2. Pipeline (decisao do cliente 2026-10-03) --}}
                    <ul class="nxl-submenu" x-show="open" x-cloak>
                        <li class="nxl-item {{ request()->routeIs('pipeline') ? 'active' : '' }}">
                            <a href="{{ route('pipeline') }}" class="nxl-link" wire:navigate>Pipeline</a>
                        </li>
                        <li class="nxl-item">
                            <span class="nxl-link cursor-not-allowed">Nova Oportunidade <span class="badge bg-gray-200 text-dark rounded ms-1">Em breve</span></span>
                        </li>
                    </ul>
                </li>
                <li class="nxl-item">
                    <span class="nxl-link cursor-not-allowed">
                        <span class="nxl-micon"><i class="feather-calendar"></i></span>
                        <span class="nxl-mtext">Agenda Comercial</span>
                        <span class="badge bg-gray-200 text-dark rounded ms-1">Em breve</span>
                    </span>
                </li>
                <li class="nxl-item">
                    <span class="nxl-link cursor-not-allowed">
                        <span class="nxl-micon"><i class="feather-dollar-sign"></i></span>
                        <span class="nxl-mtext">Conciliações</span>
                        <span class="badge bg-gray-200 text-dark rounded ms-1">Em breve</span>
                    </span>
                </li>
                <li class="nxl-item">
                    <span class="nxl-link cursor-not-allowed">
                        <span class="nxl-micon"><i class="feather-briefcase"></i></span>
                        <span class="nxl-mtext">Contratos</span>
                        <span class="badge bg-gray-200 text-dark rounded ms-1">Em breve</span>
                    </span>
                </li>
                <li class="nxl-item">
                    <span class="nxl-link cursor-not-allowed">
                        <span class="nxl-micon"><i class="feather-package"></i></span>
                        <span class="nxl-mtext">Produtos</span>
                        <span class="badge bg-gray-200 text-dark rounded ms-1">Em breve</span>
                    </span>
                </li>
                <li class="nxl-item">
                    <span class="nxl-link cursor-not-allowed">
                        <span class="nxl-micon"><i class="feather-bell"></i></span>
                        <span class="nxl-mtext">Alertas</span>
                        <span class="badge bg-gray-200 text-dark rounded ms-1">Em breve</span>
                    </span>
                </li>
                <li class="nxl-item">
                    <span class="nxl-link cursor-not-allowed">
                        <span class="nxl-micon"><i class="feather-activity"></i></span>
                        <span class="nxl-mtext">Painel Executivo</span>
                        <span class="badge bg-gray-200 text-dark rounded ms-1">Em breve</span>
                    </span>
                </li>
            </ul>
            <div class="card text-center">
                <div class="card-body">
                    <i class="feather-sunrise fs-4 text-dark"></i>
                    <h6 class="mt-4 text-dark fw-bolder">Lead Inteligente</h6>
                    <p class="fs-11 my-3 text-dark">Sistema de gestão comercial B2B — protótipo para validação.</p>
                </div>
            </div>
        </div>
    </div>
    <div class="nxl-menu-overlay" x-show="mobile" x-cloak @click="toggleMobile()"></div>
</nav>
