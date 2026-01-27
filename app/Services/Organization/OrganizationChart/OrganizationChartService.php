<?php

namespace App\Services\Organization\OrganizationChart;

use App\Models\Organization\OrganizationChart\OrganizationChart;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class OrganizationChartService
{
    public function tree(): Collection
    {
        return OrganizationChart::with('children.children')
            ->where('hierarchy', 0)
            ->where('is_active', true)
            ->orderBy('order')
            ->get();
    }

    public function find(int $id): OrganizationChart
    {
        return OrganizationChart::findOrFail($id);
    }

    public function index(array $filters): Collection
    {
        $query = OrganizationChart::query();
        if ($filters['acronym']) {
            $query->where('acronym', 'like', '%' . strtoupper($filters['acronym']) . '%');
        }
        if ($filters['filter']) {
            $query->where('filter', 'like', '%' . strtolower($filters['filter']) . '%');
        }
        if ($filters['status'] !== 'all') {
            $query->where('is_active', $filters['status']);
        }
        return $query->orderBy('order')->get();
    }

    public function store(array $data): void
    {
        OrganizationChart::create($data);
        $this->reorder();
    }

    public function update(int $id, array $data): void
    {
        $organizationChart = OrganizationChart::findOrFail($id);
        $organizationChart->update($data);
        $this->reorder();
    }

    public function status(int $id): OrganizationChart
    {
        $organizationChart = OrganizationChart::findOrFail($id);
        return $organizationChart->toggleStatus();
    }

    public function reorder(): void
    {
        DB::transaction(function () {

            $organizations = OrganizationChart::orderBy('hierarchy')
                ->get()
                ->keyBy('id');

            foreach ($organizations as $organization) {

                if ($organization->hierarchy === 0) {
                    $organization->order = '0' . $organization->acronym;
                    $organization->number_hierarchy = 1;
                    $organization->save();
                    continue;
                }

                $predecessor = $organizations[$organization->hierarchy] ?? null;

                if (!$predecessor) {
                    continue;
                }

                $order = $predecessor->order . $organization->id . $organization->acronym;

                $organization->order = $order;
                $organization->number_hierarchy = preg_match_all('/\d+/', $order);
                $organization->save();
            }
        });
    }

}
