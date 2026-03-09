<?php

namespace App\Livewire\Organization\OrganizationChart;

use App\Livewire\Traits\Modal;
use App\Livewire\Traits\WithFlashMessage;
use App\Models\Administration\User\User;
use App\Models\Organization\OrganizationChart\OrganizationChart;
use App\Services\Organization\OrganizationChart\OrganizationChartService;
use App\Validation\Organization\OrganizationChart\OrganizationChartRules;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class OrganizationChartConfigPage extends Component
{
    use Modal, WithFlashMessage, WithPagination;

    protected OrganizationChartService $organizationChartService;

    /** Filters */
    public array $filters = [
        'acronym' => '',
        'filter' => '',
        'status' => 'all',
        'responsible_user_id' => 'all',
    ];

    /** Form */
    public ?int $chartId = null;

    public string $title = '';

    public string $acronym = '';

    public ?int $hierarchy = null;

    public ?int $responsible_user_id = null;

    public ?int $usersOrganizationId = null;

    public array $organizationUserIds = [];

    public string $userSearch = '';

    public function boot(OrganizationChartService $organizationChartService)
    {
        $this->organizationChartService = $organizationChartService;
    }

    /* CREATE */
    public function create(): void
    {
        $this->reset();
        $this->openModal('modal-form-create-organitation-chart');
    }

    public function store(): void
    {
        $data = $this->validate(OrganizationChartRules::store());

        if ($this->responsible_user_id) {
            $this->addError('responsible_user_id', 'Selecione um usuário associado ao setor.');

            return;
        }

        try {
            $this->organizationChartService->store($data);
        } catch (\RuntimeException $exception) {
            $this->addError('hierarchy', $exception->getMessage());

            return;
        }
        $this->reset();
        $this->flashSuccess('Setor adicionado no organograma com sucesso.');
        $this->closeModal();
    }

    /* EDIT */
    public function edit(int $id): void
    {
        $this->reset();

        $organizationChart = $this->organizationChartService->find($id);

        $this->chartId = $organizationChart->id;
        $this->title = $organizationChart->title;
        $this->acronym = $organizationChart->acronym;
        $this->hierarchy = $organizationChart->hierarchy;
        $this->responsible_user_id = $organizationChart->responsible_user_id;

        $this->openModal('modal-form-edit-organitation-chart');
    }

    public function update(): void
    {
        if (! $this->chartId) {
            return;
        }

        $data = $this->validate(OrganizationChartRules::update($this->chartId));

        try {
            $this->organizationChartService->update($this->chartId, $data);
        } catch (\RuntimeException $exception) {
            $this->addError('hierarchy', $exception->getMessage());

            return;
        }

        $this->reset();
        $this->flashSuccess('Setor alterado no organograma com sucesso.');
        $this->closeModal();
    }

    public function status(int $id): void
    {
        $this->organizationChartService->status($id);
        $this->flashSuccess('Setor foi atualizada com sucesso.');
    }

    public function openUsers(int $organizationId): void
    {
        $organizationChart = $this->organizationChartService->find($organizationId);

        $this->usersOrganizationId = $organizationChart->id;
        $this->organizationUserIds = $organizationChart->users()
            ->pluck('users.id')
            ->map(fn ($id): int => (int) $id)
            ->all();
        $this->userSearch = '';

        $this->openModal('modal-organization-users');
    }

    public function saveUsers(): void
    {
        $data = $this->validate([
            'usersOrganizationId' => ['required', 'exists:organization_charts,id'],
            'organizationUserIds' => ['array'],
            'organizationUserIds.*' => ['integer', 'exists:users,id'],
        ]);

        $this->organizationChartService->syncUsers(
            (int) $data['usersOrganizationId'],
            array_map('intval', $data['organizationUserIds'] ?? [])
        );

        $this->usersOrganizationId = null;
        $this->organizationUserIds = [];
        $this->userSearch = '';
        $this->flashSuccess('Usuários associados ao setor com sucesso.');
        $this->closeModal();
    }

    /* RENDER */
    public function render(): View
    {
        $organizationCharts = $this->organizationChartService->index($this->filters);
        $userQuery = User::query()->orderBy('name');

        if ($this->userSearch !== '') {
            $term = '%'.$this->userSearch.'%';
            $userQuery->where('name', 'ilike', $term)
                ->orWhere('email', 'ilike', $term);
        }

        return view('livewire.organization.organization-chart.organization-chart-config-page', [
            'organizationCharts' => $organizationCharts,
            'users' => $userQuery->get(),
            'responsibleFilterUsers' => User::orderBy('name')->get(),
            'responsibleUsers' => $this->getResponsibleUsers(),
        ])->layout('layouts.app');
    }

    protected function getResponsibleUsers(): Collection
    {
        if (! $this->chartId) {
            return collect();
        }

        $organizationChart = OrganizationChart::with('users')->find($this->chartId);

        return $organizationChart?->users ?? collect();
    }
}
