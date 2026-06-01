<?php

namespace App\Modules\Items\Models;

use App\Models\User;
use App\Modules\BusinessPartners\Models\BusinessPartner;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'item_name',
        'item_code',
        'item_type',
        'item_source',
        'supplier_id',
        'supplier_price',
        'percentage',
        'item_price',
        'available_stock',
        'reorder_point',
        'taxable',
        'item_image',
        'status',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'supplier_price' => 'decimal:2',
            'percentage' => 'decimal:2',
            'item_price' => 'decimal:2',
            'available_stock' => 'integer',
            'reorder_point' => 'integer',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(BusinessPartner::class, 'supplier_id')->where('type', 'supplier');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(\App\Modules\Quotations\Models\QuotationItem::class);
    }

    public function salesOrders(): HasMany
    {
        return $this->hasMany(\App\Modules\Sales\Models\SalesOrderItem::class);
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(\App\Modules\Purchasing\Models\PurchaseOrderItem::class);
    }

    public function inventoryMovements(): HasMany
    {
        return $this->hasMany(\App\Modules\Inventory\Models\InventoryMovement::class);
    }

    public function scopeLowStock(Builder $query): Builder
    {
        return $query->whereColumn('available_stock', '<=', 'reorder_point');
    }

    public function scopeLocal(Builder $query): Builder
    {
        return $query->where('item_source', 'local');
    }

    public function scopeImport(Builder $query): Builder
    {
        return $query->where('item_source', 'import');
    }

    public function isLowStock(): bool
    {
        return $this->available_stock <= $this->reorder_point;
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
