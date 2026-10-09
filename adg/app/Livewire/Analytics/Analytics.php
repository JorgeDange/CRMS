<?php

namespace App\Livewire\Analytics;

use App\Services\DashboardService;
use Livewire\Component;

class Analytics extends Component
{
    public function render()
    {
        $service = new DashboardService;
        $data = $service->analyticsData();
        $tables = $service->tables();

        return view('livewire.analytics.analytics', [
            ...$data['kpis'],
            'charts' => $data['charts'],
            'gestores' => $tables['gestores'] ?? [],
            'topClientes' => $tables['topClientes'] ?? [],
        ]);
    }
}
