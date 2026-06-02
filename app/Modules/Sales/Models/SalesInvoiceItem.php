<?php

namespace App\Modules\Sales\Models;

use App\Modules\Items\Models\Item;
use App\Modules\Quotations\Models\UnitMeasure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesInvoiceItem extends Model
{
    protected $fillable = [
        'sales_invoice_id',
        'delivery_receipt_id',
        'delivery_receipt_item_id',
        'sales_order_item_id',
        'item_id',
        'item_name',
        'description',
        'unit_measure_id',
        'delivered_quantity',
        'previously_invoiced_quantity',
        'invoiceable_quantity',
        'quantity',
        'price',
        'subtotal',
        'tax_rate',
        'tax_amount',
        'withholding_tax_rate',
        'withholding_tax_amount',
        'total',
    ];

    protected function casts(): array
    {
        return [
            'delivered_quantity' => 'decimal:2',
            'previously_invoiced_quantity' => 'decimal:2',
            'invoiceable_quantity' => 'decimal:2',
            'quantity' => 'decimal:2',
            'price' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'tax_rate' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'withholding_tax_rate' => 'decimal:2',
            'withholding_tax_amount' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    public function salesInvoice(): BelongsTo
    {
        return $this->belongsTo(SalesInvoice::class);
    }

    public function deliveryReceipt(): BelongsTo
    {
        return $this->belongsTo(DeliveryReceipt::class);
    }

    public function deliveryReceiptItem(): BelongsTo
    {
        return $this->belongsTo(DeliveryReceiptItem::class);
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
