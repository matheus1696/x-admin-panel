<?php

namespace App\Services\Assets;

use App\DTOs\Assets\CreateInvoiceDTO;
use App\DTOs\Assets\UpsertInvoiceItemDTO;
use App\Enums\Assets\AssetEventType;
use App\Enums\Assets\AssetState;
use App\Exceptions\Assets\AssetsValidationException;
use App\Models\Assets\Asset;
use App\Models\Assets\AssetEvent;
use App\Models\Assets\AssetInvoice;
use App\Models\Assets\AssetInvoiceItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InvoiceService
{
    public function createInvoice(CreateInvoiceDTO $dto): AssetInvoice
    {
        return DB::transaction(function () use ($dto): AssetInvoice {
            return AssetInvoice::create([
                'invoice_number' => $dto->invoiceNumber,
                'invoice_series' => $dto->invoiceSeries,
                'financial_block_id' => $dto->financialBlockId,
                'supplier_name' => $dto->supplierName,
                'supplier_document' => $dto->supplierDocument,
                'supply_order' => $dto->supplyOrder,
                'issue_date' => $dto->issueDate,
                'received_date' => $dto->receivedDate,
                'total_amount' => $dto->totalAmount,
                'notes' => $dto->notes,
                'created_user_id' => $dto->createdUserId,
            ]);
        });
    }

    public function updateInvoice(int $invoiceId, CreateInvoiceDTO $dto): AssetInvoice
    {
        return DB::transaction(function () use ($invoiceId, $dto): AssetInvoice {
            $invoice = AssetInvoice::query()
                ->lockForUpdate()
                ->findOrFail($invoiceId);

            if ((bool) $invoice->is_finalized) {
                throw new AssetsValidationException('Nao e permitido editar uma nota fiscal finalizada.');
            }

            $invoice->update([
                'invoice_number' => $dto->invoiceNumber,
                'invoice_series' => $dto->invoiceSeries,
                'financial_block_id' => $dto->financialBlockId,
                'supplier_name' => $dto->supplierName,
                'supplier_document' => $dto->supplierDocument,
                'supply_order' => $dto->supplyOrder,
                'issue_date' => $dto->issueDate,
                'received_date' => $dto->receivedDate,
                'total_amount' => $dto->totalAmount,
                'notes' => $dto->notes,
            ]);

            return $invoice->refresh();
        });
    }

    public function addOrUpdateItem(UpsertInvoiceItemDTO $dto): AssetInvoiceItem
    {
        return DB::transaction(function () use ($dto): AssetInvoiceItem {
            $invoice = AssetInvoice::query()
                ->lockForUpdate()
                ->findOrFail($dto->assetInvoiceId);

            if ((bool) $invoice->is_finalized) {
                throw new AssetsValidationException('Nao e permitido editar itens de uma nota fiscal finalizada.');
            }

            $item = $dto->itemId
                ? AssetInvoiceItem::query()
                    ->where('asset_invoice_id', $invoice->id)
                    ->lockForUpdate()
                    ->findOrFail($dto->itemId)
                : new AssetInvoiceItem([
                    'asset_invoice_id' => $invoice->id,
                ]);

            $unitPrice = round((float) $dto->unitPrice, 2);
            $totalPrice = round($dto->quantity * $unitPrice, 2);

            $item->fill([
                'product_id' => $dto->productId,
                'product_measure_unit_id' => $dto->productMeasureUnitId,
                'item_code' => $dto->itemCode,
                'description' => $dto->description,
                'quantity' => $dto->quantity,
                'unit_price' => $unitPrice,
                'total_price' => $totalPrice,
                'brand' => $dto->brand,
                'model' => $dto->model,
                'metadata' => $dto->metadata,
            ]);

            $item->save();
            $this->syncInvoiceTotal($invoice);

            return $item->refresh();
        });
    }

    public function deleteItem(int $itemId): void
    {
        DB::transaction(function () use ($itemId): void {
            $item = AssetInvoiceItem::query()
                ->lockForUpdate()
                ->findOrFail($itemId);

            $invoice = AssetInvoice::query()
                ->lockForUpdate()
                ->findOrFail($item->asset_invoice_id);

            if ((bool) $invoice->is_finalized) {
                throw new AssetsValidationException('Nao e permitido remover itens de uma nota fiscal finalizada.');
            }

            $item->delete();
            $this->syncInvoiceTotal($invoice);
        });
    }

    public function finalizeInvoice(int $invoiceId, ?int $actorUserId = null): AssetInvoice
    {
        return DB::transaction(function () use ($invoiceId, $actorUserId): AssetInvoice {
            $invoice = AssetInvoice::query()
                ->lockForUpdate()
                ->findOrFail($invoiceId);

            if ((bool) $invoice->is_finalized) {
                return $invoice->refresh();
            }

            $items = AssetInvoiceItem::query()
                ->where('asset_invoice_id', $invoice->id)
                ->lockForUpdate()
                ->get();

            if ($items->isEmpty()) {
                throw new AssetsValidationException('Adicione ao menos um item antes de finalizar a nota fiscal.');
            }

            foreach ($items as $item) {
                $receivedAssets = Asset::query()
                    ->where('invoice_item_id', $item->id)
                    ->lockForUpdate()
                    ->get(['id']);

                $receivedCount = $receivedAssets->count();

                $remaining = max(0, (int) $item->quantity - $receivedCount);

                for ($index = 0; $index < $remaining; $index++) {
                    $asset = Asset::create([
                        'invoice_item_id' => $item->id,
                        'code' => 'TMP'.Str::upper(Str::random(20)),
                        'description' => $item->description,
                        'brand' => $item->brand,
                        'model' => $item->model,
                        'state' => AssetState::IN_STOCK,
                        'unit_id' => config('assets.stock_default_unit_id'),
                        'sector_id' => null,
                        'created_user_id' => $actorUserId,
                        'acquired_date' => optional($invoice->received_date)->toDateString() ?? now()->toDateString(),
                    ]);

                    $ordinal = $receivedCount + $index + 1;

                    $identity = $this->assetIdentityFor($asset, $item, $ordinal);

                    $asset->update([
                        'code' => $identity['code'],
                        'patrimony_number' => $identity['patrimony_number'],
                    ]);

                    AssetEvent::create([
                        'asset_id' => $asset->id,
                        'type' => AssetEventType::STOCK_RECEIVED,
                        'to_state' => AssetState::IN_STOCK->value,
                        'to_unit_id' => $asset->unit_id,
                        'actor_user_id' => $actorUserId,
                        'payload' => [
                            'invoice_item_id' => $item->id,
                            'source' => 'invoice_finalize',
                        ],
                    ]);
                }
            }

            $invoice->update([
                'is_finalized' => true,
                'finalized_at' => now(),
                'finalized_user_id' => $actorUserId,
            ]);

            return $invoice->refresh();
        });
    }

    private function syncInvoiceTotal(AssetInvoice $invoice): void
    {
        $total = AssetInvoiceItem::query()
            ->where('asset_invoice_id', $invoice->id)
            ->sum('total_price');

        $invoice->update([
            'total_amount' => (float) $total,
        ]);
    }

    /**
     * @return array{code: string, patrimony_number: ?string}
     */
    private function assetIdentityFor(Asset $asset, AssetInvoiceItem $item, int $ordinal): array
    {
        $itemCode = trim((string) ($item->item_code ?? ''));

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

        $candidate = $item->quantity > 1
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
