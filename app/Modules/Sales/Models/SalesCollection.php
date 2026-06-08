<?php

namespace App\Modules\Sales\Models;

use App\Models\User;
use App\Modules\BusinessPartners\Models\BusinessPartner;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesCollection extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'collection_no',
        'business_partner_id',
        'company_name',
        'agent_name',
        'contact_person',
        'bank_name',
        'check_number',
        'check_date',
        'check_amount',
        'collection_receipt_no',
        'collection_receipt_date',
        'collection_receipt_amount',
        'applied_amount',
        'status',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'check_date' => 'date',
            'collection_receipt_date' => 'date',
            'check_amount' => 'decimal:2',
            'collection_receipt_amount' => 'decimal:2',
            'applied_amount' => 'decimal:2',
        ];
    }

    public function businessPartner(): BelongsTo
    {
        return $this->belongsTo(BusinessPartner::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(SalesCollectionInvoice::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
