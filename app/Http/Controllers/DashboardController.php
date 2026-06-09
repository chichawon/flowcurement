<?php

namespace App\Http\Controllers;

use App\Modules\AuditTrail\Models\AuditTrail;
use App\Modules\BusinessPartners\Models\BusinessPartner;
use App\Modules\Items\Models\Item;
use App\Modules\Quotations\Models\Quotation;
use App\Modules\Sales\Models\DeliveryReceipt;
use App\Modules\Sales\Models\SalesCollection;
use App\Modules\Sales\Models\SalesInvoice;
use App\Modules\Sales\Models\SalesOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $now = now();
        $currentYear = (int) $now->year;
        $currentMonth = (int) $now->month;
        $logFilters = $this->logFilters($request);
        $monthLabels = collect(range(1, 12))->map(fn (int $month): string => Carbon::create($currentYear, $month, 1)->format('M'));

        $monthlyCollectionRows = SalesCollection::query()
            ->selectRaw('MONTH(collection_receipt_date) as month_no, SUM(applied_amount) as total')
            ->whereYear('collection_receipt_date', $currentYear)
            ->where('status', '!=', 'cancelled')
            ->groupBy('month_no')
            ->pluck('total', 'month_no');

        $monthlyCollections = collect(range(1, 12))->map(fn (int $month): float => (float) ($monthlyCollectionRows[$month] ?? 0));
        $maxMonthlyCollection = max($monthlyCollections->max() ?: 0, 1);

        $yearStart = $currentYear - 4;
        $yearlyCollectionRows = SalesCollection::query()
            ->selectRaw('YEAR(collection_receipt_date) as year_no, SUM(applied_amount) as total')
            ->whereYear('collection_receipt_date', '>=', $yearStart)
            ->where('status', '!=', 'cancelled')
            ->groupBy('year_no')
            ->pluck('total', 'year_no');

        $yearlyCollections = collect(range($currentYear, $yearStart))->map(fn (int $year): array => [
            'year' => $year,
            'total' => (float) ($yearlyCollectionRows[$year] ?? 0),
        ]);
        $maxYearlyCollection = max($yearlyCollections->max('total') ?: 0, 1);

        $salesOrderCounts = SalesOrder::query()
            ->select('status', DB::raw('COUNT(*) as aggregate'))
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $deliveryReceiptCounts = DeliveryReceipt::query()
            ->select('status', DB::raw('COUNT(*) as aggregate'))
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $invoiceCounts = SalesInvoice::query()
            ->select('status', DB::raw('COUNT(*) as aggregate'))
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $collectionCounts = SalesCollection::query()
            ->select('status', DB::raw('COUNT(*) as aggregate'))
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $itemCounts = [
            'all' => Item::query()->count(),
            'low' => Item::query()->where('available_stock', '>', 0)->whereColumn('available_stock', '<=', 'reorder_point')->count(),
            'out' => Item::query()->where('available_stock', '<=', 0)->count(),
        ];

        $recentLogs = $this->recentLogs($logFilters);

        return view('dashboard', [
            'currentYear' => $currentYear,
            'monthLabels' => $monthLabels,
            'monthlyCollections' => $monthlyCollections,
            'maxMonthlyCollection' => $maxMonthlyCollection,
            'yearlyCollections' => $yearlyCollections,
            'maxYearlyCollection' => $maxYearlyCollection,
            'summary' => [
                'total_collections' => (float) SalesCollection::query()->where('status', '!=', 'cancelled')->sum('applied_amount'),
                'monthly_collections' => (float) SalesCollection::query()
                    ->whereYear('collection_receipt_date', $currentYear)
                    ->whereMonth('collection_receipt_date', $currentMonth)
                    ->where('status', '!=', 'cancelled')
                    ->sum('applied_amount'),
                'pending_collections' => (int) ($collectionCounts['pending'] ?? 0),
                'unpaid_balance' => (float) SalesInvoice::query()->where('status', 'unpaid')->sum('balance_amount'),
            ],
            'operations' => [
                'quotations' => [
                    'total' => Quotation::query()->count(),
                    'this_month' => Quotation::query()->whereYear('quotation_date', $currentYear)->whereMonth('quotation_date', $currentMonth)->count(),
                ],
                'sales_orders' => [
                    'pending' => (int) ($salesOrderCounts['pending'] ?? 0),
                    'partial' => (int) ($salesOrderCounts['partial'] ?? 0),
                    'served' => (int) ($salesOrderCounts['served'] ?? 0),
                ],
                'delivery_receipts' => [
                    'pending' => (int) ($deliveryReceiptCounts['pending'] ?? 0),
                    'billed' => (int) ($deliveryReceiptCounts['billed'] ?? 0),
                    'cancelled' => (int) ($deliveryReceiptCounts['cancelled'] ?? 0),
                ],
                'invoices' => [
                    'unpaid' => (int) ($invoiceCounts['unpaid'] ?? 0),
                    'paid' => (int) ($invoiceCounts['paid'] ?? 0),
                    'cancelled' => (int) ($invoiceCounts['cancelled'] ?? 0),
                ],
                'items' => $itemCounts,
                'partners' => [
                    'clients' => BusinessPartner::query()->clients()->count(),
                    'suppliers' => BusinessPartner::query()->suppliers()->count(),
                ],
            ],
            'recentCollections' => SalesCollection::query()
                ->latest('collection_receipt_date')
                ->latest('id')
                ->limit(5)
                ->get(['collection_no', 'collection_receipt_no', 'collection_receipt_date', 'company_name', 'applied_amount', 'status']),
            'recentLogs' => $recentLogs,
            'logFilters' => $logFilters,
        ]);
    }

    public function transactionLogs(Request $request): JsonResponse
    {
        $logFilters = $this->logFilters($request);
        $recentLogs = $this->recentLogs($logFilters);

        return response()->json([
            'html' => view('dashboard.partials.transaction-logs-table', compact('recentLogs'))->render(),
        ]);
    }

    private function logFilters(Request $request): array
    {
        $limit = (int) $request->query('log_limit', 10);
        $hasDateRange = filled($request->query('log_from')) || filled($request->query('log_to'));

        return [
            'search' => trim((string) $request->query('log_search', '')),
            'from' => $request->query('log_from', now()->toDateString()),
            'to' => $request->query('log_to', now()->toDateString()),
            'limit' => in_array($limit, [10, 25, 50, 100], true) ? $limit : 10,
            'has_date_range' => $hasDateRange,
        ];
    }

    private function recentLogs(array $logFilters)
    {
        return AuditTrail::query()
            ->with('actor:id,name')
            ->when($logFilters['search'] !== '', function ($query) use ($logFilters): void {
                $search = $logFilters['search'];

                $query->where(function ($query) use ($search): void {
                    $query->where('module', 'like', "%{$search}%")
                        ->orWhere('action', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereHas('actor', fn ($query) => $query->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($logFilters['from'], fn ($query, $date) => $query->where('created_at', '>=', Carbon::parse($date)->startOfDay()))
            ->when($logFilters['to'], fn ($query, $date) => $query->where('created_at', '<=', Carbon::parse($date)->endOfDay()))
            ->when(
                $logFilters['has_date_range'],
                fn ($query) => $query->orderBy('created_at')->orderBy('id'),
                fn ($query) => $query->latest('id'),
            )
            ->paginate(
                perPage: $logFilters['limit'],
                columns: ['module', 'action', 'description', 'created_by', 'created_at'],
                pageName: 'logs_page',
            )
            ->withPath(route('dashboard.transaction-logs'))
            ->withQueryString();
    }
}
