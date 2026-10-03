@use('App\Livewire\Opportunities\Pipeline')

<x-app-layout>
    <x-slot name="header">Oportunidades</x-slot>

    <x-slot name="pageHeaderRight">
        <a class="btn btn-icon btn-light-brand" data-bs-toggle="collapse" data-bs-target="#collapseOne">
            <i class="feather-bar-chart"></i>
        </a>
        <div class="dropdown">
            <a class="btn btn-icon btn-light-brand" data-bs-toggle="dropdown" data-bs-offset="0, 10" data-bs-auto-close="outside">
                <i class="feather-filter"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-end">
                @foreach (Pipeline::COLUMNS as $stage => $config)
                    <a href="javascript:void(0);" class="dropdown-item">
                        <span class="wd-7 ht-7 bg-{{ $config['dot'] }} rounded-circle d-inline-block me-3"></span>
                        <span>{{ $config['label'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>
        <x-ui.button icon="plus" href="javascript:void(0);">
            <span>Nova Oportunidade</span>
        </x-ui.button>
    </x-slot>

    <x-slot name="pageHeaderBelow">
        <div id="collapseOne" class="accordion-collapse collapse page-header-collapse">
            <div class="accordion-body pb-2">
                @livewire('opportunities.stats')
            </div>
        </div>
    </x-slot>

    @livewire('opportunities.pipeline')
</x-app-layout>
