<?php

namespace App\Services\Assets;

use App\DTOs\Assets\AuditAssetDTO;
use App\Enums\Assets\AssetEventType;
use App\Models\Assets\Asset;
use App\Models\Assets\AssetEvent;
use App\Validation\Assets\CanAuditAssetValidator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class AuditService
{
    public function __construct(
        private readonly CanAuditAssetValidator $canAuditAssetValidator,
    ) {}

    public function auditAsset(AuditAssetDTO $dto): void
    {
        DB::transaction(function () use ($dto): void {
            $asset = Asset::query()
                ->lockForUpdate()
                ->findOrFail($dto->assetId);

            $this->canAuditAssetValidator->validateOrFail($asset, $dto);

            AssetEvent::create([
                'asset_id' => $asset->id,
                'type' => AssetEventType::AUDITED,
                'from_state' => $asset->state?->value,
                'to_state' => $asset->state?->value,
                'from_unit_id' => $asset->unit_id,
                'to_unit_id' => $asset->unit_id,
                'from_sector_id' => $asset->sector_id,
                'to_sector_id' => $asset->sector_id,
                'actor_user_id' => $dto->actorUserId,
                'notes' => $dto->notes,
                'payload' => [
                    'photo_path' => $dto->photoPath,
                ],
            ]);
        });
    }

    public function storeAuditPhoto(UploadedFile $file): string
    {
        return $file->store('assets/audits/'.now()->format('Y/m'), 'public');
    }
}
