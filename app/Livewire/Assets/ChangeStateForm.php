<?php

namespace App\Livewire\Assets;

use App\DTOs\Assets\ChangeAssetStateDTO;
use App\Enums\Assets\AssetState;
use App\Livewire\Traits\Modal;
use App\Livewire\Traits\WithFlashMessage;
use App\Models\Assets\Asset;
use App\Services\Assets\AssetOperationService;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Component;

class ChangeStateForm extends Component
{
    use Modal, WithFlashMessage;

    protected AssetOperationService $assetOperationService;

    public string $assetUuid;

    public int $assetId;

    public bool $iconOnly = false;

    public ?string $toState = null;

    public ?string $notes = null;

    public function boot(AssetOperationService $assetOperationService): void
    {
        $this->assetOperationService = $assetOperationService;
    }

    public function mount(string $assetUuid, bool $iconOnly = false): void
    {
        $asset = Asset::query()->where('uuid', $assetUuid)->firstOrFail();

        Gate::authorize('changeState', Asset::class);

        $this->assetUuid = $asset->uuid;
        $this->assetId = $asset->id;
        $this->iconOnly = $iconOnly;
    }

    public function open(): void
    {
        $this->toState = null;
        $this->notes = null;
        $this->openModal('change-state');
    }

    public function save()
    {
        $data = $this->validate([
            'toState' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $this->assetOperationService->changeAssetState(new ChangeAssetStateDTO(
            assetId: $this->assetId,
            toState: AssetState::from($data['toState']),
            actorUserId: auth()->id(),
            notes: $data['notes'],
        ));

        $this->flashSuccess('Estado do ativo atualizado com sucesso.');

        return redirect()->route('assets.show', $this->assetUuid);
    }

    public function render(): View
    {
        $asset = Asset::query()->findOrFail($this->assetId);

        return view('livewire.assets.change-state-form', [
            'availableStates' => $this->availableStates($asset->state),
        ]);
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    private function availableStates(AssetState $currentState): array
    {
        $map = [
            AssetState::IN_STOCK->value => [AssetState::IN_USE, AssetState::MAINTENANCE, AssetState::DAMAGED],
            AssetState::IN_USE->value => [AssetState::IN_STOCK, AssetState::MAINTENANCE, AssetState::DAMAGED],
            AssetState::MAINTENANCE->value => [AssetState::IN_STOCK, AssetState::IN_USE, AssetState::DAMAGED],
            AssetState::DAMAGED->value => [],
        ];

        return collect($map[$currentState->value] ?? [])
            ->map(fn (AssetState $state): array => [
                'value' => $state->value,
                'label' => match ($state) {
                    AssetState::IN_STOCK => 'Em estoque',
                    AssetState::IN_USE => 'Em uso',
                    AssetState::MAINTENANCE => 'Em manutencao',
                    AssetState::DAMAGED => 'Inservivel',
                },
            ])
            ->values()
            ->all();
    }
}
