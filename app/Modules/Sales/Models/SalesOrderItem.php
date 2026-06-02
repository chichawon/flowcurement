<?php

namespace App\Modules\Sales\Models;

use App\Modules\Items\Models\Item;
use App\Modules\Quotations\Models\UnitMeasure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesOrderItem extends Model
{
    protected $fillable = [
        'sales_order_id',
        'item_id',
        'description',
        'order_quantity',
        'balance_quantity',
        'unit_measure_id',
        'price',
        'available_stock',
        'remarks',
        'total',
    ];

    protected function casts(): array
    {
        return [
            'order_quantity' => 'decimal:2',
            'balance_quantity' => 'decimal:2',
            'price' => 'decimal:2',
            'available_stock' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    public function salesOrder(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function unitMeasure(): BelongsTo
    {
        return $this->belongsTo(UnitMeasure::class);
    }

    public function deliveryReceiptItems(): HasMany
    {
        return $this->hasMany(DeliveryReceiptItem::class);
    }

    public function salesInvoiceItems(): HasMany
    {
        return $this->hasMany(SalesInvoiceItem::class);
    }
}
