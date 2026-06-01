<?php

namespace App\Modules\Inventory\Services;

use App\Modules\Inventory\Models\InventoryMovement;
use App\Modules\Items\Models\Item;

class InventoryService
{
    public function stockOut(
        int $itemId,
        float $quantity,
        string $referenceType,
        ?int $referenceId = null,
        ?string $remarks = null
    ): InventoryMovement {
        $item = Item::query()->lockForUpdate()->findOrFail($itemId);
        $beforeStock = (float) $item->available_stock;
        $deducted = min(max($quantity, 0), $beforeStock);
        $afterStock = max($beforeStock - $deducted, 0);

        $item->update([
            'available_stock' => $afterStock,
            'updated_by' => auth()->id(),
        ]);

        return InventoryMovement::query()->create([
            'item_id' => $item->id,
            'movement_type' => 'stock_out',
            'quantity' => round($deducted, 2),
            'before_stock' => $beforeStock,
            'after_stock' => $afterStock,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'remarks' => $remarks,
            'created_by' => auth()->id(),
        ]);
    }
}

