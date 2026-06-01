<?php

namespace App\Modules\Sales\Models;

use App\Models\User;
use App\Modules\BusinessPartners\Models\BusinessPartner;
use App\Modules\Quotations\Models\Quotation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesOrder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'sales_order_no',
        'order_date',
        'no_of_days',
        'delivery_date',
        'customer_po',
        'agent_name',
        'remarks',
        'business_partner_id',
        'terms',
        'company_address',
        'contact_person',
        'contact_no',
        'quotation_id',
        'currency',
        'tax_rate',
        'subtotal',
        'tax_amount',
        'total_amount',
        'po_attachment',
        'status',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'order_date' => 'date',
            'delivery_date' => 'date',
            'no_of_days' => 'integer',
            'terms' => 'integer',
            'tax_rate' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
        ];
    }

    public function businessPartner(): BelongsTo
    {
        return $this->belongsTo(BusinessPartner::class);
    }

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(SalesOrderItem::class);
    }

    public function deliveryReceipts(): HasMany
    {
        return $this->hasMany(DeliveryReceipt::class);
    }
}
