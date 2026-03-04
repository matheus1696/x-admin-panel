<?php

namespace App\Livewire\Assets;

use App\DTOs\Assets\ReleaseAssetDTO;
use App\Livewire\Traits\Modal;
use App\Livewire\Traits\WithFlashMessage;
use App\Models\Assets\Asset;
use App\Models\Configuration\Establishment\Establishment\Department;
use App\Models\Configuration\Establishment\Establishment\Establishment;
use App\Services\Assets\AssetOperationService;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Component;

class ReleaseAssetForm extends Component
{
    use Modal, WithFlashMessage;

    protected AssetOperationService $assetOperationService;

    public string $assetUuid;

    public int $assetId;

    public ?int $unitId = null;

    public ?int $sectorId = null;

    public ?string $notes = null;

    public function boot(AssetOperationService $assetOperationService): void
    {
        $this->assetOperationService = $assetOperationService;
    }

    public function mount(string $assetUuid): void
    {
        $asset = Asset::query()->where('uuid', $assetUuid)->firstOrFail();

        Gate::authorize('release', Asset::class);

        $this->assetUuid = $asset->uuid;
        $this->assetId = $asset->id;
    }

    public function open(): void
    {
        $asset = Asset::query()->findOrFail($this->assetId);

        $this->unitId = $asset->unit_id;
        $this->sectorId = $asset->sector_id;
        $this->notes = null;

        $this->openModal('release-asset');
    }

    public function save()
    {
        $data = $this->validate([
            'unitId' => ['required', 'exists:establishments,id'],
            'sectorId' => ['nullable', 'exists:departments,id'],
            'notes' => ['nullable', 'string'],
        ]);

        $this->assetOperationService->releaseAsset(new ReleaseAssetDTO(
            assetId: $this->assetId,
            unitId: (int) $data['unitId'],
            sectorId: $data['sectorId'] ? (int) $data['sectorId'] : null,
            actorUserId: auth()->id(),
            notes: $data['notes'],
        ));

        $this->flashSuccess(__('assets.operations.release.messages.success'));

        return redirect()->route('assets.show', $this->assetUuid);
    }

    public function render(): View
    {
        return view('livewire.assets.release-asset-form', [
            'units' => Establishment::query()->orderBy('title')->get(),
            'sectors' => Department::query()->orderBy('title')->get(),
        ]);
    }
}
