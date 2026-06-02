<?php

namespace App\Modules\Sales\Models;

use App\Models\User;
use App\Modules\BusinessPartners\Models\BusinessPartner;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesInvoice extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'sales_invoice_no',
        'invoice_date',
        'due_date',
        'business_partner_id',
        'sales_order_id',
        'delivery_receipt_id',
        'sales_order_no',
        'delivery_receipt_no',
        'customer_po',
        'company_name',
        'terms',
        'company_address',
        'contact_person',
        'contact_no',
        'currency',
        'tax_rate',
        'subtotal',
        'tax_amount',
        'total_amount',
        'amount_paid',
        'balance_amount',
        'status',
        'remarks',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'invoice_date' => 'date',
            'due_date' => 'date',
            'terms' => 'integer',
            'tax_rate' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'balance_amount' => 'decimal:2',
        ];
    }

    public function businessPartner(): BelongsTo
    {
        return $this->belongsTo(BusinessPartner::class);
    }

    public function salesOrder(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function deliveryReceipt(): BelongsTo
    {
        return $this->belongsTo(DeliveryReceipt::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SalesInvoiceItem::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
