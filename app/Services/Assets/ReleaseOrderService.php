<?php

namespace App\Services\Assets;

use App\DTOs\Assets\CreateReleaseOrderDTO;
use App\DTOs\Assets\TransferAssetDTO;
use App\Enums\Assets\AssetState;
use App\Exceptions\Assets\AssetsValidationException;
use App\Models\Assets\Asset;
use App\Models\Assets\AssetReleaseOrder;
use Illuminate\Support\Facades\DB;

class ReleaseOrderService
{
    public function __construct(
        private readonly AssetOperationService $assetOperationService,
    ) {}

    public function createAndRelease(CreateReleaseOrderDTO $dto): AssetReleaseOrder
    {
        $assetIds = collect($dto->assetIds)
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values();

        if ($assetIds->isEmpty()) {
            throw new AssetsValidationException('Selecione ao menos um ativo para liberar.');
        }

        $assets = Asset::query()
            ->with(['invoiceItem.invoice.financialBlock'])
            ->whereIn('id', $assetIds->all())
            ->get();

        if ($assets->count() !== $assetIds->count()) {
            throw new AssetsValidationException('Alguns ativos selecionados nao foram encontrados.');
        }

        foreach ($assets as $asset) {
            if ($asset->state !== AssetState::IN_STOCK) {
                throw new AssetsValidationException('Somente ativos em estoque podem ser liberados no pedido.');
            }
        }

        return DB::transaction(function () use ($dto, $assets): AssetReleaseOrder {
            $order = AssetReleaseOrder::create([
                'code' => $this->generateCode(),
                'status' => 'RELEASED',
                'to_unit_id' => $dto->unitId,
                'to_sector_id' => $dto->sectorId,
                'requester_name' => $dto->requesterName,
                'receiver_name' => $dto->receiverName,
                'notes' => $dto->notes,
                'total_assets' => $assets->count(),
                'released_at' => now(),
                'released_user_id' => $dto->actorUserId,
            ]);

            foreach ($assets as $asset) {
                $this->assetOperationService->releaseFromStock(new TransferAssetDTO(
                    assetId: $asset->id,
                    unitId: $dto->unitId,
                    sectorId: $dto->sectorId,
                    actorUserId: $dto->actorUserId,
                    notes: $dto->notes,
                ));

                $financialBlock = $asset->invoiceItem?->invoice?->financialBlock;
                $itemDescription = $asset->invoiceItem?->description ?: $asset->description;

                $order->items()->create([
                    'asset_id' => $asset->id,
                    'item_description' => $itemDescription,
                    'asset_code' => $asset->code,
                    'patrimony_number' => $asset->patrimony_number,
                    'invoice_number' => $asset->invoiceItem?->invoice?->invoice_number,
                    'financial_block_label' => $financialBlock?->acronym ?: $financialBlock?->title,
                ]);
            }

            return $order->fresh(['items', 'toUnit', 'toSector', 'releasedBy']);
        });
    }

    private function generateCode(): string
    {
        return 'LIB-'.now()->format('YmdHis').'-'.strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
    }
}

