<?php

namespace App\Modules\Sales\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesCollectionInvoice extends Model
{
    protected $fillable = [
        'sales_collection_id',
        'sales_invoice_id',
        'sales_invoice_no',
        'subtotal',
        'tax_amount',
        'total_invoice_amount',
        'withholding_tax_amount',
        'previous_balance',
        'applied_amount',
        'remaining_balance',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total_invoice_amount' => 'decimal:2',
            'withholding_tax_amount' => 'decimal:2',
            'previous_balance' => 'decimal:2',
            'applied_amount' => 'decimal:2',
            'remaining_balance' => 'decimal:2',
        ];
    }

    public function salesCollection(): BelongsTo
    {
        return $this->belongsTo(SalesCollection::class);
    }

    public function salesInvoice(): BelongsTo
    {
        return $this->belongsTo(SalesInvoice::class);
    }
}
