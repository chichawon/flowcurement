<?php

namespace App\Modules\Purchasing\Models;

use App\Modules\Items\Models\Item;
use App\Modules\Quotations\Models\UnitMeasure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrderItem extends Model
{
    protected $fillable = [
        'purchase_order_id', 'item_id', 'description', 'lead_time', 'unit_measure_id',
        'quantity', 'price', 'subtotal', 'tax_amount', 'total', 'remarks',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'price' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    public function purchaseOrder(): BelongsTo { return $this->belongsTo(PurchaseOrder::class); }
    public function item(): BelongsTo { return $this->belongsTo(Item::class); }
    public function unitMeasure(): BelongsTo { return $this->belongsTo(UnitMeasure::class); }
}
