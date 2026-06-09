<?php

namespace App\Modules\Purchasing\Models;

use App\Models\User;
use App\Modules\BusinessPartners\Models\BusinessPartner;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseInvoice extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'purchase_invoice_no', 'invoice_date', 'supplier_invoice_no', 'purchase_order_id', 'purchase_order_no',
        'supplier_id', 'supplier_name', 'supplier_address', 'contact_person', 'contact_no', 'terms', 'due_date',
        'currency', 'tax_rate', 'subtotal', 'tax_amount', 'total_amount', 'paid_amount', 'balance_amount',
        'remarks', 'status', 'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'invoice_date' => 'date',
            'due_date' => 'date',
            'tax_rate' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'balance_amount' => 'decimal:2',
        ];
    }

    public function purchaseOrder(): BelongsTo { return $this->belongsTo(PurchaseOrder::class); }
    public function supplier(): BelongsTo { return $this->belongsTo(BusinessPartner::class, 'supplier_id')->where('type', 'supplier'); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function updater(): BelongsTo { return $this->belongsTo(User::class, 'updated_by'); }
    public function items(): HasMany { return $this->hasMany(PurchaseInvoiceItem::class); }
}
