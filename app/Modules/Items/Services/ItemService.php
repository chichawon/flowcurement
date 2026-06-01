<?php

namespace App\Modules\Items\Services;

use App\Modules\AuditTrail\Services\AuditTrailService;
use App\Modules\BusinessPartners\Models\BusinessPartner;
use App\Modules\Items\Models\Item;
use App\Modules\Items\Models\ItemType;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ItemService
{
    private const MODULE = 'items';

    /**
     * @param array{search?: string|null, item_source?: string|null, item_type?: string|null, supplier_id?: string|int|null, taxable?: string|null, status?: string|null, stock_filter?: string|null, with_trashed?: bool} $filters
     * @return LengthAwarePaginator<int, Item>
     */
    public function paginate(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return Item::query()
            ->with(['supplier:id,company_name,company_code,type', 'creator:id,name', 'updater:id,name'])
            ->when($filters['with_trashed'] ?? false, fn (Builder $query) => $query->withTrashed())
            ->when($filters['item_source'] ?? null, fn (Builder $query, string $source) => $query->where('item_source', $source))
            ->when($filters['search'] ?? null, function (Builder $query, string $search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('item_name', 'like', "%{$search}%")
                        ->orWhere('item_code', 'like', "%{$search}%");
                });
            })
            ->when($filters['item_type'] ?? null, fn (Builder $query, string $type) => $query->where('item_type', $type))
            ->when($filters['supplier_id'] ?? null, fn (Builder $query, mixed $supplierId) => $query->where('supplier_id', (int) $supplierId))
            ->when($filters['taxable'] ?? null, fn (Builder $query, string $taxable) => $query->where('taxable', $taxable))
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status))
            ->when(($filters['stock_filter'] ?? null) === 'low', fn (Builder $query) => $query->where('available_stock', '>', 0)->lowStock())
            ->when(($filters['stock_filter'] ?? null) === 'out', fn (Builder $query) => $query->where('available_stock', '<=', 0))
            ->latest('id')
            ->paginate($perPage);
    }

    /**
     * @return array{all: int, low: int, out: int}
     */
    public function stockSummary(?string $source = null): array
    {
        $base = Item::query()
            ->when($source, fn (Builder $query, string $source) => $query->where('item_source', $source));

        return [
            'all' => (clone $base)->count(),
            'low' => (clone $base)->where('available_stock', '>', 0)->lowStock()->count(),
            'out' => (clone $base)->where('available_stock', '<=', 0)->count(),
        ];
    }

    /**
     * @return Collection<int, BusinessPartner>
     */
    public function suppliers(?string $search = null): Collection
    {
        return BusinessPartner::query()
            ->suppliers()
            ->when($search, function (Builder $query, string $search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('company_name', 'like', "%{$search}%")
                        ->orWhere('company_code', 'like', "%{$search}%");
                });
            })
            ->orderBy('company_name')
            ->get(['id', 'company_name', 'company_code']);
    }

    /**
     * @return Collection<int, string>
     */
    public function itemTypes(): Collection
    {
        return ItemType::query()
            ->where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    public function createItemType(string $name): ItemType
    {
        return DB::transaction(function () use ($name): ItemType {
            $itemType = ItemType::query()->create([
                'name' => str($name)->squish()->title()->value(),
                'status' => 'active',
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);

            app(AuditTrailService::class)->record(
                self::MODULE,
                'item_type_created',
                $itemType,
                null,
                $itemType->getAttributes(),
                'Item type created: '.$itemType->name
            );

            return $itemType;
        });
    }

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): Item
    {
        return DB::transaction(function () use ($data): Item {
            $item = Item::query()->create($this->payload($data));

            app(AuditTrailService::class)->record(
                self::MODULE,
                'created',
                $item,
                null,
                $item->fresh()->getAttributes(),
                'Item created: '.$item->item_name
            );

            return $item;
        });
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(Item $item, array $data): Item
    {
        return DB::transaction(function () use ($item, $data): Item {
            $oldValues = $item->getOriginal();

            $item->update($this->payload($data, false));
            $item->refresh();

            app(AuditTrailService::class)->record(
                self::MODULE,
                'updated',
                $item,
                $oldValues,
                $item->getAttributes(),
                'Item updated: '.$item->item_name
            );

            return $item;
        });
    }

    public function markDeleted(Item $item): void
    {
        DB::transaction(function () use ($item): void {
            $oldValues = $item->getOriginal();

            if ($item->item_image) {
                Storage::disk('public')->delete($item->item_image);
                $item->forceFill(['item_image' => null])->save();
            }

            $item->delete();

            app(AuditTrailService::class)->record(
                self::MODULE,
                'deleted',
                $item,
                $oldValues,
                $item->fresh()?->getAttributes(),
                'Item deleted: '.$item->item_name
            );
        });
    }

    public function restore(Item $item): void
    {
        DB::transaction(function () use ($item): void {
            $oldValues = $item->getOriginal();
            $item->restore();
            $item->refresh();

            app(AuditTrailService::class)->record(
                self::MODULE,
                'restored',
                $item,
                $oldValues,
                $item->getAttributes(),
                'Item restored: '.$item->item_name
            );
        });
    }

    public function forceDelete(Item $item): void
    {
        DB::transaction(function () use ($item): void {
            $oldValues = $item->getOriginal();

            app(AuditTrailService::class)->record(
                self::MODULE,
                'force_deleted',
                $item,
                $oldValues,
                null,
                'Item permanently deleted: '.$item->item_name
            );

            if ($item->item_image) {
                Storage::disk('public')->delete($item->item_image);
            }

            $item->forceDelete();
        });
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function payload(array $data, bool $creating = true): array
    {
        $payload = Arr::only($data, [
            'item_name',
            'item_code',
            'item_type',
            'item_source',
            'supplier_id',
            'supplier_price',
            'percentage',
            'item_price',
            'available_stock',
            'reorder_point',
            'taxable',
            'item_image',
            'status',
            'created_by',
            'updated_by',
        ]);

        $payload['item_code'] = strtoupper((string) ($payload['item_code'] ?? ''));
        $payload['item_source'] = (string) ($payload['item_source'] ?? 'local');
        $payload['supplier_id'] = (int) ($payload['supplier_id'] ?? 0);
        $payload['supplier_price'] = (float) ($payload['supplier_price'] ?? 0);
        $payload['percentage'] = (float) ($payload['percentage'] ?? 0);
        $payload['item_price'] = app(ItemPricingService::class)->compute($payload['supplier_price'], $payload['percentage']);
        $payload['available_stock'] = (int) ($payload['available_stock'] ?? 0);
        $payload['reorder_point'] = (int) ($payload['reorder_point'] ?? 0);

        if (! $creating) {
            unset($payload['created_by']);
        }

        return $payload;
    }
}
