<?php

namespace App\Livewire\Assets;

use App\DTOs\Assets\RegisterAuditCampaignItemDTO;
use App\Livewire\Traits\Modal;
use App\Livewire\Traits\WithFlashMessage;
use App\Models\Assets\Asset;
use App\Models\Assets\AssetAuditCampaign;
use App\Models\Assets\AssetAuditCampaignItem;
use App\Services\Assets\AuditCampaignService;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class AuditCampaignShow extends Component
{
    use Modal, WithFileUploads, WithFlashMessage, WithPagination;

    protected AuditCampaignService $auditCampaignService;

    public string $uuid;

    public array $filters = [
        'status' => 'all',
        'search' => '',
        'perPage' => 15,
    ];

    public ?int $campaignItemId = null;

    public string $auditStatus = 'FOUND';

    public ?string $auditNotes = null;

    public ?string $observedUnit = null;

    public ?string $observedSector = null;

    public $photo = null;

    public function boot(AuditCampaignService $auditCampaignService): void
    {
        $this->auditCampaignService = $auditCampaignService;
    }

    public function mount(string $uuid): void
    {
        Gate::authorize('audit', Asset::class);
        $this->uuid = $uuid;
    }

    public function updatedFilters(): void
    {
        $this->resetPage();
    }

    public function openAuditItem(int $campaignItemId): void
    {
        $this->campaignItemId = $campaignItemId;
        $this->auditStatus = 'FOUND';
        $this->auditNotes = null;
        $this->observedUnit = null;
        $this->observedSector = null;
        $this->photo = null;

        $this->openModal('audit-item');
    }

    public function saveAuditItem(): void
    {
        $data = $this->validate([
            'campaignItemId' => ['required', 'integer', 'exists:asset_audit_campaign_items,id'],
            'auditStatus' => ['required', 'string', 'in:FOUND,NOT_FOUND,DIVERGENCE,DAMAGED,NO_TAG'],
            'auditNotes' => ['nullable', 'string'],
            'observedUnit' => ['nullable', 'string', 'max:255'],
            'observedSector' => ['nullable', 'string', 'max:255'],
            'photo' => ['nullable', 'image', 'max:5120'],
        ]);

        $photoPath = $this->photo ? $this->auditCampaignService->storeAuditPhoto($this->photo) : null;

        $this->auditCampaignService->registerItemAudit(new RegisterAuditCampaignItemDTO(
            campaignItemId: (int) $data['campaignItemId'],
            status: $data['auditStatus'],
            photoPath: $photoPath,
            notes: $data['auditNotes'] ?? null,
            observedUnit: $data['observedUnit'] ?? null,
            observedSector: $data['observedSector'] ?? null,
            actorUserId: auth()->id(),
        ));

        $this->closeModal();
        $this->flashSuccess('Item auditado com sucesso.');
    }

    public function finalizeCampaign(): void
    {
        $campaign = AssetAuditCampaign::query()->where('uuid', $this->uuid)->firstOrFail();
        $this->auditCampaignService->finalizeCampaign($campaign->id, auth()->id());
        $this->flashSuccess('Campanha finalizada com sucesso.');
    }

    public function render(): View
    {
        $campaign = AssetAuditCampaign::query()
            ->with(['unit', 'sector', 'financialBlock'])
            ->where('uuid', $this->uuid)
            ->firstOrFail();

        $itemsQuery = AssetAuditCampaignItem::query()
            ->with(['asset.unit', 'asset.sector'])
            ->where('asset_audit_campaign_id', $campaign->id)
            ->when(
                $this->filters['status'] !== 'all',
                fn ($query) => $query->where('status', $this->filters['status'])
            )
            ->when($this->filters['search'], function ($query): void {
                $search = trim($this->filters['search']);
                $query->whereHas('asset', function ($assetQuery) use ($search): void {
                    $assetQuery->where('code', 'like', '%'.$search.'%')
                        ->orWhere('description', 'like', '%'.$search.'%')
                        ->orWhere('patrimony_number', 'like', '%'.$search.'%');
                });
            });

        $items = $itemsQuery->orderBy('id')->paginate((int) $this->filters['perPage']);

        $total = AssetAuditCampaignItem::query()->where('asset_audit_campaign_id', $campaign->id)->count();
        $done = AssetAuditCampaignItem::query()->where('asset_audit_campaign_id', $campaign->id)->where('status', '!=', 'PENDING')->count();
        $pending = max(0, $total - $done);
        $openIssues = $campaign->issues()->where('status', 'OPEN')->count();
        $conformity = $total > 0 ? (int) round(($done - $openIssues) * 100 / $total) : 0;

        $openIssuesList = $campaign->issues()
            ->where('status', 'OPEN')
            ->with('asset')
            ->latest('id')
            ->limit(20)
            ->get();

        return view('livewire.assets.audit-campaign-show', [
            'campaign' => $campaign,
            'items' => $items,
            'openIssuesList' => $openIssuesList,
            'metrics' => [
                'total' => $total,
                'done' => $done,
                'pending' => $pending,
                'openIssues' => $openIssues,
                'conformity' => max(0, min(100, $conformity)),
            ],
        ])->layout('layouts.app');
    }
}

