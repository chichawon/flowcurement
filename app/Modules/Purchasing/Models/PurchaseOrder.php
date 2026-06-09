<?php

namespace App\Modules\Purchasing\Models;

use App\Models\User;
use App\Modules\BusinessPartners\Models\BusinessPartner;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'purchase_order_no', 'purchase_order_date', 'expected_delivery_date', 'supplier_id',
        'supplier_name', 'supplier_address', 'contact_person', 'contact_no', 'terms', 'remarks',
        'currency', 'tax_rate', 'subtotal', 'tax_amount', 'total_amount', 'status', 'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'purchase_order_date' => 'date',
            'expected_delivery_date' => 'date',
            'tax_rate' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
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

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(PurchaseInvoice::class);
    }
}
