<?php

namespace App\Modules\BusinessPartners\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusinessPartner extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type',
        'company_name',
        'company_code',
        'tin_number',
        'contact_person',
        'contact_no',
        'agent_name',
        'credit_limit',
        'company_address',
        'under_pesa',
        'vatable',
        'terms',
        'status',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'credit_limit' => 'decimal:2',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeClients(Builder $query): Builder
    {
        return $query->where('type', 'client');
    }

    public function scopeSuppliers(Builder $query): Builder
    {
        return $query->where('type', 'supplier');
    }

    public function isClient(): bool
    {
        return $this->type === 'client';
    }

    public function isSupplier(): bool
    {
        return $this->type === 'supplier';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
