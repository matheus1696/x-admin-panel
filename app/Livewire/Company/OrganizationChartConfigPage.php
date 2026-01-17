<?php

namespace App\Livewire\Company;

use App\Livewire\Traits\Modal;
use App\Livewire\Traits\WithFlashMessage;
use App\Models\Company\OrganizationChart;
use App\Services\Company\OrganizationChartService;
use Livewire\Component;

class OrganizationChartConfigPage extends Component
{
    use WithFlashMessage, Modal;

    public array $filters = [
        'acronym' => '',
        'title' => '',
        'status' => 'all',
        'perPage' => 25,
    ];

    /** FORM */
    public ?int $chartId = null;
    public string $title = '';
    public ?string $acronym = null;
    public $hierarchy = null;

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

    public function store(OrganizationChartService $organizationChartService): void
    {
        $data = $this->validate($this->rules());
        $organizationChartService->create($data);
        $this->resetForm();
        $this->flashSuccess('Setor adicionado no organograma com sucesso.');
        $this->closeModal();
    }
    
    /* EDIT */
    public function edit(int $id): void
    {
        $organizationChart = OrganizationChart::findOrFail($id);

        $this->chartId   = $organizationChart->id;
        $this->title      = $organizationChart->title;
        $this->acronym   = $organizationChart->acronym;
        $this->hierarchy = $organizationChart->hierarchy;

        $this->openModal('modal-form-edit-organitation-chart');
    }

    public function update(OrganizationChartService $organizationChartService): void
    {
        $data = $this->validate($this->rules());
        $organizationChartService->update($this->chartId, $data);
        $this->resetForm();
        $this->flashSuccess('Setor alterado no organograma com sucesso.');
        $this->closeModal();
    }

    public function status(OrganizationChartService $organizationChartService, OrganizationChart $organizationChart): void
    {
        $organizationChartService->status($organizationChart->id);
        $this->flashSuccess('Setor foi atualizada com sucesso.');
    }

    protected function rules(): array
    {
        return [
            'title' => 'required|string',
            'acronym' => 'nullable|string|max:20',
            'hierarchy' => 'required',
        ];
    }

    /* RENDER */
    public function render()
    {
        
        $query = OrganizationChart::query();

        if ($this->filters['acronym']) {
            $query->where('acronym', 'like', '%' . strtoupper($this->filters['acronym']) . '%');
        }

        if ($this->filters['title']) {
            $query->where('filter', 'like', '%' . strtolower($this->filters['title']) . '%');
        }

        if ($this->filters['status'] !== 'all') {
            $query->where('status', $this->filters['status']);
        }

        $organizationCharts = $query->orderBy('order')->paginate($this->filters['perPage']);

        return view('livewire.company.organization-chart-config-page', [
            'organizationCharts' => $organizationCharts
        ])->layout('layouts.app');
    }
}
