<?php

namespace App\Modules\Quotations\Models;

use App\Models\User;
use App\Modules\BusinessPartners\Models\BusinessPartner;
use App\Modules\Sales\Models\SalesOrder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quotation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'quotation_no', 'quotation_date', 'validity_date', 'business_partner_id',
        'company_address', 'contact_person', 'contact_no', 'agent_name', 'prepared_by',
        'remarks', 'currency', 'tax_rate', 'subtotal', 'tax_amount', 'total_amount',
        'status', 'reference_sales_order_id', 'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'quotation_date' => 'date',
            'validity_date' => 'date',
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

    public function preparedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'prepared_by');
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
        return $this->hasMany(QuotationItem::class);
    }

    public function referenceSalesOrder(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class, 'reference_sales_order_id');
    }
}
