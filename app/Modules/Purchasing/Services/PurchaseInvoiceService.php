<?php

namespace App\Modules\Purchasing\Services;

use App\Modules\AuditTrail\Services\AuditTrailService;
use App\Modules\BusinessPartners\Models\BusinessPartner;
use App\Modules\Purchasing\Models\PurchaseInvoice;
use App\Modules\Purchasing\Models\PurchaseInvoiceItem;
use App\Modules\Purchasing\Models\PurchaseOrder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class PurchaseInvoiceService
{
    private const MODULE = 'purchase-invoices';

    public function paginate(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return PurchaseInvoice::query()
            ->with(['supplier:id,company_name', 'purchaseOrder:id,purchase_order_no'])
            ->when($filters['search'] ?? null, function (Builder $query, string $search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('purchase_invoice_no', 'like', "%{$search}%")
                        ->orWhere('supplier_invoice_no', 'like', "%{$search}%")
                        ->orWhere('purchase_order_no', 'like', "%{$search}%")
                        ->orWhere('supplier_name', 'like', "%{$search}%");
                });
            })
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status))
            ->when($filters['currency'] ?? null, fn (Builder $query, string $currency) => $query->where('currency', $currency))
            ->when($filters['date_from'] ?? null, fn (Builder $query, string $date) => $query->whereDate('invoice_date', '>=', $date))
            ->when($filters['date_to'] ?? null, fn (Builder $query, string $date) => $query->whereDate('invoice_date', '<=', $date))
            ->latest('id')
            ->paginate($perPage);
    }

    public function nextPurchaseInvoiceNo(): string
    {
        return DB::transaction(function (): string {
            $prefix = 'PI'.now()->format('y-m').'-';
            $last = PurchaseInvoice::query()->where('purchase_invoice_no', 'like', $prefix.'%')->lockForUpdate()->orderByDesc('id')->value('purchase_invoice_no');
            $next = $last ? ((int) Str::afterLast($last, '-')) + 1 : 1;

            return $prefix.str_pad((string) $next, 3, '0', STR_PAD_LEFT);
        });
    }

    public function purchaseOrders(): Collection
    {
        return PurchaseOrder::query()
            ->with('supplier:id,company_name')
            ->whereNot('status', 'cancelled')
            ->latest('id')
            ->get(['id', 'purchase_order_no', 'supplier_id', 'supplier_name', 'total_amount', 'status']);
    }

    public function purchaseOrderDetails(int $purchaseOrderId): ?array
    {
        $order = PurchaseOrder::query()
            ->with(['items.item:id,item_name,item_code,item_image', 'items.unitMeasure:id,name'])
            ->find($purchaseOrderId);

        if (! $order) {
            return null;
        }

        return [
            'purchase_order_id' => $order->id,
            'purchase_order_no' => $order->purchase_order_no,
            'supplier_id' => $order->supplier_id,
            'supplier_name' => $order->supplier_name,
            'supplier_address' => $order->supplier_address,
            'contact_person' => $order->contact_person,
            'contact_no' => $order->contact_no,
            'terms' => $order->terms,
            'currency' => $order->currency,
            'tax_rate' => (string) (int) $order->tax_rate,
            'items' => $order->items->map(fn ($item): array => [
                'purchase_order_item_id' => $item->id,
                'item_id' => $item->item_id,
                'item_name' => $item->item?->item_name,
                'item_image' => $item->item?->item_image,
                'description' => $item->description,
                'unit_measure_id' => $item->unit_measure_id,
                'unit_measure_name' => $item->unitMeasure?->name,
                'quantity' => (string) (int) $item->quantity,
                'price' => number_format((float) $item->price, 2, '.', ''),
                'subtotal' => number_format((float) $item->subtotal, 2, '.', ''),
                'tax_amount' => number_format((float) $item->tax_amount, 2, '.', ''),
                'total' => number_format((float) $item->total, 2, '.', ''),
            ])->values()->all(),
        ];
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
            'paid_amount' => 0,
            'balance_amount' => round($subtotal + $taxAmount, 2),
        ];
    }

    public function create(array $payload): PurchaseInvoice
    {
        Gate::authorize('create', PurchaseInvoice::class);

        return DB::transaction(function () use ($payload): PurchaseInvoice {
            $payload['purchase_invoice_no'] = $this->nextPurchaseInvoiceNo();
            $invoice = PurchaseInvoice::query()->create($this->headerPayload($payload));
            $this->syncItems($invoice, $payload['items'] ?? []);
            app(AuditTrailService::class)->record(self::MODULE, 'created', $invoice, null, $invoice->load('items')->toArray(), 'Purchase invoice created: '.$invoice->purchase_invoice_no);

            return $invoice->refresh();
        });
    }

    public function update(PurchaseInvoice $invoice, array $payload): PurchaseInvoice
    {
        Gate::authorize('update', $invoice);

        return DB::transaction(function () use ($invoice, $payload): PurchaseInvoice {
            $old = $invoice->load('items')->toArray();
            $payload['purchase_invoice_no'] = $invoice->purchase_invoice_no;
            $invoice->update($this->headerPayload($payload, false));
            $invoice->items()->delete();
            $this->syncItems($invoice, $payload['items'] ?? []);
            app(AuditTrailService::class)->record(self::MODULE, 'updated', $invoice, $old, $invoice->load('items')->toArray(), 'Purchase invoice updated: '.$invoice->purchase_invoice_no);

            return $invoice->refresh();
        });
    }

    public function cancel(PurchaseInvoice $invoice): PurchaseInvoice
    {
        Gate::authorize('cancel', $invoice);

        $old = $invoice->toArray();
        $invoice->update(['status' => 'cancelled', 'updated_by' => auth()->id()]);
        app(AuditTrailService::class)->record(self::MODULE, 'cancelled', $invoice, $old, $invoice->toArray(), 'Purchase invoice cancelled: '.$invoice->purchase_invoice_no);

        return $invoice->refresh();
    }

    private function headerPayload(array $payload, bool $creating = true): array
    {
        $supplier = $this->supplierDetails((int) $payload['supplier_id']);
        $totals = $this->totals($payload['items'] ?? [], $payload['tax_rate'] ?? 0);

        $header = [
            'purchase_invoice_no' => $payload['purchase_invoice_no'] ?? null,
            'invoice_date' => $payload['invoice_date'],
            'supplier_invoice_no' => $payload['supplier_invoice_no'] ?? null,
            'purchase_order_id' => $payload['purchase_order_id'] ?: null,
            'purchase_order_no' => $payload['purchase_order_no'] ?? null,
            'supplier_id' => $payload['supplier_id'],
            'supplier_name' => $supplier['supplier_name'] ?? '',
            'supplier_address' => $supplier['supplier_address'] ?? '',
            'contact_person' => $supplier['contact_person'] ?? '',
            'contact_no' => $supplier['contact_no'] ?? '',
            'terms' => $supplier['terms'] ?? '',
            'due_date' => $payload['due_date'] ?: null,
            'currency' => $payload['currency'] ?? 'php',
            'tax_rate' => $payload['tax_rate'] ?? 0,
            'subtotal' => $totals['subtotal'],
            'tax_amount' => $totals['tax_amount'],
            'total_amount' => $totals['total_amount'],
            'paid_amount' => $payload['paid_amount'] ?? 0,
            'balance_amount' => round($totals['total_amount'] - (float) ($payload['paid_amount'] ?? 0), 2),
            'remarks' => $payload['remarks'] ?? null,
            'status' => $payload['status'] ?? 'unpaid',
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ];

        if (! $creating) {
            unset($header['created_by']);
        }

        return $header;
    }

    private function syncItems(PurchaseInvoice $invoice, array $items): void
    {
        foreach ($items as $row) {
            $subtotal = round((float) $row['quantity'] * (float) $row['price'], 2);
            $taxAmount = round($subtotal * ((float) $invoice->tax_rate / 100), 2);
            PurchaseInvoiceItem::query()->create([
                'purchase_invoice_id' => $invoice->id,
                'purchase_order_item_id' => $row['purchase_order_item_id'] ?? null,
                'item_id' => $row['item_id'],
                'description' => $row['description'] ?? null,
                'unit_measure_id' => $row['unit_measure_id'],
                'quantity' => $row['quantity'],
                'price' => $row['price'],
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total' => round($subtotal + $taxAmount, 2),
            ]);
        }
    }
}
