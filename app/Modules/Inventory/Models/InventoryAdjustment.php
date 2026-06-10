<?php

namespace App\Modules\Inventory\Models;

use App\Models\User;
use App\Modules\Items\Models\Item;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryAdjustment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'adjustment_no',
        'adjustment_date',
        'item_id',
        'adjustment_type',
        'quantity',
        'before_stock',
        'after_stock',
        'reason',
        'remarks',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'adjustment_date' => 'date',
            'quantity' => 'integer',
            'before_stock' => 'integer',
            'after_stock' => 'integer',
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
