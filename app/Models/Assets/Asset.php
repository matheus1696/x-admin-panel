<?php

namespace App\Models\Assets;

use App\Enums\Assets\AssetState;
use App\Models\Administration\User\User;
use App\Models\Configuration\Establishment\Establishment\Department;
use App\Models\Configuration\Establishment\Establishment\Establishment;
use App\Models\Traits\HasUuid;
use App\Models\Traits\HasUuidRouteKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asset extends Model
{
    use HasUuid, HasUuidRouteKey;

    protected $fillable = [
        'uuid',
        'invoice_item_id',
        'code',
        'serial_number',
        'patrimony_number',
        'description',
        'brand',
        'model',
        'state',
        'unit_id',
        'sector_id',
        'created_user_id',
        'acquired_date',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'state' => AssetState::class,
            'acquired_date' => 'date',
            'metadata' => 'array',
        ];
    }

    public function invoiceItem(): BelongsTo
    {
        return $this->belongsTo(AssetInvoiceItem::class, 'invoice_item_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Establishment::class, 'unit_id');
    }

    public function sector(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'sector_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_user_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(AssetEvent::class, 'asset_id')->orderByDesc('created_at');
    }
}
