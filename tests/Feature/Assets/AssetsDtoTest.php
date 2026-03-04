<?php

use App\DTOs\Assets\AuditAssetDTO;
use App\DTOs\Assets\ChangeAssetStateDTO;
use App\DTOs\Assets\CreateInvoiceDTO;
use App\DTOs\Assets\ReceiveStockDTO;
use App\DTOs\Assets\ReleaseAssetDTO;
use App\DTOs\Assets\ReturnToPatrimonyDTO;
use App\DTOs\Assets\TransferAssetDTO;
use App\DTOs\Assets\UpsertInvoiceItemDTO;
use App\Enums\Assets\AssetState;

test('assets dto classes can be instantiated with named parameters', function () {
    $createInvoice = new CreateInvoiceDTO(
        invoiceNumber: 'NF-200',
        invoiceSeries: 'A1',
        supplierName: 'Fornecedor Teste',
        supplierDocument: '00.000.000/0001-00',
        issueDate: '2026-03-04',
        receivedDate: '2026-03-05',
        totalAmount: '1520.55',
        notes: 'Observacao',
        createdUserId: 9,
    );

    $upsertItem = new UpsertInvoiceItemDTO(
        assetInvoiceId: 12,
        itemId: 15,
        itemCode: 'ITEM-01',
        description: 'Notebook',
        quantity: 3,
        unitPrice: 1000,
        totalPrice: 3000,
        brand: 'Dell',
        model: 'Latitude',
        metadata: ['ram' => '16GB'],
    );

    $receiveStock = new ReceiveStockDTO(
        invoiceItemId: 15,
        quantity: 2,
        description: 'Notebook corporativo',
        brand: 'Dell',
        model: 'Latitude',
        acquiredDate: '2026-03-06',
        actorUserId: 3,
        metadata: ['batch' => 'A'],
    );

    $releaseAsset = new ReleaseAssetDTO(
        assetId: 4,
        unitId: 8,
        sectorId: 11,
        actorUserId: 2,
        notes: 'Liberado para uso',
    );

    $transferAsset = new TransferAssetDTO(
        assetId: 4,
        unitId: 9,
        sectorId: 12,
        actorUserId: 2,
        notes: 'Transferencia interna',
    );

    $auditAsset = new AuditAssetDTO(
        assetId: 4,
        photoPath: 'assets/audits/foto.jpg',
        actorUserId: 2,
        notes: 'Auditoria mensal',
    );

    $changeState = new ChangeAssetStateDTO(
        assetId: 4,
        toState: AssetState::IN_USE,
        actorUserId: 2,
        notes: 'Colocado em uso',
    );

    $returnToPatrimony = new ReturnToPatrimonyDTO(
        assetId: 4,
        actorUserId: 2,
        notes: 'Encerrado',
    );

    expect($createInvoice->invoiceNumber)->toBe('NF-200')
        ->and($createInvoice->createdUserId)->toBe(9)
        ->and($upsertItem->metadata)->toBe(['ram' => '16GB'])
        ->and($receiveStock->quantity)->toBe(2)
        ->and($releaseAsset->sectorId)->toBe(11)
        ->and($transferAsset->unitId)->toBe(9)
        ->and($auditAsset->photoPath)->toBe('assets/audits/foto.jpg')
        ->and($changeState->toState)->toBe(AssetState::IN_USE)
        ->and($returnToPatrimony->assetId)->toBe(4);
});
