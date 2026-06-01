<?php

namespace App\Modules\Sales\Models;

use App\Models\User;
use App\Modules\BusinessPartners\Models\BusinessPartner;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeliveryReceipt extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'delivery_receipt_no',
        'dr_date',
        'received_date',
        'received_by',
        'delivered_by',
        'sales_order_id',
        'sales_order_no',
        'customer_po',
        'agent_name',
        'business_partner_id',
        'company_name',
        'terms',
        'company_address',
        'contact_person',
        'contact_no',
        'status',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'dr_date' => 'date',
            'received_date' => 'date',
            'terms' => 'integer',
        ];
    }

    public function salesOrder(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function businessPartner(): BelongsTo
    {
        return $this->belongsTo(BusinessPartner::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(DeliveryReceiptItem::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(DeliveryReceiptAttachment::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
