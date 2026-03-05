<?php

namespace App\Services\Assets;

use App\DTOs\Assets\ChangeAssetStateDTO;
use App\DTOs\Assets\ReleaseAssetDTO;
use App\DTOs\Assets\ReturnToPatrimonyDTO;
use App\DTOs\Assets\TransferAssetDTO;
use App\Enums\Assets\AssetEventType;
use App\Enums\Assets\AssetState;
use App\Models\Assets\Asset;
use App\Models\Assets\AssetEvent;
use App\Validation\Assets\CanChangeStateValidator;
use App\Validation\Assets\CanReleaseAssetValidator;
use App\Validation\Assets\CanReturnToPatrimonyValidator;
use App\Validation\Assets\CanTransferAssetValidator;
use Illuminate\Support\Facades\DB;

class AssetOperationService
{
    public function __construct(
        private readonly CanReleaseAssetValidator $canReleaseAssetValidator,
        private readonly CanTransferAssetValidator $canTransferAssetValidator,
        private readonly CanChangeStateValidator $canChangeStateValidator,
        private readonly CanReturnToPatrimonyValidator $canReturnToPatrimonyValidator,
    ) {}

    public function releaseAsset(ReleaseAssetDTO $dto): void
    {
        DB::transaction(function () use ($dto): void {
            $asset = Asset::query()
                ->lockForUpdate()
                ->findOrFail($dto->assetId);

            if ($this->isIdempotentRelease($asset, $dto)) {
                return;
            }

            $fromState = $asset->state;
            $fromUnitId = $asset->unit_id;
            $fromSectorId = $asset->sector_id;

            $this->canReleaseAssetValidator->validateOrFail($asset, $dto);

            $asset->update([
                'state' => AssetState::IN_USE,
                'code' => $dto->patrimonyNumber,
                'patrimony_number' => $dto->patrimonyNumber,
                'unit_id' => $dto->unitId,
                'sector_id' => $dto->sectorId,
            ]);

            $this->recordEvent(
                $asset,
                AssetEventType::RELEASED,
                $fromState?->value,
                AssetState::IN_USE->value,
                $fromUnitId,
                $dto->unitId,
                $fromSectorId,
                $dto->sectorId,
                $dto->actorUserId,
                $dto->notes,
                [
                    'context' => ['service' => self::class, 'operation' => 'release'],
                    'patrimony_number' => $dto->patrimonyNumber,
                    'code_replaced' => true,
                ]
            );
        });
    }

    public function transferAsset(TransferAssetDTO $dto): void
    {
        DB::transaction(function () use ($dto): void {
            $asset = Asset::query()
                ->lockForUpdate()
                ->findOrFail($dto->assetId);

            if ($this->isIdempotentTransfer($asset, $dto)) {
                return;
            }

            $fromUnitId = $asset->unit_id;
            $fromSectorId = $asset->sector_id;

            $this->canTransferAssetValidator->validateOrFail($asset, $dto);

            $asset->update([
                'unit_id' => $dto->unitId,
                'sector_id' => $dto->sectorId,
            ]);

            $this->recordEvent(
                $asset,
                AssetEventType::TRANSFERRED,
                $asset->state?->value,
                $asset->state?->value,
                $fromUnitId,
                $dto->unitId,
                $fromSectorId,
                $dto->sectorId,
                $dto->actorUserId,
                $dto->notes,
                ['context' => ['service' => self::class, 'operation' => 'transfer']]
            );
        });
    }

    public function changeAssetState(ChangeAssetStateDTO $dto): void
    {
        DB::transaction(function () use ($dto): void {
            $asset = Asset::query()
                ->lockForUpdate()
                ->findOrFail($dto->assetId);

            $fromState = $asset->state;

            $this->canChangeStateValidator->validateOrFail($asset, $dto);

            $asset->update([
                'state' => $dto->toState,
            ]);

            $this->recordEvent(
                $asset,
                AssetEventType::STATE_CHANGED,
                $fromState?->value,
                $dto->toState->value,
                $asset->unit_id,
                $asset->unit_id,
                $asset->sector_id,
                $asset->sector_id,
                $dto->actorUserId,
                $dto->notes,
                ['context' => ['service' => self::class, 'operation' => 'change_state']]
            );
        });
    }

    public function returnToPatrimony(ReturnToPatrimonyDTO $dto): void
    {
        DB::transaction(function () use ($dto): void {
            $asset = Asset::query()
                ->lockForUpdate()
                ->findOrFail($dto->assetId);

            if ($this->isIdempotentReturn($asset, $dto)) {
                return;
            }

            $fromState = $asset->state;
            $fromUnitId = $asset->unit_id;
            $fromSectorId = $asset->sector_id;

            $this->canReturnToPatrimonyValidator->validateOrFail($asset, $dto);

            $asset->update([
                'state' => AssetState::IN_STOCK,
                'unit_id' => config('assets.patrimony_unit_id'),
                'sector_id' => null,
            ]);

            $this->recordEvent(
                $asset,
                AssetEventType::RETURNED_TO_PATRIMONY,
                $fromState?->value,
                AssetState::IN_STOCK->value,
                $fromUnitId,
                $asset->unit_id,
                $fromSectorId,
                null,
                $dto->actorUserId,
                $dto->notes,
                ['context' => ['service' => self::class, 'operation' => 'return_to_patrimony']]
            );
        });
    }

    private function recordEvent(
        Asset $asset,
        AssetEventType $type,
        ?string $fromState,
        ?string $toState,
        ?int $fromUnitId,
        ?int $toUnitId,
        ?int $fromSectorId,
        ?int $toSectorId,
        ?int $actorUserId,
        ?string $notes,
        ?array $payload = null,
    ): void {
        AssetEvent::create([
            'asset_id' => $asset->id,
            'type' => $type,
            'from_state' => $fromState,
            'to_state' => $toState,
            'from_unit_id' => $fromUnitId,
            'to_unit_id' => $toUnitId,
            'from_sector_id' => $fromSectorId,
            'to_sector_id' => $toSectorId,
            'actor_user_id' => $actorUserId,
            'notes' => $notes,
            'payload' => $payload,
        ]);
    }

    private function isIdempotentRelease(Asset $asset, ReleaseAssetDTO $dto): bool
    {
        $lastEvent = $asset->events()->latest('id')->first();

        return $asset->state === AssetState::IN_USE
            && (string) ($asset->patrimony_number ?? '') === $dto->patrimonyNumber
            && (string) ($asset->code ?? '') === $dto->patrimonyNumber
            && (int) $asset->unit_id === $dto->unitId
            && (int) ($asset->sector_id ?? 0) === (int) ($dto->sectorId ?? 0)
            && $lastEvent?->type === AssetEventType::RELEASED;
    }

    private function isIdempotentTransfer(Asset $asset, TransferAssetDTO $dto): bool
    {
        $lastEvent = $asset->events()->latest('id')->first();

        return (int) $asset->unit_id === $dto->unitId
            && (int) ($asset->sector_id ?? 0) === (int) ($dto->sectorId ?? 0)
            && $lastEvent?->type === AssetEventType::TRANSFERRED;
    }

    private function isIdempotentReturn(Asset $asset, ReturnToPatrimonyDTO $dto): bool
    {
        $lastEvent = $asset->events()->latest('id')->first();

        return $asset->state === AssetState::IN_STOCK
            && (int) ($asset->unit_id ?? 0) === (int) config('assets.patrimony_unit_id')
            && $asset->sector_id === null
            && $lastEvent?->type === AssetEventType::RETURNED_TO_PATRIMONY;
    }
}
