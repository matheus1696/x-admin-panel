<?php

namespace App\Livewire\Assets;

use App\DTOs\Assets\TransferAssetDTO;
use App\Livewire\Traits\Modal;
use App\Livewire\Traits\WithFlashMessage;
use App\Models\Assets\Asset;
use App\Models\Configuration\Establishment\Establishment\Department;
use App\Models\Configuration\Establishment\Establishment\Establishment;
use App\Services\Assets\AssetOperationService;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Component;

class TransferAssetForm extends Component
{
    use Modal, WithFlashMessage;

    protected AssetOperationService $assetOperationService;

    public string $assetUuid;

    public int $assetId;

    public bool $iconOnly = false;

    public ?int $unitId = null;

    public ?int $sectorId = null;

    public ?string $notes = null;

    public function boot(AssetOperationService $assetOperationService): void
    {
        $this->assetOperationService = $assetOperationService;
    }

    public function mount(string $assetUuid, bool $iconOnly = false): void
    {
        $asset = Asset::query()->where('uuid', $assetUuid)->firstOrFail();

        Gate::authorize('transfer', Asset::class);

        $this->assetUuid = $asset->uuid;
        $this->assetId = $asset->id;
        $this->iconOnly = $iconOnly;
    }

    public function open(): void
    {
        $asset = Asset::query()->findOrFail($this->assetId);

        $this->unitId = $asset->unit_id;
        $this->sectorId = $asset->sector_id;
        $this->notes = null;

        $this->openModal('transfer-asset');
    }

    public function save()
    {
        $data = $this->validate([
            'unitId' => ['required', 'exists:establishments,id'],
            'sectorId' => ['nullable', 'exists:departments,id'],
            'notes' => ['nullable', 'string'],
        ]);

        $this->assetOperationService->transferAsset(new TransferAssetDTO(
            assetId: $this->assetId,
            unitId: (int) $data['unitId'],
            sectorId: $data['sectorId'] ? (int) $data['sectorId'] : null,
            actorUserId: auth()->id(),
            notes: $data['notes'],
        ));

        $this->flashSuccess('Ativo transferido com sucesso.');

        return redirect()->route('assets.show', $this->assetUuid);
    }

    public function render(): View
    {
        return view('livewire.assets.transfer-asset-form', [
            'units' => Establishment::query()->orderBy('title')->get(),
            'sectors' => Department::query()->orderBy('title')->get(),
        ]);
    }
}
