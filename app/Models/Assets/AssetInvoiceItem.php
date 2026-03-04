<?php

namespace App\Models\Assets;

use App\Models\Traits\HasUuid;
use App\Models\Traits\HasUuidRouteKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssetInvoiceItem extends Model
{
    use HasUuid, HasUuidRouteKey;

    protected $fillable = [
        'uuid',
        'asset_invoice_id',
        'item_code',
        'description',
        'quantity',
        'unit_price',
        'total_price',
        'brand',
        'model',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'total_price' => 'decimal:2',
            'metadata' => 'array',
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(AssetInvoice::class, 'asset_invoice_id');
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class, 'invoice_item_id');
    }
}
