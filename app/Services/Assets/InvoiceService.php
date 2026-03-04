<?php

namespace App\Services\Assets;

use App\DTOs\Assets\CreateInvoiceDTO;
use App\DTOs\Assets\UpsertInvoiceItemDTO;
use App\Models\Assets\AssetInvoice;
use App\Models\Assets\AssetInvoiceItem;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    public function createInvoice(CreateInvoiceDTO $dto): AssetInvoice
    {
        return DB::transaction(function () use ($dto): AssetInvoice {
            return AssetInvoice::create([
                'invoice_number' => $dto->invoiceNumber,
                'invoice_series' => $dto->invoiceSeries,
                'supplier_name' => $dto->supplierName,
                'supplier_document' => $dto->supplierDocument,
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

            $invoice->update([
                'invoice_number' => $dto->invoiceNumber,
                'invoice_series' => $dto->invoiceSeries,
                'supplier_name' => $dto->supplierName,
                'supplier_document' => $dto->supplierDocument,
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

            $item = $dto->itemId
                ? AssetInvoiceItem::query()
                    ->where('asset_invoice_id', $invoice->id)
                    ->lockForUpdate()
                    ->findOrFail($dto->itemId)
                : new AssetInvoiceItem([
                    'asset_invoice_id' => $invoice->id,
                ]);

            $item->fill([
                'item_code' => $dto->itemCode,
                'description' => $dto->description,
                'quantity' => $dto->quantity,
                'unit_price' => $dto->unitPrice,
                'total_price' => $dto->totalPrice,
                'brand' => $dto->brand,
                'model' => $dto->model,
                'metadata' => $dto->metadata,
            ]);

            $item->save();

            return $item->refresh();
        });
    }

    public function deleteItem(int $itemId): void
    {
        DB::transaction(function () use ($itemId): void {
            $item = AssetInvoiceItem::query()
                ->lockForUpdate()
                ->findOrFail($itemId);

            $item->delete();
        });
    }
}
