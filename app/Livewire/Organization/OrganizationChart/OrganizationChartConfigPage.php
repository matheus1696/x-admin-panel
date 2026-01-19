<?php

namespace App\Livewire\Organization\OrganizationChart;

use App\Livewire\Traits\Modal;
use App\Livewire\Traits\WithFlashMessage;
use App\Services\Organization\OrganizationChart\OrganizationChartService;
use App\Validation\Organization\OrganizationChart\OrganizationChartRules;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class OrganizationChartConfigPage extends Component
{
    use WithFileUploads, WithPagination, WithFlashMessage, Modal;

    protected OrganizationChartService $organizationChartService;

    /** Filters */
    public array $filters = [
        'acronym' => '',
        'filter' => '',
        'status' => 'all',
        'perPage' => 50,
    ];

    /** Form */
    public ?int $chartId = null;
    public string $title = '';
    public string $acronym = '';
    public ?int $hierarchy = null;    
    public $responsible_photo = null;
    public ?string $responsible_name = '';
    public ?string $responsible_contact = '';
    public ?string $responsible_email = '';
    public $temporaryPhoto = false;

    public function boot(OrganizationChartService $organizationChartService)
    {
        $this->organizationChartService = $organizationChartService;
    }

    public function updatedFilters()
    {
        $this->resetPage();
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
        // Upload da foto
        if ($this->responsible_photo) {
            $data['responsible_photo'] = $this->responsible_photo->store('organizationChart', 'public');
        }        
        $this->organizationChartService->store($data);
        $this->resetForm();
        $this->flashSuccess('Setor adicionado no organograma com sucesso.');
        $this->closeModal();
    }
    
    /* EDIT */
    public function edit(int $id): void
    {
        $organizationChart = $this->organizationChartService->find($id);

        $this->chartId             = $organizationChart->id;
        $this->title               = $organizationChart->title;
        $this->acronym             = $organizationChart->acronym;
        $this->hierarchy           = $organizationChart->hierarchy;
        $this->responsible_name    = $organizationChart->responsible_name;
        $this->responsible_contact = $organizationChart->responsible_contact;
        $this->responsible_email   = $organizationChart->responsible_email;
        $this->responsible_photo   = $organizationChart->responsible_photo;

        $this->openModal('modal-form-edit-organitation-chart');
    }

    public function update(): void
    {
        if (!$this->chartId) return;

        $data = $this->validate(OrganizationChartRules::update($this->chartId));

        // Upload da nova foto, se houver
        if ($this->responsible_photo instanceof TemporaryUploadedFile) {

            // Buscar a foto antiga
            $organizationChart = $this->organizationChartService->find($this->chartId);
            if ($organizationChart->responsible_photo) {
                Storage::disk('public')->delete($organizationChart->responsible_photo);
            }

            // Salvar a nova foto
            $data['responsible_photo'] = $this->responsible_photo->store('organizationChart', 'public');

            
            // 3️⃣ Deletar o arquivo temporário do Livewire
            if (file_exists($this->responsible_photo->getRealPath())) {
                unlink($this->responsible_photo->getRealPath());
            }
        }

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
