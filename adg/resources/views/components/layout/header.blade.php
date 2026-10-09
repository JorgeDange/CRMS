{{-- Header — copiado de crm-prototipo/index.html (PROMPT_MASTER sec.11) --}}
<header class="nxl-header">
    <div class="header-wrapper">
        <!--! [Start] Header Left !-->
        <div class="header-left d-flex align-items-center gap-4">
            <!--! [Start] nxl-head-mobile-toggler !-->
            <a href="javascript:void(0);" class="nxl-head-mobile-toggler" id="mobile-collapse" @click="toggleMobile()">
                <div class="hamburger hamburger--arrowturn" :class="mobile && 'is-active'">
                    <div class="hamburger-box">
                        <div class="hamburger-inner"></div>
                    </div>
                </div>
            </a>
            <!--! [Start] nxl-navigation-toggle !-->
            <div class="nxl-navigation-toggle">
                <a href="javascript:void(0);" id="menu-mini-button" x-show="!mini" x-cloak @click="toggleMini()">
                    <i class="feather-align-left"></i>
                </a>
                <a href="javascript:void(0);" id="menu-expend-button" x-show="mini" x-cloak @click="toggleMini()">
                    <i class="feather-arrow-right"></i>
                </a>
            </div>
        </div>
        <!--! [End] Header Left !-->
        <!--! [Start] Header Right !-->
        <div class="header-right ms-auto">
            <div class="d-flex align-items-center">
                <div class="dropdown nxl-h-item">
                    {{-- href sem navegacao: com route() o dropdown nunca abria (prototipo usa href="#") --}}
                    <a href="javascript:void(0);" class="nxl-head-link me-0 position-relative" data-bs-toggle="dropdown" role="button" data-bs-auto-close="outside">
                        <i class="feather-bell fs-18 text-dark"></i>
                        @php $unreadCount = \App\Models\Alert::where('user_id', auth()->id())->where('lido', false)->where('resolvido', false)->count(); @endphp
                        @if ($unreadCount > 0)
                            <span class="badge bg-danger nxl-h-badge position-absolute top-0 start-100 translate-middle">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                        @endif
                    </a>
                    <div class="dropdown-menu dropdown-menu-end nxl-h-dropdown nxl-notifications-menu">
                        <div class="d-flex justify-content-between align-items-center notifications-head">
                            <h6 class="fw-bold text-dark mb-0">Notificações</h6>
                            <form method="POST" action="{{ route('alerts.markAll') }}" class="ms-auto">
                                @csrf
                                <button type="submit" class="fs-11 text-success text-end border-0 bg-transparent p-0">
                                    <i class="feather-check"></i>
                                    <span>Marcar todas como lidas</span>
                                </button>
                            </form>
                        </div>
                        @php $recentAlerts = \App\Models\Alert::where('user_id', auth()->id())->where('resolvido', false)->orderBy('created_at', 'desc')->limit(5)->get(); @endphp
                        @if ($recentAlerts->isEmpty())
                            <div class="text-center notifications-footer p-3">
                                <i class="feather-bell fs-1 mb-3 text-muted"></i>
                                <p class="text-muted mb-0">Sem notificações novas.</p>
                            </div>
                        @else
                            <div class="notifications-item">
                                @foreach ($recentAlerts as $alert)
                                    <div class="notifications-desc">
                                        <a href="javascript:void(0);" class="font-body text-truncate-2-line">
                                            <span class="fw-semibold text-dark">{{ $alert->tipo->label() }}</span>
                                            <span class="text-muted ms-2">{{ $alert->mensagem }}</span>
                                        </a>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="notifications-date text-muted border-bottom border-bottom-dashed">
                                                {{ $alert->created_at?->diffForHumans() }}
                                            </div>
                                            <div class="d-flex align-items-center float-end gap-2">
                                                @if (! $alert->lido)
                                                    <a href="javascript:void(0);" class="d-block wd-8 ht-8 rounded-circle bg-gray-300" data-bs-toggle="tooltip" title="Marcar como lido"></a>
                                                @endif
                                                <a href="javascript:void(0);" class="text-danger" data-bs-toggle="tooltip" title="Remover">
                                                    <i class="feather-x fs-12"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        <div class="text-center notifications-footer">
                            <a href="{{ route('alerts') }}" class="fs-13 fw-semibold text-dark" wire:navigate>Todas as notificações</a>
                        </div>
                    </div>
                </div>
                <div class="dropdown nxl-h-item">
                    <a href="javascript:void(0);" data-bs-toggle="dropdown" role="button" data-bs-auto-close="outside">
                        <img src="{{ asset('assets/images/avatar/1.png') }}" alt="user-image" class="img-fluid user-avtar me-0" />
                    </a>
                    <div class="dropdown-menu dropdown-menu-end nxl-h-dropdown nxl-user-dropdown">
                        <div class="dropdown-header">
                            <div class="d-flex align-items-center">
                                <img src="{{ asset('assets/images/avatar/1.png') }}" alt="user-image" class="img-fluid user-avtar" />
                                <div>
                                    <h6 class="text-dark mb-0">{{ auth()->user()->name }}</h6>
                                    <span class="fs-12 fw-medium text-muted"><a href="mailto:{{ auth()->user()->email }}">{{ auth()->user()->email }}</a></span>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('profile') }}" class="dropdown-item" wire:navigate>
                            <i class="feather-user"></i>
                            <span>Meu perfil</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <i class="feather-log-out"></i>
                                <span>Terminar sessão</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!--! [End] Header Right !-->
    </div>
</header>