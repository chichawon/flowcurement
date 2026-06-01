<?php

namespace App\Modules\Quotations\Models;

use App\Modules\Items\Models\Item;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuotationItem extends Model
{
    protected $fillable = ['quotation_id', 'item_id', 'description', 'unit_measure_id', 'item_price', 'quantity', 'total'];

    protected function casts(): array
    {
        return ['item_price' => 'decimal:2', 'quantity' => 'decimal:2', 'total' => 'decimal:2'];
    }

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function unitMeasure(): BelongsTo
    {
        return $this->belongsTo(UnitMeasure::class);
    }
}
