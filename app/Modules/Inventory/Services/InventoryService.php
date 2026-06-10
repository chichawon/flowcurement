<?php

namespace App\Modules\Inventory\Services;

use App\Modules\AuditTrail\Services\AuditTrailService;
use App\Modules\BusinessPartners\Models\BusinessPartner;
use App\Modules\Inventory\Models\InventoryAdjustment;
use App\Modules\Inventory\Models\InventoryMovement;
use App\Modules\Items\Models\Item;
use App\Modules\Items\Models\ItemType;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    private const MODULE = 'inventory';

    public function stockList(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return Item::query()
            ->with('supplier:id,company_name,company_code,type')
            ->when($filters['search'] ?? null, function (Builder $query, string $search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('item_name', 'like', "%{$search}%")
                        ->orWhere('item_code', 'like', "%{$search}%");
                });
            })
            ->when($filters['item_type'] ?? null, fn (Builder $query, string $type) => $query->where('item_type', $type))
            ->when($filters['supplier_id'] ?? null, fn (Builder $query, mixed $supplierId) => $query->where('supplier_id', (int) $supplierId))
            ->when(($filters['stock_filter'] ?? null) === 'low', fn (Builder $query) => $query->where('available_stock', '>', 0)->lowStock())
            ->when(($filters['stock_filter'] ?? null) === 'out', fn (Builder $query) => $query->where('available_stock', '<=', 0))
            ->orderBy('item_name')
            ->paginate($perPage);
    }

    public function stockSummary(): array
    {
        return [
            'all' => Item::query()->count(),
            'low' => Item::query()->where('available_stock', '>', 0)->lowStock()->count(),
            'out' => Item::query()->where('available_stock', '<=', 0)->count(),
        ];
    }

    public function movements(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return InventoryMovement::query()
            ->with(['item:id,item_name,item_code,item_type', 'creator:id,name'])
            ->when($filters['search'] ?? null, function (Builder $query, string $search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('movement_type', 'like', "%{$search}%")
                        ->orWhere('reference_type', 'like', "%{$search}%")
                        ->orWhere('remarks', 'like', "%{$search}%")
                        ->orWhereHas('item', function (Builder $item) use ($search): void {
                            $item->where('item_name', 'like', "%{$search}%")
                                ->orWhere('item_code', 'like', "%{$search}%");
                        });
                });
            })
            ->when($filters['movement_type'] ?? null, fn (Builder $query, string $type) => $query->where('movement_type', $type))
            ->when($filters['date_from'] ?? null, fn (Builder $query, string $date) => $query->whereDate('created_at', '>=', $date))
            ->when($filters['date_to'] ?? null, fn (Builder $query, string $date) => $query->whereDate('created_at', '<=', $date))
            ->latest('id')
            ->paginate($perPage);
    }

    /**
     * @return Collection<int, Item>
     */
    public function adjustmentItems(?string $search = null): Collection
    {
        return Item::query()
            ->when($search, function (Builder $query, string $search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('item_name', 'like', "%{$search}%")
                        ->orWhere('item_code', 'like', "%{$search}%");
                });
            })
            ->orderBy('item_name')
            ->limit(100)
            ->get(['id', 'item_name', 'item_code', 'available_stock']);
    }

    /**
     * @return Collection<int, ItemType>
     */
    public function itemTypes(): Collection
    {
        return ItemType::query()
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    /**
     * @return Collection<int, BusinessPartner>
     */
    public function suppliers(): Collection
    {
        return BusinessPartner::query()
            ->suppliers()
            ->orderBy('company_name')
            ->get(['id', 'company_name']);
    }

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

    public function adjustStock(array $data): InventoryAdjustment
    {
        return DB::transaction(function () use ($data): InventoryAdjustment {
            $item = Item::query()->lockForUpdate()->findOrFail((int) $data['item_id']);
            $beforeStock = (int) $item->available_stock;
            $quantity = max((int) ($data['quantity'] ?? 0), 0);
            $type = (string) ($data['adjustment_type'] ?? 'add');
            $afterStock = $type === 'deduct'
                ? max($beforeStock - $quantity, 0)
                : $beforeStock + $quantity;

            $adjustment = InventoryAdjustment::query()->create([
                'adjustment_no' => $this->nextAdjustmentNo(),
                'adjustment_date' => $data['adjustment_date'] ?? now()->toDateString(),
                'item_id' => $item->id,
                'adjustment_type' => $type,
                'quantity' => $quantity,
                'before_stock' => $beforeStock,
                'after_stock' => $afterStock,
                'reason' => $data['reason'] ?? null,
                'remarks' => $data['remarks'] ?? null,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);

            $item->update([
                'available_stock' => $afterStock,
                'updated_by' => auth()->id(),
            ]);

            $movement = InventoryMovement::query()->create([
                'item_id' => $item->id,
                'movement_type' => $type === 'deduct' ? 'adjustment_out' : 'adjustment_in',
                'quantity' => $quantity,
                'before_stock' => $beforeStock,
                'after_stock' => $afterStock,
                'reference_type' => 'inventory_adjustment',
                'reference_id' => $adjustment->id,
                'remarks' => trim(($adjustment->reason ? $adjustment->reason.' - ' : '').(string) $adjustment->remarks) ?: null,
                'created_by' => auth()->id(),
            ]);

            app(AuditTrailService::class)->record(
                self::MODULE,
                'adjusted',
                $adjustment,
                ['available_stock' => $beforeStock],
                ['available_stock' => $afterStock, 'movement_id' => $movement->id],
                'Inventory adjusted: '.$item->item_name.' ('.$adjustment->adjustment_no.')'
            );

            return $adjustment;
        });
    }

    public function nextAdjustmentNo(): string
    {
        $prefix = 'IA'.now()->format('y-m').'-';
        $lastNo = InventoryAdjustment::query()
            ->where('adjustment_no', 'like', $prefix.'%')
            ->latest('id')
            ->value('adjustment_no');

        $next = 1;
        if (is_string($lastNo) && preg_match('/(\d+)$/', $lastNo, $matches) === 1) {
            $next = (int) $matches[1] + 1;
        }

        return $prefix.str_pad((string) $next, 3, '0', STR_PAD_LEFT);
    }
}
