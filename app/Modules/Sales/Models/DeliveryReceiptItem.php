<?php

namespace App\Modules\Sales\Models;

use App\Modules\Items\Models\Item;
use App\Modules\Quotations\Models\UnitMeasure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryReceiptItem extends Model
{
    protected $fillable = [
        'delivery_receipt_id',
        'sales_order_item_id',
        'item_id',
        'item_name',
        'ordered_quantity',
        'previously_delivered_quantity',
        'remaining_balance_quantity',
        'available_stock',
        'delivered_quantity',
        'balance_quantity',
        'unit_measure_id',
        'stock_status',
        'delivery_no',
        'delivered_date',
        'delivered_by',
        'received_by',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'ordered_quantity' => 'decimal:2',
            'previously_delivered_quantity' => 'decimal:2',
            'remaining_balance_quantity' => 'decimal:2',
            'available_stock' => 'decimal:2',
            'delivered_quantity' => 'decimal:2',
            'balance_quantity' => 'decimal:2',
            'delivered_date' => 'date',
        ];
    }

    public function deliveryReceipt(): BelongsTo
    {
        return $this->belongsTo(DeliveryReceipt::class);
    }

    public function salesOrderItem(): BelongsTo
    {
        return $this->belongsTo(SalesOrderItem::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function unitMeasure(): BelongsTo
    {
        return $this->belongsTo(UnitMeasure::class);
    }
}

