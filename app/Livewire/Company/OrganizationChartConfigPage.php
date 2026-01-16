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

    /** LISTAGEM */
    public $organizationCharts = [];

    /** FORM */
    public ?int $chartId = null;
    public string $name = '';
    public ?string $acronym = null;
    public $hierarchy = null;

    /* LIFECYCLE */
    public function mount()
    {
        $this->loadOrganizationCharts();
    }

    /* DATA */
    public function loadOrganizationCharts(): void
    {
        $this->organizationCharts = OrganizationChart::orderBy('order')->get();
    }

    public function resetForm(): void
    {
        $this->reset(['chartId', 'name', 'acronym', 'hierarchy']);
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

        $this->afterSave('Setor adicionado no organograma com sucesso.');
    }
    
    /* EDIT */
    public function edit(int $id): void
    {
        $organizationChart = OrganizationChart::findOrFail($id);

        $this->chartId   = $organizationChart->id;
        $this->name      = $organizationChart->name;
        $this->acronym   = $organizationChart->acronym;
        $this->hierarchy = $organizationChart->hierarchy;

        $this->openModal('modal-form-edit-organitation-chart');
    }

    public function update(OrganizationChartService $organizationChartService): void
    {
        $data = $this->validate($this->rules());

        $organizationChartService->update($this->chartId, $data);

        $this->afterSave('Setor alterado no organograma com sucesso.');
    }

    /* HELPERS */
    protected function afterSave(string $message): void
    {
        $this->loadOrganizationCharts();
        $this->resetForm();
        $this->flashSuccess($message);
        $this->closeModal();
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string',
            'acronym' => 'nullable|string|max:20',
            'hierarchy' => 'required',
        ];
    }

    /* RENDER */
    public function render()
    {
        return view('livewire.company.organization-chart-config-page')->layout('layouts.app');
    }
}
