<?php

namespace App\Modules\Quotations\Services;

use App\Modules\AuditTrail\Services\AuditTrailService;
use App\Modules\BusinessPartners\Models\BusinessPartner;
use App\Modules\Items\Models\Item;
use App\Modules\Items\Services\ItemPricingService;
use App\Modules\Quotations\Models\Quotation;
use App\Modules\Quotations\Models\UnitMeasure;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class QuotationService
{
    private const MODULE = 'quotations';

    public function paginate(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return Quotation::query()
            ->with([
                'businessPartner:id,company_name,type,contact_no',
                'preparedBy:id,name',
                'referenceSalesOrder:id,sales_order_no',
                'items:id,quotation_id,item_id,description,lead_time,unit_measure_id,item_price,quantity,total',
                'items.item:id,item_name,item_image',
                'items.unitMeasure:id,name',
            ])
            ->when($filters['search'] ?? null, function (Builder $query, string $search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('quotation_no', 'like', "%{$search}%")
                        ->orWhereHas('businessPartner', fn (Builder $partner) => $partner->where('company_name', 'like', "%{$search}%"));
                });
            })
            ->when($filters['currency'] ?? null, fn (Builder $query, string $currency) => $query->where('currency', $currency))
            ->when($filters['date_from'] ?? null, fn (Builder $query, string $date) => $query->whereDate('quotation_date', '>=', $date))
            ->when($filters['date_to'] ?? null, fn (Builder $query, string $date) => $query->whereDate('quotation_date', '<=', $date))
            ->latest('id')
            ->paginate($perPage);
    }

    public function nextQuotationNo(): string
    {
        $prefix = 'Q'.now()->format('y-m');
        $last = Quotation::query()
            ->where('quotation_no', 'like', "{$prefix}-%")
            ->lockForUpdate()
            ->orderByDesc('id')
            ->value('quotation_no');

        $next = $last ? ((int) str($last)->afterLast('-')->value()) + 1 : 1;

        return $prefix.'-'.str_pad((string) $next, 3, '0', STR_PAD_LEFT);
    }

    public function totals(array $items, float|int|string $taxRate): array
    {
        $subtotal = collect($items)->sum(fn (array $row) => (float) ($row['item_price'] ?? 0) * (float) ($row['quantity'] ?? 0));
        $taxAmount = round($subtotal * ((float) $taxRate) / 100, 2);

        return [
            'subtotal' => round($subtotal, 2),
            'tax_amount' => $taxAmount,
            'total_amount' => round($subtotal + $taxAmount, 2),
        ];
    }

    public function create(array $data): Quotation
    {
        Gate::authorize('create', Quotation::class);

        return DB::transaction(function () use ($data): Quotation {
            $data['quotation_no'] = $this->nextQuotationNo();
            $quotation = Quotation::query()->create($this->headerPayload($data));
            $this->syncItems($quotation, $data['items']);

            app(AuditTrailService::class)->record(self::MODULE, 'created', $quotation, null, $quotation->load('items')->toArray(), 'Quotation created: '.$quotation->quotation_no);

            return $quotation->refresh();
        });
    }

    public function update(Quotation $quotation, array $data): Quotation
    {
        Gate::authorize('update', $quotation);

        return DB::transaction(function () use ($quotation, $data): Quotation {
            $old = $quotation->load('items')->toArray();
            $quotation->update($this->headerPayload($data, false));
            $this->syncItems($quotation, $data['items']);

            app(AuditTrailService::class)->record(self::MODULE, 'updated', $quotation, $old, $quotation->load('items')->toArray(), 'Quotation updated: '.$quotation->quotation_no);

            return $quotation->refresh();
        });
    }

    public function delete(Quotation $quotation): void
    {
        Gate::authorize('delete', $quotation);

        DB::transaction(function () use ($quotation): void {
            $old = $quotation->load('items')->toArray();
            $quotation->delete();
            app(AuditTrailService::class)->record(self::MODULE, 'deleted', $quotation, $old, null, 'Quotation deleted: '.$quotation->quotation_no);
        });
    }

    public function status(Quotation $quotation, string $status): Quotation
    {
        Gate::authorize($status === 'approved' ? 'approve' : 'update', $quotation);

        return DB::transaction(function () use ($quotation, $status): Quotation {
            $old = $quotation->getOriginal();
            $quotation->update(['status' => $status, 'updated_by' => auth()->id()]);
            app(AuditTrailService::class)->record(self::MODULE, 'status_'.$status, $quotation, $old, $quotation->getAttributes(), 'Quotation status changed: '.$quotation->quotation_no);

            return $quotation->refresh();
        });
    }

    public function clients(): Collection
    {
        return BusinessPartner::query()->clients()->where('status', 'active')->orderBy('company_name')->get(['id', 'company_name', 'company_address', 'contact_person', 'contact_no', 'agent_name']);
    }

    public function activeItems(): Collection
    {
        return Item::query()->where('status', 'active')->orderBy('item_name')->get(['id', 'item_name', 'item_code', 'item_price', 'item_image']);
    }

    public function unitMeasures(): Collection
    {
        return UnitMeasure::query()->where('status', 'active')->orderBy('name')->get(['id', 'name']);
    }

    public function createQuickItem(array $data): Item
    {
        return DB::transaction(function () use ($data): Item {
            $price = app(ItemPricingService::class)->compute($data['supplier_price'], $data['percentage']);
            $supplierId = BusinessPartner::query()->suppliers()->value('id');

            $item = Item::query()->create([
                'item_name' => $data['item_name'],
                'item_code' => strtoupper($data['item_code'] ?? 'QT-'.Str::upper(Str::random(8))),
                'item_type' => $data['item_type'] ?? 'Product Consumable',
                'item_source' => $data['item_source'],
                'supplier_id' => $supplierId,
                'supplier_price' => (float) $data['supplier_price'],
                'percentage' => (float) $data['percentage'],
                'item_price' => $price,
                'item_image' => $data['item_image'] ?? null,
                'available_stock' => 0,
                'reorder_point' => 0,
                'taxable' => 'no',
                'status' => 'active',
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);

            app(AuditTrailService::class)->record(self::MODULE, 'quick_item_created', $item, null, $item->getAttributes(), 'Item created from quotation: '.$item->item_name);

            return $item;
        });
    }

    private function headerPayload(array $data, bool $creating = true): array
    {
        $totals = $this->totals($data['items'], $data['tax_rate']);

        return array_merge([
            'quotation_date' => $data['quotation_date'],
            'validity_date' => $data['validity_date'],
            'business_partner_id' => $data['business_partner_id'],
            'company_address' => $data['company_address'] ?? null,
            'contact_person' => $data['contact_person'] ?? null,
            'contact_no' => $data['contact_no'] ?? null,
            'agent_name' => $data['agent_name'],
            'prepared_by' => $data['prepared_by'],
            'remarks' => $data['remarks'] ?? null,
            'currency' => $data['currency'],
            'tax_rate' => (float) $data['tax_rate'],
            'status' => $data['status'] ?? 'draft',
            'updated_by' => $data['updated_by'] ?? auth()->id(),
        ], $totals, $creating ? [
            'quotation_no' => $data['quotation_no'],
            'created_by' => $data['created_by'] ?? auth()->id(),
        ] : []);
    }

    private function syncItems(Quotation $quotation, array $items): void
    {
        $quotation->items()->delete();

        foreach ($items as $row) {
            $price = (float) $row['item_price'];
            $qty = (float) $row['quantity'];
            $quotation->items()->create([
                'item_id' => $row['item_id'],
                'description' => $row['description'] ?? null,
                'lead_time' => $row['lead_time'] ?? null,
                'unit_measure_id' => $row['unit_measure_id'],
                'item_price' => $price,
                'quantity' => $qty,
                'total' => round($price * $qty, 2),
            ]);
        }
    }
}
