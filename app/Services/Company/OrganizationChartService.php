<?php

namespace App\Services\Company;

use App\Models\Company\OrganizationChart;

class OrganizationChartService
{
    public function tree()
    {
        return OrganizationChart::with('children.children') // 2 níveis para começar
            ->whereNull('parent_id')
            ->orderBy('order')
            ->get();
    }
}
