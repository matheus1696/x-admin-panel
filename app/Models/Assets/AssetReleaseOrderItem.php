<?php

namespace App\Models\Assets;

use App\Models\Traits\HasUuid;
use App\Models\Traits\HasUuidRouteKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetReleaseOrderItem extends Model
{
    use HasUuid, HasUuidRouteKey;

    protected $fillable = [
        'uuid',
        'asset_release_order_id',
        'asset_id',
        'item_description',
        'asset_code',
        'patrimony_number',
        'invoice_number',
        'financial_block_label',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(AssetReleaseOrder::class, 'asset_release_order_id');
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }
}

