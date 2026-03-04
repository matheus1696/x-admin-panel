<?php

namespace App\Services\Assets;

use App\DTOs\Assets\ReceiveStockDTO;
use App\Enums\Assets\AssetEventType;
use App\Enums\Assets\AssetState;
use App\Models\Assets\Asset;
use App\Models\Assets\AssetEvent;
use App\Models\Assets\AssetInvoiceItem;
use App\Validation\Assets\CanReceiveStockValidator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StockService
{
    public function __construct(
        private readonly CanReceiveStockValidator $canReceiveStockValidator,
    ) {}

    /**
     * @return array<int, \App\Models\Assets\Asset>
     */
    public function receiveStock(ReceiveStockDTO $dto): array
    {
        return DB::transaction(function () use ($dto): array {
            $invoiceItem = AssetInvoiceItem::query()
                ->lockForUpdate()
                ->findOrFail($dto->invoiceItemId);

            $this->canReceiveStockValidator->validateOrFail($dto, $invoiceItem);

            $assets = [];

            for ($index = 0; $index < $dto->quantity; $index++) {
                $asset = Asset::create([
                    'invoice_item_id' => $invoiceItem->id,
                    'code' => 'TMP'.Str::upper(Str::random(20)),
                    'description' => $dto->description ?? $invoiceItem->description,
                    'brand' => $dto->brand ?? $invoiceItem->brand,
                    'model' => $dto->model ?? $invoiceItem->model,
                    'state' => AssetState::IN_STOCK,
                    'unit_id' => config('assets.stock_default_unit_id'),
                    'sector_id' => null,
                    'created_user_id' => $dto->actorUserId,
                    'acquired_date' => $dto->acquiredDate,
                    'metadata' => $dto->metadata,
                ]);

                $asset->update([
                    'code' => $this->assetCodeFor($asset),
                ]);

                AssetEvent::create([
                    'asset_id' => $asset->id,
                    'type' => AssetEventType::STOCK_RECEIVED,
                    'to_state' => AssetState::IN_STOCK->value,
                    'to_unit_id' => $asset->unit_id,
                    'actor_user_id' => $dto->actorUserId,
                    'payload' => [
                        'invoice_item_id' => $invoiceItem->id,
                    ],
                ]);

                $assets[] = $asset->refresh();
            }

            return $assets;
        });
    }

    private function assetCodeFor(Asset $asset): string
    {
        return 'AST'.str_pad((string) $asset->id, 6, '0', STR_PAD_LEFT);
    }
}
