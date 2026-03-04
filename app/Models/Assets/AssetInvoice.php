<?php

namespace App\Models\Assets;

use App\Models\Administration\User\User;
use App\Models\Traits\HasUuid;
use App\Models\Traits\HasUuidRouteKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssetInvoice extends Model
{
    use HasUuid, HasUuidRouteKey;

    protected $fillable = [
        'uuid',
        'invoice_number',
        'invoice_series',
        'supplier_name',
        'supplier_document',
        'issue_date',
        'received_date',
        'total_amount',
        'notes',
        'created_user_id',
    ];

    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
            'received_date' => 'date',
            'total_amount' => 'decimal:2',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(AssetInvoiceItem::class, 'asset_invoice_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_user_id');
    }
}
