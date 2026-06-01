<?php

namespace App\Modules\Inventory\Models;

use App\Models\User;
use App\Modules\Items\Models\Item;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryMovement extends Model
{
    protected $fillable = [
        'item_id',
        'movement_type',
        'quantity',
        'before_stock',
        'after_stock',
        'reference_type',
        'reference_id',
        'remarks',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'before_stock' => 'decimal:2',
            'after_stock' => 'decimal:2',
        ];
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

