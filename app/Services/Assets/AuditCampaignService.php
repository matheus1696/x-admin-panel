<?php

namespace App\Services\Assets;

use App\DTOs\Assets\CreateAuditCampaignDTO;
use App\DTOs\Assets\RegisterAuditCampaignItemDTO;
use App\Enums\Assets\AssetEventType;
use App\Models\Assets\Asset;
use App\Models\Assets\AssetAuditCampaign;
use App\Models\Assets\AssetAuditCampaignItem;
use App\Models\Assets\AssetAuditIssue;
use Illuminate\Support\Facades\DB;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class AuditCampaignService
{
    public function createCampaign(CreateAuditCampaignDTO $dto): AssetAuditCampaign
    {
        return DB::transaction(function () use ($dto): AssetAuditCampaign {
            $campaign = AssetAuditCampaign::create([
                'title' => $dto->title,
                'status' => 'IN_PROGRESS',
                'unit_id' => $dto->unitId,
                'sector_id' => $dto->sectorId,
                'financial_block_id' => $dto->financialBlockId,
                'start_date' => $dto->startDate,
                'due_date' => $dto->dueDate,
                'started_at' => now(),
                'created_user_id' => $dto->createdUserId,
            ]);

            $assets = Asset::query()
                ->with(['invoiceItem.invoice'])
                ->when($dto->unitId, fn ($query) => $query->where('unit_id', $dto->unitId))
                ->when($dto->sectorId, fn ($query) => $query->where('sector_id', $dto->sectorId))
                ->when(
                    $dto->financialBlockId,
                    fn ($query) => $query->whereHas('invoiceItem.invoice', fn ($invoiceQuery) => $invoiceQuery->where('financial_block_id', $dto->financialBlockId))
                )
                ->orderBy('id')
                ->get();

            $now = now();

            $rows = $assets->map(fn ($asset) => [
                'uuid' => (string) \Illuminate\Support\Str::uuid(),
                'asset_audit_campaign_id' => $campaign->id,
                'asset_id' => $asset->id,
                'status' => 'PENDING',
                'expected_unit_id' => $asset->unit_id,
                'expected_sector_id' => $asset->sector_id,
                'created_at' => $now,
                'updated_at' => $now,
            ])->all();

            if (! empty($rows)) {
                AssetAuditCampaignItem::query()->insert($rows);
            }

            return $campaign->fresh('items');
        });
    }

    public function registerItemAudit(RegisterAuditCampaignItemDTO $dto): void
    {
        DB::transaction(function () use ($dto): void {
            $item = AssetAuditCampaignItem::query()
                ->with(['campaign', 'asset'])
                ->lockForUpdate()
                ->findOrFail($dto->campaignItemId);

            if ($item->campaign->status === 'CONCLUDED') {
                return;
            }

            $item->update([
                'status' => $dto->status,
                'audited_at' => now(),
                'audited_user_id' => $dto->actorUserId,
                'photo_path' => $dto->photoPath,
                'notes' => $dto->notes,
                'observed_unit' => $dto->observedUnit,
                'observed_sector' => $dto->observedSector,
            ]);

            $item->asset->events()->create([
                'type' => AssetEventType::AUDITED,
                'from_state' => $item->asset->state?->value,
                'to_state' => $item->asset->state?->value,
                'from_unit_id' => $item->asset->unit_id,
                'to_unit_id' => $item->asset->unit_id,
                'from_sector_id' => $item->asset->sector_id,
                'to_sector_id' => $item->asset->sector_id,
                'actor_user_id' => $dto->actorUserId,
                'notes' => $dto->notes,
                'payload' => [
                    'context' => [
                        'service' => self::class,
                        'operation' => 'campaign_item_audit',
                    ],
                    'audit_campaign_id' => $item->asset_audit_campaign_id,
                    'audit_campaign_item_id' => $item->id,
                    'audit_status' => $dto->status,
                    'photo_path' => $dto->photoPath,
                    'observed_unit' => $dto->observedUnit,
                    'observed_sector' => $dto->observedSector,
                ],
            ]);

            $this->syncIssue($item, $dto->status, $dto->notes, $dto->actorUserId);
        });
    }

    public function finalizeCampaign(int $campaignId, ?int $userId): void
    {
        $campaign = AssetAuditCampaign::query()->findOrFail($campaignId);

        $campaign->update([
            'status' => 'CONCLUDED',
            'finished_at' => now(),
            'finished_user_id' => $userId,
        ]);
    }

    public function storeAuditPhoto(TemporaryUploadedFile $file): string
    {
        return $file->store('assets/audit-campaigns/'.now()->format('Y/m'), 'public');
    }

    private function syncIssue(AssetAuditCampaignItem $item, string $status, ?string $notes, ?int $actorUserId): void
    {
        $issueStatuses = ['NOT_FOUND', 'DIVERGENCE', 'DAMAGED', 'NO_TAG'];

        $existing = AssetAuditIssue::query()
            ->where('asset_audit_campaign_item_id', $item->id)
            ->where('status', 'OPEN')
            ->latest('id')
            ->first();

        if (in_array($status, $issueStatuses, true)) {
            if ($existing) {
                $existing->update([
                    'issue_type' => $status,
                    'notes' => $notes,
                ]);

                return;
            }

            AssetAuditIssue::create([
                'asset_audit_campaign_id' => $item->asset_audit_campaign_id,
                'asset_audit_campaign_item_id' => $item->id,
                'asset_id' => $item->asset_id,
                'issue_type' => $status,
                'status' => 'OPEN',
                'notes' => $notes,
                'opened_at' => now(),
            ]);

            return;
        }

        if ($existing) {
            $existing->update([
                'status' => 'RESOLVED',
                'resolved_at' => now(),
                'resolved_user_id' => $actorUserId,
            ]);
        }
    }
}

