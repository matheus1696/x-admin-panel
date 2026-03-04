<?php

use App\Enums\Assets\AssetEventType;
use App\Enums\Assets\AssetState;
use App\Models\Assets\Asset;
use App\Models\Assets\AssetEvent;
use App\Models\Assets\AssetInvoice;
use App\Models\Assets\AssetInvoiceItem;
use Illuminate\Support\Facades\Schema;

test('assets core tables are available', function () {
    expect(Schema::hasTable('asset_invoices'))->toBeTrue();
    expect(Schema::hasTable('asset_invoice_items'))->toBeTrue();
    expect(Schema::hasTable('assets'))->toBeTrue();
    expect(Schema::hasTable('asset_events'))->toBeTrue();
});

test('assets core model relations are wired', function () {
    $invoice = AssetInvoice::create([
        'invoice_number' => 'NF-100',
        'supplier_name' => 'Fornecedor Teste',
        'issue_date' => now()->toDateString(),
        'total_amount' => 1000,
    ]);

    $item = AssetInvoiceItem::create([
        'asset_invoice_id' => $invoice->id,
        'description' => 'Notebook corporativo',
        'quantity' => 1,
        'unit_price' => 1000,
        'total_price' => 1000,
    ]);

    $asset = Asset::create([
        'invoice_item_id' => $item->id,
        'code' => 'AST-0001',
        'description' => 'Notebook corporativo',
        'state' => AssetState::IN_STOCK,
    ]);

    $event = AssetEvent::create([
        'asset_id' => $asset->id,
        'type' => AssetEventType::STOCK_RECEIVED,
    ]);

    expect($invoice->items)->toHaveCount(1);
    expect($item->invoice->is($invoice))->toBeTrue();
    expect($item->assets)->toHaveCount(1);
    expect($asset->invoiceItem->is($item))->toBeTrue();
    expect($asset->events)->toHaveCount(1);
    expect($event->asset->is($asset))->toBeTrue();
});
