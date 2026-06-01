<?php

namespace App\Modules\Sales\Services;

use App\Modules\AuditTrail\Services\AuditTrailService;
use App\Modules\BusinessPartners\Models\BusinessPartner;
use App\Modules\Items\Models\Item;
use App\Modules\Items\Services\ItemPricingService;
use App\Modules\Quotations\Models\Quotation;
use App\Modules\Quotations\Models\UnitMeasure;
use App\Modules\Sales\Models\SalesOrder;
use App\Modules\Sales\Models\SalesOrderItem;
use App\Modules\Sales\Models\DeliveryReceiptItem;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SalesOrderService
{
    private const MODULE = 'sales-orders';

    public function paginate(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->filteredQuery($filters)
            ->latest('id')
            ->paginate($perPage);
    }

    public function statusCounts(array $filters = []): array
    {
        $base = $this->filteredQuery(array_merge($filters, ['status' => '']));
        $grouped = (clone $base)
            ->select('status', DB::raw('COUNT(*) as aggregate'))
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        return [
            'all' => (clone $base)->count(),
            'pending' => (int) ($grouped['pending'] ?? 0),
            'partial' => (int) ($grouped['partial'] ?? 0),
            'served' => (int) ($grouped['served'] ?? 0),
        ];
    }

    public function nextSalesOrderNo(): string
    {
        return DB::transaction(function (): string {
            $prefix = 'SO'.now()->format('y-m').'-';
            $last = SalesOrder::query()
                ->where('sales_order_no', 'like', $prefix.'%')
                ->lockForUpdate()
                ->orderByDesc('id')
                ->value('sales_order_no');

            $next = $last ? ((int) Str::afterLast($last, '-')) + 1 : 1;

            return $prefix.str_pad((string) $next, 3, '0', STR_PAD_LEFT);
        });
    }

    public function deliveryDate(string $orderDate, int|string $days): string
    {
        return \Illuminate\Support\Carbon::parse($orderDate)->addDays((int) $days)->toDateString();
    }

    public function totals(array $items, float|int|string $taxRate): array
    {
        $subtotal = collect($items)->sum(fn (array $row) => (float) ($row['price'] ?? 0) * (float) ($row['order_quantity'] ?? 0));
        $taxAmount = round($subtotal * ((float) $taxRate) / 100, 2);

        return [
            'subtotal' => round($subtotal, 2),
            'tax_amount' => $taxAmount,
            'total_amount' => round($subtotal + $taxAmount, 2),
        ];
    }

    public function create(array $data): SalesOrder
    {
        Gate::authorize('create', SalesOrder::class);

        return DB::transaction(function () use ($data): SalesOrder {
            $data['sales_order_no'] = $this->nextSalesOrderNo();
            $salesOrder = SalesOrder::query()->create($this->headerPayload($data));
            $this->syncItems($salesOrder, $data['items']);
            $this->syncQuotationReference($salesOrder, null, $data['quotation_id'] ?? null);
            $this->refreshStatusFromBalances($salesOrder);

            app(AuditTrailService::class)->record(self::MODULE, 'created', $salesOrder, null, $salesOrder->load('items')->toArray(), 'Sales order created: '.$salesOrder->sales_order_no);

            return $salesOrder->refresh();
        });
    }

    public function update(SalesOrder $salesOrder, array $data): SalesOrder
    {
        Gate::authorize('update', $salesOrder);

        return DB::transaction(function () use ($salesOrder, $data): SalesOrder {
            $old = $salesOrder->load('items')->toArray();

            if ($this->hasIssuedDeliveryReceipt($salesOrder)) {
                $allowedHeader = [
                    'po_attachment' => $data['po_attachment'] ?? $salesOrder->po_attachment,
                    'updated_by' => $data['updated_by'] ?? auth()->id(),
                ];
                $salesOrder->update($allowedHeader);
                app(AuditTrailService::class)->record(self::MODULE, 'attachment_updated', $salesOrder, $old, $salesOrder->fresh()->toArray(), 'Sales order attachment updated: '.$salesOrder->sales_order_no);

                return $salesOrder->refresh();
            }

            $previousQuotationId = $salesOrder->quotation_id;
            $salesOrder->update($this->headerPayload($data, false));
            $this->syncItems($salesOrder, $data['items']);
            $this->syncQuotationReference($salesOrder, $previousQuotationId, $data['quotation_id'] ?? null);
            $this->refreshStatusFromBalances($salesOrder);

            app(AuditTrailService::class)->record(self::MODULE, 'updated', $salesOrder, $old, $salesOrder->load('items')->toArray(), 'Sales order updated: '.$salesOrder->sales_order_no);

            return $salesOrder->refresh();
        });
    }

    public function hasIssuedDeliveryReceipt(SalesOrder $salesOrder): bool
    {
        return DeliveryReceiptItem::query()
            ->whereHas('salesOrderItem', fn (Builder $query) => $query->where('sales_order_id', $salesOrder->id))
            ->whereHas('deliveryReceipt', fn (Builder $query) => $query->where('status', '!=', 'cancelled'))
            ->exists();
    }

    public function delete(SalesOrder $salesOrder): void
    {
        Gate::authorize('delete', $salesOrder);

        DB::transaction(function () use ($salesOrder): void {
            $old = $salesOrder->load('items')->toArray();
            $salesOrder->delete();
            app(AuditTrailService::class)->record(self::MODULE, 'deleted', $salesOrder, $old, null, 'Sales order deleted: '.$salesOrder->sales_order_no);
        });
    }

    public function status(SalesOrder $salesOrder, string $status): SalesOrder
    {
        Gate::authorize('update', $salesOrder);

        return DB::transaction(function () use ($salesOrder, $status): SalesOrder {
            $old = $salesOrder->getOriginal();
            $salesOrder->update(['status' => $status, 'updated_by' => auth()->id()]);
            app(AuditTrailService::class)->record(self::MODULE, 'status_'.$status, $salesOrder, $old, $salesOrder->getAttributes(), 'Sales order status changed: '.$salesOrder->sales_order_no);

            return $salesOrder->refresh();
        });
    }

    public function createQuickItem(array $data): Item
    {
        return DB::transaction(function () use ($data): Item {
            $price = app(ItemPricingService::class)->compute($data['supplier_price'], $data['percentage']);
            $supplierId = BusinessPartner::query()->suppliers()->value('id');

            $item = Item::query()->create([
                'item_name' => $data['item_name'],
                'item_code' => strtoupper($data['item_code'] ?? 'SO-'.Str::upper(Str::random(8))),
                'item_type' => $data['item_type'] ?? 'Product Consumable',
                'item_source' => $data['item_source'],
                'supplier_id' => $supplierId,
                'supplier_price' => (float) $data['supplier_price'],
                'percentage' => (float) $data['percentage'],
                'item_price' => $price,
                'available_stock' => 0,
                'reorder_point' => 0,
                'taxable' => 'no',
                'status' => 'active',
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);

            app(AuditTrailService::class)->record(self::MODULE, 'quick_item_created', $item, null, $item->getAttributes(), 'Item created from sales order: '.$item->item_name);

            return $item;
        });
    }

    public function quotationRows(Quotation $quotation): array
    {
        $quotation->load(['businessPartner', 'items.item:id,item_name,item_code,item_price,available_stock', 'items.unitMeasure']);

        return [
            'customer' => [
                'business_partner_id' => (string) $quotation->business_partner_id,
                'terms' => (int) ($quotation->businessPartner?->terms ?? 30),
                'company_address' => (string) $quotation->company_address,
                'contact_person' => (string) $quotation->contact_person,
                'contact_no' => (string) $quotation->contact_no,
                'quotation_id' => (string) $quotation->id,
                'currency' => $quotation->currency,
            ],
            'items' => $quotation->items->map(fn ($row): array => [
                'item_id' => (string) $row->item_id,
                'description' => (string) $row->description,
                'order_quantity' => (string) (float) $row->quantity,
                'unit_measure_id' => (string) $row->unit_measure_id,
                'price' => number_format((float) $row->item_price, 2, '.', ''),
                'available_stock' => number_format((float) ($row->item?->available_stock ?? 0), 2, '.', ''),
                'remarks' => '',
                'total' => number_format((float) $row->item_price * (float) $row->quantity, 2, '.', ''),
            ])->values()->all(),
        ];
    }

    public function clients(): Collection
    {
        return BusinessPartner::query()->clients()->where('status', 'active')->orderBy('company_name')->get(['id', 'company_name', 'terms', 'company_address', 'contact_person', 'contact_no']);
    }

    public function activeItems(): Collection
    {
        return Item::query()->where('status', 'active')->orderBy('item_name')->get(['id', 'item_name', 'item_code', 'item_price', 'available_stock']);
    }

    public function unitMeasures(): Collection
    {
        return UnitMeasure::query()->where('status', 'active')->orderBy('name')->get(['id', 'name']);
    }

    public function sourceQuotations(?int $currentQuotationId = null): Collection
    {
        return Quotation::query()
            ->with('businessPartner:id,company_name')
            ->where(function (Builder $query) use ($currentQuotationId): void {
                $query->whereNull('reference_sales_order_id');
                if ($currentQuotationId) {
                    $query->orWhere('id', $currentQuotationId);
                }
            })
            ->latest('id')
            ->get(['id', 'quotation_no', 'business_partner_id', 'status']);
    }

    private function syncQuotationReference(SalesOrder $salesOrder, int|string|null $previousQuotationId, int|string|null $currentQuotationId): void
    {
        $currentId = $currentQuotationId ? (int) $currentQuotationId : null;

        if (! $currentId) {
            return;
        }

        $quotation = Quotation::query()->find($currentId);
        if (! $quotation) {
            throw ValidationException::withMessages([
                'selected_quotation_id' => 'Selected quotation does not exist.',
            ]);
        }

        if ($quotation->reference_sales_order_id && (int) $quotation->reference_sales_order_id !== (int) $salesOrder->id) {
            throw ValidationException::withMessages([
                'selected_quotation_id' => 'Quotation is already used as reference.',
            ]);
        }

        if ((int) $quotation->reference_sales_order_id !== (int) $salesOrder->id) {
            $quotation->update([
                'reference_sales_order_id' => $salesOrder->id,
                'updated_by' => auth()->id(),
            ]);
        }
    }

    private function headerPayload(array $data, bool $creating = true): array
    {
        $totals = $this->totals($data['items'], $data['tax_rate']);

        return array_merge([
            'order_date' => $data['order_date'],
            'no_of_days' => (int) $data['no_of_days'],
            'delivery_date' => $data['delivery_date'],
            'customer_po' => $data['customer_po'] ?? null,
            'agent_name' => $data['agent_name'],
            'remarks' => $data['remarks'] ?? null,
            'business_partner_id' => $data['business_partner_id'],
            'terms' => (int) ($data['terms'] ?? 30),
            'company_address' => $data['company_address'] ?? null,
            'contact_person' => $data['contact_person'] ?? null,
            'contact_no' => $data['contact_no'] ?? null,
            'quotation_id' => $data['quotation_id'] ?? null,
            'currency' => $data['currency'],
            'tax_rate' => (float) $data['tax_rate'],
            'po_attachment' => $data['po_attachment'] ?? null,
            'updated_by' => $data['updated_by'] ?? auth()->id(),
        ], $totals, $creating ? [
            'sales_order_no' => $data['sales_order_no'],
            'status' => 'pending',
            'created_by' => $data['created_by'] ?? auth()->id(),
        ] : []);
    }

    private function syncItems(SalesOrder $salesOrder, array $items): void
    {
        $existingRows = $salesOrder->items()->get()->keyBy('id');
        $retainIds = [];

        foreach ($items as $row) {
            $price = (float) $row['price'];
            $qty = (float) $row['order_quantity'];
            $payload = [
                'item_id' => $row['item_id'],
                'description' => $row['description'] ?? null,
                'order_quantity' => $qty,
                'unit_measure_id' => $row['unit_measure_id'],
                'price' => $price,
                'available_stock' => (float) ($row['available_stock'] ?? 0),
                'remarks' => $row['remarks'] ?? null,
                'total' => round($price * $qty, 2),
            ];

            $rowId = isset($row['id']) ? (int) $row['id'] : 0;
            if ($rowId > 0 && $existingRows->has($rowId)) {
                /** @var SalesOrderItem $existing */
                $existing = $existingRows->get($rowId);
                $oldQty = (float) $existing->order_quantity;
                $oldBalance = (float) ($existing->balance_quantity ?? $oldQty);
                $deliveredQty = max($oldQty - $oldBalance, 0);
                $payload['balance_quantity'] = max(round($qty - $deliveredQty, 2), 0);
                $existing->update($payload);
                $retainIds[] = $existing->id;
                continue;
            }

            $payload['balance_quantity'] = max(round($qty, 2), 0);
            $created = $salesOrder->items()->create($payload);
            $retainIds[] = $created->id;
        }

        if ($retainIds === []) {
            $salesOrder->items()->delete();
            return;
        }

        $salesOrder->items()->whereNotIn('id', $retainIds)->delete();
    }

    public function refreshStatusFromBalances(SalesOrder $salesOrder): SalesOrder
    {
        $salesOrder->loadMissing('items');

        $totalOrdered = (float) $salesOrder->items->sum(fn (SalesOrderItem $row) => (float) $row->order_quantity);
        $totalBalance = (float) $salesOrder->items->sum(fn (SalesOrderItem $row) => (float) ($row->balance_quantity ?? $row->order_quantity));

        $status = 'pending';
        if ($totalOrdered > 0 && $totalBalance <= 0) {
            $status = 'served';
        } elseif ($totalOrdered > 0 && $totalBalance < $totalOrdered) {
            $status = 'partial';
        }

        if ($salesOrder->status !== $status) {
            $salesOrder->update(['status' => $status, 'updated_by' => auth()->id()]);
        }

        return $salesOrder->refresh();
    }

    private function filteredQuery(array $filters = []): Builder
    {
        return SalesOrder::query()
            ->with([
                'businessPartner:id,company_name,type',
                'creator:id,name',
                'items:id,sales_order_id,item_id,unit_measure_id,order_quantity,balance_quantity,total',
                'items.item:id,item_name',
                'items.unitMeasure:id,name',
                'items.deliveryReceiptItems:id,delivery_receipt_id,sales_order_item_id,delivered_quantity,delivery_no,delivered_date,delivered_by,received_by',
                'items.deliveryReceiptItems.deliveryReceipt:id,delivery_receipt_no,status,dr_date,received_date,received_by,delivered_by',
            ])
            ->when($filters['search'] ?? null, function (Builder $query, string $search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('sales_order_no', 'like', "%{$search}%")
                        ->orWhere('customer_po', 'like', "%{$search}%")
                        ->orWhereHas('businessPartner', fn (Builder $partner) => $partner->where('company_name', 'like', "%{$search}%"));
                });
            })
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status))
            ->when($filters['currency'] ?? null, fn (Builder $query, string $currency) => $query->where('currency', $currency))
            ->when($filters['date_from'] ?? null, fn (Builder $query, string $date) => $query->whereDate('order_date', '>=', $date))
            ->when($filters['date_to'] ?? null, fn (Builder $query, string $date) => $query->whereDate('order_date', '<=', $date));
    }
}
