<?php

namespace App\Livewire\Organization\OrganizationChart;

use App\Livewire\Traits\Modal;
use App\Livewire\Traits\WithFlashMessage;
use App\Services\Organization\OrganizationChart\OrganizationChartService;
use App\Validation\Organization\OrganizationChart\OrganizationChartRules;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class OrganizationChartConfigPage extends Component
{
    use WithPagination, WithFlashMessage, Modal;

    protected OrganizationChartService $organizationChartService;

    /** Filters */
    public array $filters = [
        'acronym' => '',
        'filter' => '',
        'status' => 'all',
        'perPage' => 25,
    ];

    /** Form */
    public ?int $chartId = null;
    public string $title = '';
    public string $acronym = '';
    public ?int $hierarchy = null;    

    public function boot(OrganizationChartService $organizationChartService)
    {
        $this->organizationChartService = $organizationChartService;
    }

    public function resetForm(): void
    {
        $this->reset(['chartId', 'title', 'acronym', 'hierarchy']);
    }

    /* CREATE */
    public function create(): void
    {
        $this->resetForm();
        $this->openModal('modal-form-create-organitation-chart');
    }

    public function store(): void
    {
        $data = $this->validate(OrganizationChartRules::store());
        $this->organizationChartService->store($data);
        $this->resetForm();
        $this->flashSuccess('Setor adicionado no organograma com sucesso.');
        $this->closeModal();
    }
    
    /* EDIT */
    public function edit(int $id): void
    {
        $organizationChart = $this->organizationChartService->find($id);

        $this->chartId   = $organizationChart->id;
        $this->title      = $organizationChart->title;
        $this->acronym   = $organizationChart->acronym;
        $this->hierarchy = $organizationChart->hierarchy;

        $this->openModal('modal-form-edit-organitation-chart');
    }

    public function update(): void
    {
        if (!$this->chartId) { return; }
        $data = $this->validate(OrganizationChartRules::update($this->chartId));
        $this->organizationChartService->update($this->chartId, $data);
        $this->resetForm();
        $this->flashSuccess('Setor alterado no organograma com sucesso.');
        $this->closeModal();
    }

    public function status(int $id): void
    {
        $this->organizationChartService->status($id);
        $this->flashSuccess('Setor foi atualizada com sucesso.');
    }

    /* RENDER */
    public function render(): View
    {
        
        $organizationCharts = $this->organizationChartService->index($this->filters);

        return view('livewire.organization.organization-chart.organization-chart-config-page', [
            'organizationCharts' => $organizationCharts
        ])->layout('layouts.app');
    }
}
