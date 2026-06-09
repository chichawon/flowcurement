<?php

namespace App\Modules\Purchasing\Services;

use App\Modules\AuditTrail\Services\AuditTrailService;
use App\Modules\BusinessPartners\Models\BusinessPartner;
use App\Modules\Items\Models\Item;
use App\Modules\Purchasing\Models\PurchaseOrder;
use App\Modules\Purchasing\Models\PurchaseOrderItem;
use App\Modules\Quotations\Models\UnitMeasure;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class PurchaseOrderService
{
    private const MODULE = 'purchase-orders';

    public function paginate(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return PurchaseOrder::query()
            ->with(['supplier:id,company_name', 'creator:id,name'])
            ->withCount('items')
            ->when($filters['search'] ?? null, function (Builder $query, string $search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('purchase_order_no', 'like', "%{$search}%")
                        ->orWhere('supplier_name', 'like', "%{$search}%");
                });
            })
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status))
            ->when($filters['currency'] ?? null, fn (Builder $query, string $currency) => $query->where('currency', $currency))
            ->when($filters['date_from'] ?? null, fn (Builder $query, string $date) => $query->whereDate('purchase_order_date', '>=', $date))
            ->when($filters['date_to'] ?? null, fn (Builder $query, string $date) => $query->whereDate('purchase_order_date', '<=', $date))
            ->latest('id')
            ->paginate($perPage);
    }

    public function nextPurchaseOrderNo(): string
    {
        return DB::transaction(function (): string {
            $last = PurchaseOrder::query()
                ->whereNotNull('purchase_order_no')
                ->lockForUpdate()
                ->orderByDesc('id')
                ->value('purchase_order_no');

            if ($last && preg_match('/^(.*?)(\d+)$/', $last, $matches)) {
                $prefix = $matches[1];
                $number = $matches[2];
                $next = (int) $number + 1;

                return $prefix.str_pad((string) $next, strlen($number), '0', STR_PAD_LEFT);
            }

            return 'PO'.now()->format('y-m').'-001';
        });
    }

    public function suppliers(): Collection
    {
        return BusinessPartner::query()->suppliers()->where('status', 'active')->orderBy('company_name')->get(['id', 'company_name', 'company_address', 'contact_person', 'contact_no', 'terms']);
    }

    public function items(?int $supplierId = null): Collection
    {
        return Item::query()
            ->where('status', 'active')
            ->when($supplierId, fn (Builder $query) => $query->where('supplier_id', $supplierId))
            ->orderBy('item_name')
            ->get(['id', 'item_name', 'item_code', 'supplier_id', 'supplier_price', 'item_price', 'item_image']);
    }

    public function unitMeasures(): Collection
    {
        return UnitMeasure::query()->where('status', 'active')->orderBy('name')->get(['id', 'name']);
    }

    public function supplierDetails(int $supplierId): ?array
    {
        $supplier = BusinessPartner::query()->suppliers()->find($supplierId);
        if (! $supplier) {
            return null;
        }

        return [
            'supplier_id' => $supplier->id,
            'supplier_name' => $supplier->company_name,
            'supplier_address' => (string) $supplier->company_address,
            'contact_person' => (string) $supplier->contact_person,
            'contact_no' => (string) $supplier->contact_no,
            'terms' => (string) $supplier->terms,
        ];
    }

    public function totals(array $items, float|int|string $taxRate): array
    {
        $subtotal = collect($items)->sum(fn (array $row): float => (float) ($row['quantity'] ?? 0) * (float) ($row['price'] ?? 0));
        $taxAmount = round($subtotal * ((float) $taxRate / 100), 2);

        return [
            'subtotal' => round($subtotal, 2),
            'tax_amount' => $taxAmount,
            'total_amount' => round($subtotal + $taxAmount, 2),
        ];
    }

    public function create(array $payload): PurchaseOrder
    {
        Gate::authorize('create', PurchaseOrder::class);

        return DB::transaction(function () use ($payload): PurchaseOrder {
            $payload['purchase_order_no'] = trim((string) ($payload['purchase_order_no'] ?? '')) ?: $this->nextPurchaseOrderNo();
            $order = PurchaseOrder::query()->create($this->headerPayload($payload));
            $this->syncItems($order, $payload['items'] ?? []);
            app(AuditTrailService::class)->record(self::MODULE, 'created', $order, null, $order->load('items')->toArray(), 'Purchase order created: '.$order->purchase_order_no);

            return $order->refresh();
        });
    }

    public function update(PurchaseOrder $order, array $payload): PurchaseOrder
    {
        Gate::authorize('update', $order);

        return DB::transaction(function () use ($order, $payload): PurchaseOrder {
            $old = $order->load('items')->toArray();
            $payload['purchase_order_no'] = trim((string) ($payload['purchase_order_no'] ?? $order->purchase_order_no));
            $order->update($this->headerPayload($payload, false));
            $order->items()->delete();
            $this->syncItems($order, $payload['items'] ?? []);
            app(AuditTrailService::class)->record(self::MODULE, 'updated', $order, $old, $order->load('items')->toArray(), 'Purchase order updated: '.$order->purchase_order_no);

            return $order->refresh();
        });
    }

    public function cancel(PurchaseOrder $order): PurchaseOrder
    {
        Gate::authorize('cancel', $order);

        $old = $order->toArray();
        $order->update(['status' => 'cancelled', 'updated_by' => auth()->id()]);
        app(AuditTrailService::class)->record(self::MODULE, 'cancelled', $order, $old, $order->toArray(), 'Purchase order cancelled: '.$order->purchase_order_no);

        return $order->refresh();
    }

    private function headerPayload(array $payload, bool $creating = true): array
    {
        $supplier = $this->supplierDetails((int) $payload['supplier_id']);
        $totals = $this->totals($payload['items'] ?? [], $payload['tax_rate'] ?? 0);

        $header = [
            'purchase_order_no' => $payload['purchase_order_no'] ?? null,
            'purchase_order_date' => $payload['purchase_order_date'],
            'expected_delivery_date' => $payload['expected_delivery_date'] ?: null,
            'supplier_id' => $payload['supplier_id'],
            'supplier_name' => $supplier['supplier_name'] ?? '',
            'supplier_address' => $supplier['supplier_address'] ?? '',
            'contact_person' => $supplier['contact_person'] ?? '',
            'contact_no' => $supplier['contact_no'] ?? '',
            'terms' => $supplier['terms'] ?? '',
            'remarks' => $payload['remarks'] ?? null,
            'currency' => $payload['currency'] ?? 'php',
            'tax_rate' => $payload['tax_rate'] ?? 0,
            'subtotal' => $totals['subtotal'],
            'tax_amount' => $totals['tax_amount'],
            'total_amount' => $totals['total_amount'],
            'status' => $payload['status'] ?? 'pending',
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ];

        if (! $creating) {
            unset($header['created_by']);
        }

        return $header;
    }

    private function syncItems(PurchaseOrder $order, array $items): void
    {
        foreach ($items as $row) {
            $subtotal = round((float) $row['quantity'] * (float) $row['price'], 2);
            $taxAmount = round($subtotal * ((float) $order->tax_rate / 100), 2);
            PurchaseOrderItem::query()->create([
                'purchase_order_id' => $order->id,
                'item_id' => $row['item_id'],
                'description' => $row['description'] ?? null,
                'lead_time' => $row['lead_time'] ?? null,
                'unit_measure_id' => $row['unit_measure_id'],
                'quantity' => $row['quantity'],
                'price' => $row['price'],
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total' => round($subtotal + $taxAmount, 2),
                'remarks' => $row['remarks'] ?? null,
            ]);
        }
    }
}
