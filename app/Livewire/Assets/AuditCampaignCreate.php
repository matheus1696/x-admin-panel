<?php

namespace App\Livewire\Assets;

use App\DTOs\Assets\CreateAuditCampaignDTO;
use App\Models\Assets\Asset;
use App\Models\Configuration\Establishment\Establishment\Department;
use App\Models\Configuration\Establishment\Establishment\Establishment;
use App\Models\Configuration\FinancialBlock\FinancialBlock;
use App\Services\Assets\AuditCampaignService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Component;

class AuditCampaignCreate extends Component
{
    protected AuditCampaignService $auditCampaignService;

    public string $title = '';

    public ?int $unitId = null;

    public ?int $sectorId = null;

    public ?int $financialBlockId = null;

    public ?string $startDate = null;

    public ?string $dueDate = null;

    public function boot(AuditCampaignService $auditCampaignService): void
    {
        $this->auditCampaignService = $auditCampaignService;
    }

    public function mount(): void
    {
        Gate::authorize('audit', Asset::class);
    }

    public function updatedUnitId(): void
    {
        $this->sectorId = null;
    }

    public function save(): mixed
    {
        $data = $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'unitId' => ['nullable', 'integer', 'exists:establishments,id'],
            'sectorId' => [
                'nullable',
                'integer',
                Rule::exists('departments', 'id')->where(fn ($query) => $query->where('establishment_id', (int) $this->unitId)),
            ],
            'financialBlockId' => ['nullable', 'integer', 'exists:financial_blocks,id'],
            'startDate' => ['nullable', 'date'],
            'dueDate' => ['nullable', 'date', 'after_or_equal:startDate'],
        ]);

        $campaign = $this->auditCampaignService->createCampaign(new CreateAuditCampaignDTO(
            title: $data['title'],
            unitId: isset($data['unitId']) ? (int) $data['unitId'] : null,
            sectorId: isset($data['sectorId']) ? (int) $data['sectorId'] : null,
            financialBlockId: isset($data['financialBlockId']) ? (int) $data['financialBlockId'] : null,
            startDate: $data['startDate'] ?? null,
            dueDate: $data['dueDate'] ?? null,
            createdUserId: auth()->id(),
        ));

        session()->flash('success', 'Campanha de auditoria criada com sucesso.');

        return redirect()->route('assets.audits.campaigns.show', $campaign->uuid);
    }

    public function render(): View
    {
        return view('livewire.assets.audit-campaign-create', [
            'units' => Establishment::query()->orderBy('title')->get(),
            'sectors' => $this->unitId
                ? Department::query()->where('establishment_id', $this->unitId)->orderBy('title')->get()
                : collect(),
            'financialBlocks' => FinancialBlock::query()->where('is_active', true)->orderBy('title')->get(),
        ])->layout('layouts.app');
    }
}

