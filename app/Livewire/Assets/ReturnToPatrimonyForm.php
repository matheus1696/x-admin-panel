<?php

namespace App\Livewire\Assets;

use App\DTOs\Assets\ReturnToPatrimonyDTO;
use App\Livewire\Traits\Modal;
use App\Livewire\Traits\WithFlashMessage;
use App\Models\Assets\Asset;
use App\Services\Assets\AssetOperationService;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Component;

class ReturnToPatrimonyForm extends Component
{
    use Modal, WithFlashMessage;

    protected AssetOperationService $assetOperationService;

    public string $assetUuid;

    public int $assetId;

    public ?string $notes = null;

    public function boot(AssetOperationService $assetOperationService): void
    {
        $this->assetOperationService = $assetOperationService;
    }

    public function mount(string $assetUuid): void
    {
        $asset = Asset::query()->where('uuid', $assetUuid)->firstOrFail();

        Gate::authorize('returnToPatrimony', Asset::class);

        $this->assetUuid = $asset->uuid;
        $this->assetId = $asset->id;
    }

    public function open(): void
    {
        $this->notes = null;
        $this->openModal('return-to-patrimony');
    }

    public function save()
    {
        $data = $this->validate([
            'notes' => ['nullable', 'string'],
        ]);

        $this->assetOperationService->returnToPatrimony(new ReturnToPatrimonyDTO(
            assetId: $this->assetId,
            actorUserId: auth()->id(),
            notes: $data['notes'],
        ));

        $this->flashSuccess('Ativo retornado ao patrimonio com sucesso.');

        return redirect()->route('assets.show', $this->assetUuid);
    }

    public function render(): View
    {
        return view('livewire.assets.return-to-patrimony-form');
    }
}
