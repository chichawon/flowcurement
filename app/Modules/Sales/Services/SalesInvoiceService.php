<?php

namespace App\Modules\Sales\Services;

use App\Modules\AuditTrail\Services\AuditTrailService;
use App\Modules\Sales\Models\DeliveryReceipt;
use App\Modules\Sales\Models\DeliveryReceiptItem;
use App\Modules\Sales\Models\SalesInvoice;
use App\Modules\Sales\Models\SalesInvoiceItem;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class SalesInvoiceService
{
    private const MODULE = 'sales-invoices';

    public function paginate(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return SalesInvoice::query()
            ->with(['salesOrder:id,sales_order_no', 'deliveryReceipt:id,delivery_receipt_no', 'businessPartner:id,company_name', 'creator:id,name'])
            ->when($filters['search'] ?? null, function (Builder $query, string $search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('sales_invoice_no', 'like', "%{$search}%")
                        ->orWhere('sales_order_no', 'like', "%{$search}%")
                        ->orWhere('delivery_receipt_no', 'like', "%{$search}%")
                        ->orWhere('customer_po', 'like', "%{$search}%")
                        ->orWhere('company_name', 'like', "%{$search}%");
                });
            })
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status))
            ->when($filters['currency'] ?? null, fn (Builder $query, string $currency) => $query->where('currency', $currency))
            ->when($filters['date_from'] ?? null, fn (Builder $query, string $date) => $query->whereDate('invoice_date', '>=', $date))
            ->when($filters['date_to'] ?? null, fn (Builder $query, string $date) => $query->whereDate('invoice_date', '<=', $date))
            ->latest('id')
            ->paginate($perPage);
    }

    public function nextSalesInvoiceNo(): string
    {
        return DB::transaction(function (): string {
            $prefix = 'SI'.now()->format('y-m').'-';
            $last = SalesInvoice::query()
                ->where('sales_invoice_no', 'like', $prefix.'%')
                ->lockForUpdate()
                ->orderByDesc('id')
                ->value('sales_invoice_no');
            $next = $last ? ((int) Str::afterLast($last, '-')) + 1 : 1;

            return $prefix.str_pad((string) $next, 3, '0', STR_PAD_LEFT);
        });
    }

    public function eligibleDeliveryReceipts(): Collection
    {
        return DeliveryReceipt::query()
            ->where('status', 'completed')
            ->whereHas('items', function (Builder $query): void {
                $query->where('delivered_quantity', '>', 0)
                    ->whereRaw('delivered_quantity > COALESCE((select sum(sales_invoice_items.quantity) from sales_invoice_items inner join sales_invoices on sales_invoices.id = sales_invoice_items.sales_invoice_id where sales_invoice_items.delivery_receipt_item_id = delivery_receipt_items.id and sales_invoices.deleted_at is null and sales_invoices.status != ?), 0)', ['void']);
            })
            ->latest('id')
            ->get(['id', 'delivery_receipt_no', 'sales_order_no', 'company_name', 'sales_order_id', 'business_partner_id']);
    }

    public function deliveryReceiptDetails(int $deliveryReceiptId): ?array
    {
        $receipt = DeliveryReceipt::query()->with([
            'salesOrder:id,sales_order_no,currency,tax_rate',
            'businessPartner:id,company_name',
            'items.item:id,item_name',
            'items.unitMeasure:id,name',
            'items.salesOrderItem:id,description,price,total',
        ])->find($deliveryReceiptId);

        if (! $receipt) {
            return null;
        }

        if ($receipt->status !== 'completed') {
            return null;
        }

        $invoicedMap = $this->invoicedTotalsByDeliveryReceiptItem([$receipt->id]);
        $rows = $receipt->items
            ->map(function (DeliveryReceiptItem $row) use ($receipt, $invoicedMap): ?array {
                $delivered = (float) $row->delivered_quantity;
                $previouslyInvoiced = (float) ($invoicedMap[$row->id] ?? 0);
                $invoiceable = max($delivered - $previouslyInvoiced, 0);

                if ($invoiceable <= 0) {
                    return null;
                }

                $price = (float) ($row->salesOrderItem?->price ?? 0);
                $taxRate = (float) ($receipt->salesOrder?->tax_rate ?? 0);
                $subtotal = round($invoiceable * $price, 2);
                $taxAmount = round($subtotal * ($taxRate / 100), 2);

                return [
                    'delivery_receipt_id' => $receipt->id,
                    'delivery_receipt_item_id' => $row->id,
                    'sales_order_item_id' => $row->sales_order_item_id,
                    'item_id' => $row->item_id,
                    'item_name' => (string) ($row->item?->item_name ?? $row->item_name),
                    'description' => (string) ($row->salesOrderItem?->description ?? ''),
                    'unit_measure_id' => $row->unit_measure_id,
                    'unit_measure_name' => (string) ($row->unitMeasure?->name ?? ''),
                    'delivered_quantity' => number_format($delivered, 2, '.', ''),
                    'previously_invoiced_quantity' => number_format($previouslyInvoiced, 2, '.', ''),
                    'invoiceable_quantity' => number_format($invoiceable, 2, '.', ''),
                    'quantity' => number_format($invoiceable, 2, '.', ''),
                    'price' => number_format($price, 2, '.', ''),
                    'subtotal' => number_format($subtotal, 2, '.', ''),
                    'tax_rate' => number_format($taxRate, 2, '.', ''),
                    'tax_amount' => number_format($taxAmount, 2, '.', ''),
                    'total' => number_format($subtotal + $taxAmount, 2, '.', ''),
                ];
            })
            ->filter()
            ->values()
            ->all();

        return [
            'delivery_receipt_id' => $receipt->id,
            'delivery_receipt_no' => $receipt->delivery_receipt_no,
            'sales_order_id' => $receipt->sales_order_id,
            'sales_order_no' => $receipt->sales_order_no,
            'business_partner_id' => $receipt->business_partner_id,
            'customer_po' => (string) $receipt->customer_po,
            'company_name' => $receipt->company_name,
            'terms' => (int) $receipt->terms,
            'company_address' => (string) $receipt->company_address,
            'contact_person' => (string) $receipt->contact_person,
            'contact_no' => (string) $receipt->contact_no,
            'currency' => (string) ($receipt->salesOrder?->currency ?? 'php'),
            'tax_rate' => (string) (int) ($receipt->salesOrder?->tax_rate ?? 0),
            'items' => $rows,
        ];
    }

    public function create(array $payload): SalesInvoice
    {
        Gate::authorize('create', SalesInvoice::class);

        return DB::transaction(function () use ($payload): SalesInvoice {
            $this->ensureDeliveryReceiptIsCompleted((int) ($payload['delivery_receipt_id'] ?? 0));
            $this->applySourceTaxRate($payload);
            $payload['sales_invoice_no'] = $this->nextSalesInvoiceNo();
            $invoice = SalesInvoice::query()->create($this->headerPayload($payload));
            $this->syncItems($invoice, $payload['items'] ?? []);
            app(AuditTrailService::class)->record(self::MODULE, 'created', $invoice, null, $invoice->load('items')->toArray(), 'Sales invoice created: '.$invoice->sales_invoice_no);

            return $invoice->refresh();
        });
    }

    public function update(SalesInvoice $invoice, array $payload): SalesInvoice
    {
        Gate::authorize('update', $invoice);

        return DB::transaction(function () use ($invoice, $payload): SalesInvoice {
            $this->ensureDeliveryReceiptIsCompleted((int) ($payload['delivery_receipt_id'] ?? 0));
            $this->applySourceTaxRate($payload);
            $old = $invoice->load('items')->toArray();
            $invoice->update($this->headerPayload($payload, false));
            $invoice->items()->delete();
            $this->syncItems($invoice, $payload['items'] ?? []);
            app(AuditTrailService::class)->record(self::MODULE, 'updated', $invoice, $old, $invoice->load('items')->toArray(), 'Sales invoice updated: '.$invoice->sales_invoice_no);

            return $invoice->refresh();
        });
    }

    public function void(SalesInvoice $invoice): SalesInvoice
    {
        Gate::authorize('delete', $invoice);

        return DB::transaction(function () use ($invoice): SalesInvoice {
            $old = $invoice->toArray();
            $invoice->update([
                'status' => 'void',
                'updated_by' => auth()->id(),
            ]);
            app(AuditTrailService::class)->record(self::MODULE, 'voided', $invoice, $old, $invoice->toArray(), 'Sales invoice voided: '.$invoice->sales_invoice_no);

            return $invoice->refresh();
        });
    }

    public function issue(SalesInvoice $invoice): SalesInvoice
    {
        Gate::authorize('issue', $invoice);

        return DB::transaction(function () use ($invoice): SalesInvoice {
            $old = $invoice->toArray();
            $invoice->update([
                'status' => 'issued',
                'updated_by' => auth()->id(),
            ]);
            app(AuditTrailService::class)->record(self::MODULE, 'issued', $invoice, $old, $invoice->toArray(), 'Sales invoice issued: '.$invoice->sales_invoice_no);

            return $invoice->refresh();
        });
    }

    public function totals(array $items, float|int|string $taxRate): array
    {
        $subtotal = collect($items)->sum(fn (array $row) => (float) ($row['quantity'] ?? 0) * (float) ($row['price'] ?? 0));
        $taxAmount = round($subtotal * ((float) $taxRate / 100), 2);
        $total = round($subtotal + $taxAmount, 2);

        return [
            'subtotal' => round($subtotal, 2),
            'tax_amount' => $taxAmount,
            'total_amount' => $total,
            'balance_amount' => $total,
        ];
    }

    private function headerPayload(array $payload, bool $creating = true): array
    {
        $totals = $this->totals($payload['items'] ?? [], $payload['tax_rate'] ?? 0);

        return array_merge([
            'invoice_date' => $payload['invoice_date'],
            'due_date' => $payload['due_date'] ?? null,
            'business_partner_id' => $payload['business_partner_id'],
            'sales_order_id' => $payload['sales_order_id'],
            'delivery_receipt_id' => $payload['delivery_receipt_id'],
            'sales_order_no' => $payload['sales_order_no'],
            'delivery_receipt_no' => $payload['delivery_receipt_no'],
            'customer_po' => $payload['customer_po'] ?? null,
            'company_name' => $payload['company_name'],
            'terms' => (int) ($payload['terms'] ?? 30),
            'company_address' => $payload['company_address'] ?? null,
            'contact_person' => $payload['contact_person'] ?? null,
            'contact_no' => $payload['contact_no'] ?? null,
            'currency' => $payload['currency'],
            'tax_rate' => (float) $payload['tax_rate'],
            'status' => $payload['status'] ?? 'pending',
            'remarks' => $payload['remarks'] ?? null,
            'updated_by' => auth()->id(),
        ], $totals, $creating ? [
            'sales_invoice_no' => $payload['sales_invoice_no'],
            'created_by' => auth()->id(),
        ] : []);
    }

    private function syncItems(SalesInvoice $invoice, array $items): void
    {
        $invoicedMap = $this->invoicedTotalsByDeliveryReceiptItem(
            collect($items)->pluck('delivery_receipt_id')->filter()->unique()->map(fn ($id) => (int) $id)->all(),
            $invoice->id
        );

        $saved = 0;
        foreach ($items as $row) {
            $drItem = DeliveryReceiptItem::query()->with('salesOrderItem')->find((int) ($row['delivery_receipt_item_id'] ?? 0));
            if (! $drItem) {
                continue;
            }

            $delivered = (float) $drItem->delivered_quantity;
            $previouslyInvoiced = (float) ($invoicedMap[$drItem->id] ?? 0);
            $invoiceable = max($delivered - $previouslyInvoiced, 0);
            $quantity = min(max((float) ($row['quantity'] ?? 0), 0), $invoiceable);

            if ($quantity <= 0) {
                continue;
            }

            $price = (float) ($drItem->salesOrderItem?->price ?? 0);
            $subtotal = round($quantity * $price, 2);
            $taxRate = (float) ($invoice->tax_rate ?? 0);
            $taxAmount = round($subtotal * ($taxRate / 100), 2);

            SalesInvoiceItem::query()->create([
                'sales_invoice_id' => $invoice->id,
                'delivery_receipt_id' => $drItem->delivery_receipt_id,
                'delivery_receipt_item_id' => $drItem->id,
                'sales_order_item_id' => $drItem->sales_order_item_id,
                'item_id' => $drItem->item_id,
                'item_name' => $row['item_name'] ?? $drItem->item_name,
                'description' => $row['description'] ?? $drItem->salesOrderItem?->description,
                'unit_measure_id' => $drItem->unit_measure_id,
                'delivered_quantity' => $delivered,
                'previously_invoiced_quantity' => $previouslyInvoiced,
                'invoiceable_quantity' => $invoiceable,
                'quantity' => $quantity,
                'price' => $price,
                'subtotal' => $subtotal,
                'tax_rate' => $taxRate,
                'tax_amount' => $taxAmount,
                'total' => round($subtotal + $taxAmount, 2),
            ]);
            $saved++;
        }

        if ($saved === 0) {
            throw new \RuntimeException('No invoiceable delivered items were selected.');
        }
    }

    private function ensureDeliveryReceiptIsCompleted(int $deliveryReceiptId): void
    {
        $status = DeliveryReceipt::query()->whereKey($deliveryReceiptId)->value('status');

        if ($status !== 'completed') {
            throw new \RuntimeException('Only completed delivery receipts can be invoiced.');
        }
    }

    private function applySourceTaxRate(array &$payload): void
    {
        $source = DeliveryReceipt::query()
            ->with('salesOrder:id,tax_rate')
            ->find((int) ($payload['delivery_receipt_id'] ?? 0));

        if ($source?->salesOrder) {
            $payload['tax_rate'] = (float) $source->salesOrder->tax_rate;
        }
    }

    private function invoicedTotalsByDeliveryReceiptItem(array $deliveryReceiptIds, ?int $exceptInvoiceId = null): array
    {
        if ($deliveryReceiptIds === []) {
            return [];
        }

        return SalesInvoiceItem::query()
            ->select('delivery_receipt_item_id', DB::raw('SUM(quantity) as invoiced_total'))
            ->whereIn('delivery_receipt_id', $deliveryReceiptIds)
            ->when($exceptInvoiceId, fn (Builder $query) => $query->where('sales_invoice_id', '!=', $exceptInvoiceId))
            ->whereHas('salesInvoice', fn (Builder $query) => $query->where('status', '!=', 'void'))
            ->groupBy('delivery_receipt_item_id')
            ->pluck('invoiced_total', 'delivery_receipt_item_id')
            ->map(fn ($value) => (float) $value)
            ->all();
    }
}
