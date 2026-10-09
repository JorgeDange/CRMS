<?php

namespace App\Livewire\Dashboard;

use App\Services\DashboardService;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $service = new DashboardService;
        $data = $service->dashboardData();

        return view('livewire.dashboard.dashboard', $data);
    }
}
