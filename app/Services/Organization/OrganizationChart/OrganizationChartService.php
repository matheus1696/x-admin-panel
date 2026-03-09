<?php

namespace App\Services\Organization\OrganizationChart;

use App\Models\Organization\OrganizationChart\OrganizationChart;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class OrganizationChartService
{
    public function tree(): Collection
    {
        return OrganizationChart::with([
            'responsibleUser',
            'children.responsibleUser',
            'children.children.responsibleUser',
        ])
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
        $query = OrganizationChart::query()
            ->withCount('users')
            ->with('responsibleUser');
        if ($filters['acronym']) {
            $query->where('acronym', 'like', '%'.strtoupper($filters['acronym']).'%');
        }
        if ($filters['filter']) {
            $query->where('filter', 'like', '%'.strtolower($filters['filter']).'%');
        }
        if ($filters['status'] !== 'all') {
            $query->where('is_active', $filters['status']);
        }
        if (($filters['responsible_user_id'] ?? 'all') !== 'all') {
            $query->where('responsible_user_id', (int) $filters['responsible_user_id']);
        }

        return $query->orderBy('order')->get();
    }

    public function store(array $data): void
    {
        $this->assertValidHierarchy((int) ($data['hierarchy'] ?? 0));
        OrganizationChart::create($data);
        $this->reorder();
    }

    public function update(int $id, array $data): void
    {
        $this->assertValidHierarchy((int) ($data['hierarchy'] ?? 0), $id);
        $organizationChart = OrganizationChart::findOrFail($id);
        $organizationChart->update($data);
        $this->reorder();
    }

    public function status(int $id): OrganizationChart
    {
        $organizationChart = OrganizationChart::findOrFail($id);

        return $organizationChart->toggleStatus();
    }

    public function syncUsers(int $organizationId, array $userIds): void
    {
        $organizationChart = OrganizationChart::findOrFail($organizationId);
        $organizationChart->users()->sync($userIds);
    }

    public function reorder(): void
    {
        DB::transaction(function () {

            $organizations = OrganizationChart::orderBy('hierarchy')
                ->get()
                ->keyBy('id');

            foreach ($organizations as $organization) {

                if ($organization->hierarchy === 0) {
                    $organization->order = '0'.$organization->acronym;
                    $organization->number_hierarchy = 1;
                    $organization->save();

                    continue;
                }

                $predecessor = $organizations[$organization->hierarchy] ?? null;

                if (! $predecessor) {
                    throw new \RuntimeException('Setor com hierarquia invalida detectado durante reorder.');
                }

                $order = $predecessor->order.$organization->id.$organization->acronym;

                $organization->order = $order;
                $organization->number_hierarchy = preg_match_all('/\d+/', $order);
                $organization->save();
            }
        });
    }

    private function assertValidHierarchy(int $hierarchyId, ?int $currentId = null): void
    {
        if ($hierarchyId === 0) {
            return;
        }

        if ($currentId !== null && $hierarchyId === $currentId) {
            throw new \RuntimeException('Um setor nao pode ser pai dele mesmo.');
        }

        $parent = OrganizationChart::query()->find($hierarchyId);

        if (! $parent) {
            throw new \RuntimeException('Setor pai nao encontrado.');
        }

        if ($currentId === null) {
            return;
        }

        $visited = [];
        $cursor = $parent;

        while ($cursor && $cursor->hierarchy !== 0) {
            if (in_array($cursor->id, $visited, true)) {
                throw new \RuntimeException('Ciclo de hierarquia detectado.');
            }

            $visited[] = $cursor->id;

            if ($cursor->hierarchy === $currentId) {
                throw new \RuntimeException('A hierarquia nao pode apontar para um setor descendente.');
            }

            $cursor = OrganizationChart::query()->find($cursor->hierarchy);
        }
    }
}
