<?php

namespace App\Services\Organization\OrganizationChart;

use App\Models\Organization\OrganizationChart\OrganizationChart;

class OrganizationChartService
{
    public function tree()
    {
        return OrganizationChart::with('children.children')
            ->where('hierarchy', 0)
            ->where('status', true)
            ->orderBy('order')
            ->get();
    }

    public function create(array $data): void
    {
        OrganizationChart::create([
            'title' => $data['title'],
            'acronym' => $data['acronym'],
            'hierarchy' => $data['hierarchy'],
        ]);

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

    public function reorder()
    {
        $organizations = OrganizationChart::orderBy('hierarchy')->get();

        foreach ($organizations as $organization) {

            if ($organization->hierarchy == 0) {
                $organization->order = '0' . $organization->acronym;
                $organization->number_hierarchy = 1;
                $organization->save();
            }

            $predecessor = OrganizationChart::find($organization->hierarchy);

            if ($predecessor) {
                $numberHierarchy = $predecessor->order . $organization->id . $organization->acronym;

                $organization->order = $numberHierarchy;
                $organization->number_hierarchy = preg_match_all('!\d+!', $numberHierarchy);
                $organization->save();
            }
        }
    }
}
