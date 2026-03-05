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

            $receivedCount = Asset::query()
                ->where('invoice_item_id', $invoiceItem->id)
                ->lockForUpdate()
                ->count();

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

                $ordinal = $receivedCount + $index + 1;

                $identity = $this->assetIdentityFor($asset, $invoiceItem, $ordinal);

                $asset->update([
                    'code' => $identity['code'],
                    'patrimony_number' => $identity['patrimony_number'],
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

    /**
     * @return array{code: string, patrimony_number: ?string}
     */
    private function assetIdentityFor(Asset $asset, AssetInvoiceItem $invoiceItem, int $ordinal): array
    {
        $itemCode = trim((string) ($invoiceItem->item_code ?? ''));

        if ($itemCode === '') {
            return [
                'code' => 'AST'.str_pad((string) $asset->id, 6, '0', STR_PAD_LEFT),
                'patrimony_number' => null,
            ];
        }

        if (ctype_digit($itemCode)) {
            $candidate = (string) ((int) $itemCode + max(0, $ordinal - 1));

            while (Asset::query()->where('code', $candidate)->where('id', '!=', $asset->id)->exists()) {
                $candidate = (string) (((int) $candidate) + 1);
            }

            return [
                'code' => $candidate,
                'patrimony_number' => $candidate,
            ];
        }

        $candidate = $invoiceItem->quantity > 1
            ? $itemCode.'-'.str_pad((string) $ordinal, 3, '0', STR_PAD_LEFT)
            : $itemCode;

        $exists = Asset::query()
            ->where('code', $candidate)
            ->where('id', '!=', $asset->id)
            ->exists();

        $code = $exists ? $candidate.'-'.$asset->id : $candidate;

        return [
            'code' => $code,
            'patrimony_number' => $code,
        ];
    }
}
