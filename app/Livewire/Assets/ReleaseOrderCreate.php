<?php

namespace App\Livewire\Assets;

use App\DTOs\Assets\CreateReleaseOrderDTO;
use App\Enums\Assets\AssetState;
use App\Exceptions\Assets\AssetsValidationException;
use App\Livewire\Traits\Modal;
use App\Livewire\Traits\WithFlashMessage;
use App\Models\Assets\Asset;
use App\Models\Configuration\FinancialBlock\FinancialBlock;
use App\Models\Configuration\Establishment\Establishment\Department;
use App\Models\Configuration\Establishment\Establishment\Establishment;
use App\Services\Assets\ReleaseOrderService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class ReleaseOrderCreate extends Component
{
    use Modal, WithFlashMessage, WithPagination;

    protected ReleaseOrderService $releaseOrderService;

    public array $filters = [
        'search' => '',
        'financialBlockId' => 'all',
        'perPage' => 5,
    ];

    /** @var array<int> */
    public array $selectedAssetIds = [];

    public ?int $unitId = null;

    public ?int $sectorId = null;

    public string $requesterName = '';

    public ?string $receiverName = null;

    public ?string $notes = null;

    public function boot(ReleaseOrderService $releaseOrderService): void
    {
        $this->releaseOrderService = $releaseOrderService;
    }

    public function mount(): void
    {
        Gate::authorize('transfer', Asset::class);
    }

    public function updatedFilters(): void
    {
        $this->resetPage();
    }

    public function updatedUnitId($value): void
    {
        $this->sectorId = null;

        if (! $value) {
            return;
        }
    }

    public function clearFilters(): void
    {
        $this->filters = [
            'search' => '',
            'financialBlockId' => 'all',
            'perPage' => 5,
        ];

        $this->resetPage();
    }

    public function openAddItemModal(): void
    {
        $this->openModal('add-item');
    }

    public function addAssetToRelease(int $assetId): void
    {
        if (! in_array($assetId, $this->selectedAssetIds, true)) {
            $this->selectedAssetIds[] = $assetId;
            $this->selectedAssetIds = array_values(array_unique(array_map('intval', $this->selectedAssetIds)));
        }
    }

    public function removeSelectedAsset(int $assetId): void
    {
        $this->selectedAssetIds = array_values(array_filter(
            $this->selectedAssetIds,
            fn ($selectedId) => (int) $selectedId !== $assetId
        ));
    }

    public function createReleaseOrder(): mixed
    {
        Gate::authorize('transfer', Asset::class);

        $data = $this->validate([
            'selectedAssetIds' => ['required', 'array', 'min:1'],
            'selectedAssetIds.*' => ['required', 'integer', 'exists:assets,id'],
            'unitId' => ['required', 'integer', 'exists:establishments,id'],
            'sectorId' => [
                'nullable',
                'integer',
                Rule::exists('departments', 'id')->where(
                    fn ($query) => $query->where('establishment_id', (int) $this->unitId)
                ),
            ],
            'requesterName' => ['required', 'string', 'max:255'],
            'receiverName' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        try {
            $order = $this->releaseOrderService->createAndRelease(new CreateReleaseOrderDTO(
                assetIds: $data['selectedAssetIds'],
                unitId: (int) $data['unitId'],
                sectorId: isset($data['sectorId']) ? (int) $data['sectorId'] : null,
                requesterName: $data['requesterName'],
                receiverName: $data['receiverName'] ?? null,
                notes: $data['notes'] ?? null,
                actorUserId: auth()->id(),
            ));
        } catch (AssetsValidationException $exception) {
            $this->flashWarning($exception->getMessage());

            return null;
        }

        $this->flashSuccess('Pedido de liberacao criado e executado com sucesso.');

        return redirect()->route('assets.release-orders.show', $order->uuid);
    }

    public function render(): View
    {
        $assets = Asset::query()
            ->with(['invoiceItem.invoice.financialBlock'])
            ->where('state', AssetState::IN_STOCK)
            ->when($this->filters['search'], function ($query): void {
                $search = trim($this->filters['search']);

                $query->where(function ($innerQuery) use ($search): void {
                    $innerQuery->where('assets.code', 'like', '%'.$search.'%')
                        ->orWhere('assets.description', 'like', '%'.$search.'%')
                        ->orWhere('assets.patrimony_number', 'like', '%'.$search.'%')
                        ->orWhereHas('invoiceItem', fn ($invoiceItemQuery) => $invoiceItemQuery->where('description', 'like', '%'.$search.'%'))
                        ->orWhereHas('invoiceItem.invoice', fn ($invoiceQuery) => $invoiceQuery->where('invoice_number', 'like', '%'.$search.'%'));
                });
            })
            ->when(
                $this->filters['financialBlockId'] !== 'all',
                fn ($query) => $query->whereHas('invoiceItem.invoice', fn ($invoiceQuery) => $invoiceQuery->where('financial_block_id', (int) $this->filters['financialBlockId']))
            )
            ->orderByDesc('assets.id')
            ->paginate(5);

        $selectedAssets = empty($this->selectedAssetIds)
            ? collect()
            : Asset::query()
                ->with(['invoiceItem.invoice.financialBlock'])
                ->whereIn('id', $this->selectedAssetIds)
                ->orderByDesc('id')
                ->get();

        return view('livewire.assets.release-order-create', [
            'assets' => $assets,
            'selectedAssets' => $selectedAssets,
            'units' => Establishment::query()->orderBy('title')->get(),
            'sectors' => $this->unitId
                ? Department::query()
                    ->where('establishment_id', (int) $this->unitId)
                    ->orderBy('title')
                    ->get()
                : collect(),
            'financialBlocks' => FinancialBlock::query()->where('is_active', true)->orderBy('title')->get(),
        ])->layout('layouts.app');
    }
}
