<?php

namespace App\Livewire\Company;

use App\Services\Company\OrganizationChartService;
use Livewire\Component;

class OrganizationChartDashboardPage extends Component
{
    public function render(OrganizationChartService $organizationChartService)
    {
        $organizationCharts = $organizationChartService->tree();

        return view('livewire.company.organization-chart-dashboard-page', [
            'organizationCharts' => $organizationCharts
        ])->layout('layouts.app');
    }
}
