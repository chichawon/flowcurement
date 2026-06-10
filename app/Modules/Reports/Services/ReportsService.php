<?php

namespace App\Modules\Reports\Services;

use App\Modules\AuditTrail\Models\AuditTrail;
use App\Modules\Items\Models\Item;
use App\Modules\Purchasing\Models\PurchaseInvoice;
use App\Modules\Purchasing\Models\PurchaseOrder;
use App\Modules\Sales\Models\DeliveryReceipt;
use App\Modules\Sales\Models\SalesCollection;
use App\Modules\Sales\Models\SalesInvoice;
use App\Modules\Sales\Models\SalesOrder;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class ReportsService
{
    public function summary(string $dateFrom, string $dateTo): array
    {
        [$from, $to] = $this->dateBounds($dateFrom, $dateTo);

        $collections = SalesCollection::query()
            ->whereBetween('collection_receipt_date', [$from->toDateString(), $to->toDateString()]);

        $salesInvoices = SalesInvoice::query()
            ->whereBetween('invoice_date', [$from->toDateString(), $to->toDateString()]);

        $purchaseInvoices = PurchaseInvoice::query()
            ->whereBetween('invoice_date', [$from->toDateString(), $to->toDateString()]);

        return [
            'total_collections' => (float) (clone $collections)->sum('applied_amount'),
            'collection_count' => (clone $collections)->count(),
            'sales_invoice_total' => (float) (clone $salesInvoices)->sum('total_amount'),
            'unpaid_invoice_balance' => (float) (clone $salesInvoices)->where('status', 'unpaid')->sum('balance_amount'),
            'sales_order_total' => (float) SalesOrder::query()->whereBetween('order_date', [$from->toDateString(), $to->toDateString()])->sum('total_amount'),
            'delivery_receipt_count' => DeliveryReceipt::query()->whereBetween('dr_date', [$from->toDateString(), $to->toDateString()])->count(),
            'purchase_order_total' => (float) PurchaseOrder::query()->whereBetween('purchase_order_date', [$from->toDateString(), $to->toDateString()])->sum('total_amount'),
            'purchase_invoice_total' => (float) (clone $purchaseInvoices)->sum('total_amount'),
            'purchase_payable_balance' => (float) (clone $purchaseInvoices)->sum('balance_amount'),
            'low_stock_count' => Item::query()->where('available_stock', '>', 0)->whereColumn('available_stock', '<=', 'reorder_point')->count(),
            'out_of_stock_count' => Item::query()->where('available_stock', '<=', 0)->count(),
        ];
    }

    public function monthlyCollections(int $year): array
    {
        $rows = SalesCollection::query()
            ->selectRaw('MONTH(collection_receipt_date) as month_no, SUM(applied_amount) as amount')
            ->whereYear('collection_receipt_date', $year)
            ->groupBy('month_no')
            ->pluck('amount', 'month_no');

        return collect(range(1, 12))
            ->map(fn (int $month) => [
                'label' => CarbonImmutable::create($year, $month, 1)->format('M'),
                'amount' => (float) ($rows[$month] ?? 0),
            ])
            ->all();
    }

    public function salesInvoices(string $dateFrom, string $dateTo): Collection
    {
        [$from, $to] = $this->dateBounds($dateFrom, $dateTo);

        return SalesInvoice::query()
            ->whereBetween('invoice_date', [$from->toDateString(), $to->toDateString()])
            ->latest('invoice_date')
            ->latest('id')
            ->limit(8)
            ->get();
    }

    public function purchasingSummary(string $dateFrom, string $dateTo): Collection
    {
        [$from, $to] = $this->dateBounds($dateFrom, $dateTo);

        return PurchaseOrder::query()
            ->whereBetween('purchase_order_date', [$from->toDateString(), $to->toDateString()])
            ->latest('purchase_order_date')
            ->latest('id')
            ->limit(8)
            ->get();
    }

    public function inventoryAlerts(): Collection
    {
        return Item::query()
            ->with('supplier:id,company_name')
            ->where(function (Builder $query): void {
                $query->where('available_stock', '<=', 0)
                    ->orWhereColumn('available_stock', '<=', 'reorder_point');
            })
            ->orderBy('available_stock')
            ->orderBy('item_name')
            ->limit(8)
            ->get();
    }

    public function auditTrails(array $filters, int $perPage): LengthAwarePaginator
    {
        $search = trim((string) ($filters['search'] ?? ''));
        [$from, $to] = $this->dateBounds((string) ($filters['date_from'] ?? ''), (string) ($filters['date_to'] ?? ''));

        return AuditTrail::query()
            ->with('actor:id,name')
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $nested) use ($search): void {
                    $nested->where('module', 'like', "%{$search}%")
                        ->orWhere('action', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereHas('actor', fn (Builder $actor) => $actor->where('name', 'like', "%{$search}%"));
                });
            })
            ->whereBetween('created_at', [$from->startOfDay(), $to->endOfDay()])
            ->latest('created_at')
            ->paginate($perPage);
    }

    public function topBusinessPartners(array $filters, int $perPage): LengthAwarePaginator
    {
        $search = trim((string) ($filters['search'] ?? ''));
        [$from, $to] = $this->dateBounds((string) ($filters['date_from'] ?? ''), (string) ($filters['date_to'] ?? ''));

        return DB::table('sales_orders')
            ->leftJoin('business_partners', 'business_partners.id', '=', 'sales_orders.business_partner_id')
            ->selectRaw('
                sales_orders.business_partner_id,
                business_partners.company_name as company_name,
                COALESCE(business_partners.agent_name, sales_orders.agent_name) as agent_name,
                COALESCE(business_partners.contact_person, sales_orders.contact_person) as contact_person,
                COALESCE(business_partners.contact_no, sales_orders.contact_no) as contact_no,
                COUNT(sales_orders.id) as order_count,
                SUM(sales_orders.total_amount) as total_order_amount,
                MAX(sales_orders.order_date) as last_order_date
            ')
            ->whereNull('sales_orders.deleted_at')
            ->whereBetween('sales_orders.order_date', [$from->toDateString(), $to->toDateString()])
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($nested) use ($search): void {
                    $nested->where('business_partners.company_name', 'like', "%{$search}%")
                        ->orWhere('sales_orders.agent_name', 'like', "%{$search}%")
                        ->orWhere('sales_orders.contact_person', 'like', "%{$search}%");
                });
            })
            ->groupBy(
                'sales_orders.business_partner_id',
                'business_partners.company_name',
                'business_partners.agent_name',
                'sales_orders.agent_name',
                'business_partners.contact_person',
                'sales_orders.contact_person',
                'business_partners.contact_no',
                'sales_orders.contact_no'
            )
            ->orderByDesc('total_order_amount')
            ->orderByDesc('order_count')
            ->paginate($perPage);
    }

    public function companyTopOrderedItems(array $filters, int $perPage): LengthAwarePaginator
    {
        $search = trim((string) ($filters['search'] ?? ''));
        [$from, $to] = $this->dateBounds((string) ($filters['date_from'] ?? ''), (string) ($filters['date_to'] ?? ''));

        return DB::table('sales_order_items')
            ->join('sales_orders', 'sales_orders.id', '=', 'sales_order_items.sales_order_id')
            ->leftJoin('business_partners', 'business_partners.id', '=', 'sales_orders.business_partner_id')
            ->leftJoin('items', 'items.id', '=', 'sales_order_items.item_id')
            ->leftJoin('unit_measures', 'unit_measures.id', '=', 'sales_order_items.unit_measure_id')
            ->selectRaw('
                sales_orders.business_partner_id,
                business_partners.company_name,
                sales_order_items.item_id,
                COALESCE(items.item_name, sales_order_items.description) as item_name,
                COALESCE(items.item_code, "-") as item_code,
                COALESCE(unit_measures.name, "-") as unit,
                COUNT(DISTINCT sales_orders.id) as order_count,
                SUM(sales_order_items.order_quantity) as ordered_quantity,
                SUM(sales_order_items.total) as total_order_amount,
                MAX(sales_orders.order_date) as last_order_date
            ')
            ->whereNull('sales_orders.deleted_at')
            ->whereBetween('sales_orders.order_date', [$from->toDateString(), $to->toDateString()])
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($nested) use ($search): void {
                    $nested->where('business_partners.company_name', 'like', "%{$search}%")
                        ->orWhere('items.item_name', 'like', "%{$search}%")
                        ->orWhere('items.item_code', 'like', "%{$search}%")
                        ->orWhere('sales_order_items.description', 'like', "%{$search}%");
                });
            })
            ->groupBy(
                'sales_orders.business_partner_id',
                'business_partners.company_name',
                'sales_order_items.item_id',
                'items.item_name',
                'sales_order_items.description',
                'items.item_code',
                'unit_measures.name'
            )
            ->orderBy('business_partners.company_name')
            ->orderByDesc('ordered_quantity')
            ->orderByDesc('total_order_amount')
            ->paginate($perPage);
    }

    public function years(): array
    {
        $currentYear = (int) now()->year;

        return range($currentYear, $currentYear - 4);
    }

    private function dateBounds(string $dateFrom, string $dateTo): array
    {
        $from = $dateFrom !== '' ? CarbonImmutable::parse($dateFrom) : now()->toImmutable()->startOfMonth();
        $to = $dateTo !== '' ? CarbonImmutable::parse($dateTo) : now()->toImmutable();

        if ($from->greaterThan($to)) {
            return [$to, $from];
        }

        return [$from, $to];
    }
}
