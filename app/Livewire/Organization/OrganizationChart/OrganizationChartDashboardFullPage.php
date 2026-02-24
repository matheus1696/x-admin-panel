<?php

namespace App\Livewire\Organization\OrganizationChart;

use App\Services\Organization\OrganizationChart\OrganizationChartService;
use Livewire\Component;

class OrganizationChartDashboardFullPage extends Component
{
    public function render(OrganizationChartService $organizationChartService)
    {
        $organizationCharts = $organizationChartService->tree();

        return view('livewire.organization.organization-chart.organization-chart-dashboard-full-page', [
            'organizationCharts' => $organizationCharts
        ])->layout('layouts.guest');
    }
}
