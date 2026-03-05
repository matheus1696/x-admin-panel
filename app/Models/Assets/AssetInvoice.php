<?php

namespace App\Models\Assets;

use App\Models\Administration\User\User;
use App\Models\Configuration\FinancialBlock\FinancialBlock;
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
        'financial_block_id',
        'supplier_name',
        'supplier_document',
        'supply_order',
        'issue_date',
        'received_date',
        'total_amount',
        'notes',
        'is_finalized',
        'finalized_at',
        'finalized_user_id',
        'created_user_id',
    ];

    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
            'received_date' => 'date',
            'total_amount' => 'decimal:2',
            'is_finalized' => 'boolean',
            'finalized_at' => 'datetime',
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

    public function finalizedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'finalized_user_id');
    }

    public function financialBlock(): BelongsTo
    {
        return $this->belongsTo(FinancialBlock::class, 'financial_block_id');
    }
}
