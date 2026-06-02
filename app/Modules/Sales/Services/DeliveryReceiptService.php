<?php

namespace App\Modules\Sales\Services;

use App\Modules\AuditTrail\Services\AuditTrailService;
use App\Modules\Inventory\Services\InventoryService;
use App\Modules\Sales\Models\DeliveryReceipt;
use App\Modules\Sales\Models\DeliveryReceiptAttachment;
use App\Modules\Sales\Models\DeliveryReceiptItem;
use App\Modules\Sales\Models\SalesOrder;
use App\Modules\Sales\Models\SalesOrderItem;
use App\Modules\Items\Models\Item;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DeliveryReceiptService
{
    private const MODULE = 'delivery-receipts';

    public function paginate(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return DeliveryReceipt::query()
            ->with([
                'salesOrder:id,sales_order_no',
                'businessPartner:id,company_name',
                'creator:id,name',
                'salesInvoices:id,delivery_receipt_id,sales_invoice_no,status',
            ])
            ->when($filters['search'] ?? null, function (Builder $query, string $search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('delivery_receipt_no', 'like', "%{$search}%")
                        ->orWhere('sales_order_no', 'like', "%{$search}%")
                        ->orWhere('customer_po', 'like', "%{$search}%")
                        ->orWhere('company_name', 'like', "%{$search}%");
                });
            })
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status))
            ->when($filters['date_from'] ?? null, fn (Builder $query, string $date) => $query->whereDate('dr_date', '>=', $date))
            ->when($filters['date_to'] ?? null, fn (Builder $query, string $date) => $query->whereDate('dr_date', '<=', $date))
            ->latest('id')
            ->paginate($perPage);
    }

    public function nextDeliveryReceiptNo(): string
    {
        return DB::transaction(function (): string {
            $prefix = 'DR'.now()->format('y-m').'-';
            $last = DeliveryReceipt::query()
                ->where('delivery_receipt_no', 'like', $prefix.'%')
                ->lockForUpdate()
                ->orderByDesc('id')
                ->value('delivery_receipt_no');
            $next = $last ? ((int) Str::afterLast($last, '-')) + 1 : 1;

            return $prefix.str_pad((string) $next, 3, '0', STR_PAD_LEFT);
        });
    }

    public function eligibleSalesOrders(): Collection
    {
        return SalesOrder::query()
            ->whereIn('status', ['pending', 'partial'])
            ->whereHas('items', fn (Builder $query) => $query->where('balance_quantity', '>', 0))
            ->with('businessPartner:id,company_name')
            ->latest('id')
            ->get(['id', 'sales_order_no', 'business_partner_id', 'customer_po', 'agent_name']);
    }

    public function salesOrderDetails(int $salesOrderId): ?array
    {
        $salesOrder = SalesOrder::query()->with([
            'businessPartner:id,company_name',
            'items.item:id,item_name,available_stock',
            'items.unitMeasure:id,name',
        ])->find($salesOrderId);

        if (! $salesOrder) {
            return null;
        }

        $previouslyDeliveredMap = $this->deliveredTotalsBySalesOrderItem($salesOrder->id);
        $rows = $salesOrder->items
            ->map(function (SalesOrderItem $row) use ($previouslyDeliveredMap): ?array {
                $ordered = (float) $row->order_quantity;
                $previouslyDelivered = max((float) ($previouslyDeliveredMap[$row->id] ?? 0), 0);
                $derivedRemaining = max($ordered - $previouslyDelivered, 0);
                $storedRemaining = max((float) ($row->balance_quantity ?? 0), 0);
                $remaining = max($derivedRemaining, $storedRemaining);

                // Hide fully served lines in DR items panel.
                if ($remaining <= 0) {
                    return null;
                }

                $available = max((float) ($row->item?->available_stock ?? 0), 0);
                $deliverable = floor(min($remaining, $available));
                $delivered = $deliverable;
                $balance = max($remaining - $delivered, 0);

                $stockStatus = 'available';
                if ($deliverable <= 0) {
                    $stockStatus = 'no_stock';
                } elseif ($available < $remaining) {
                    $stockStatus = 'partial_stock';
                }

                return [
                    'sales_order_item_id' => $row->id,
                    'item_id' => $row->item_id,
                    'item_name' => (string) ($row->item?->item_name ?? 'N/A'),
                    'ordered_quantity' => number_format($ordered, 2, '.', ''),
                    'previously_delivered_quantity' => number_format($previouslyDelivered, 2, '.', ''),
                    'remaining_balance_quantity' => number_format($remaining, 2, '.', ''),
                    'available_stock' => number_format($available, 2, '.', ''),
                    'deliverable_quantity' => (string) (int) $deliverable,
                    'delivered_quantity' => (string) (int) $delivered,
                    'balance_quantity' => number_format($balance, 2, '.', ''),
                    'unit_measure_id' => $row->unit_measure_id,
                    'unit_measure_name' => (string) ($row->unitMeasure?->name ?? ''),
                    'stock_status' => $stockStatus,
                    'remarks' => '',
                ];
            })
            ->filter()
            ->values()
            ->all();

        return [
            'sales_order_id' => $salesOrder->id,
            'sales_order_no' => $salesOrder->sales_order_no,
            'customer_po' => (string) $salesOrder->customer_po,
            'agent_name' => $salesOrder->agent_name,
            'business_partner_id' => $salesOrder->business_partner_id,
            'company_name' => (string) ($salesOrder->businessPartner?->company_name ?? ''),
            'terms' => (int) $salesOrder->terms,
            'company_address' => (string) $salesOrder->company_address,
            'contact_person' => (string) $salesOrder->contact_person,
            'contact_no' => (string) $salesOrder->contact_no,
            'items' => $rows,
        ];
    }

    public function create(array $payload): DeliveryReceipt
    {
        Gate::authorize('create', DeliveryReceipt::class);

        return DB::transaction(function () use ($payload): DeliveryReceipt {
            $salesOrder = SalesOrder::query()->with(['items.item'])->findOrFail((int) $payload['sales_order_id']);
            $header = $this->headerPayload($payload);
            $header['delivery_receipt_no'] = $this->nextDeliveryReceiptNo();
            $header['created_by'] = auth()->id();
            $header['updated_by'] = auth()->id();
            $dr = DeliveryReceipt::query()->create($header);

            $savedCount = $this->saveItemsAndDeductStock($dr, $salesOrder, $payload['items'] ?? []);
            if ($savedCount === 0) {
                throw new \RuntimeException('All items in this Sales Order have no available stocks.');
            }

            app(SalesOrderService::class)->refreshStatusFromBalances($salesOrder->refresh());
            app(AuditTrailService::class)->record(self::MODULE, 'created', $dr, null, $dr->load('items')->toArray(), 'Delivery receipt created: '.$dr->delivery_receipt_no);

            return $dr->refresh();
        });
    }

    public function cancel(DeliveryReceipt $deliveryReceipt): DeliveryReceipt
    {
        Gate::authorize('cancel', $deliveryReceipt);

        return DB::transaction(function () use ($deliveryReceipt): DeliveryReceipt {
            $old = $deliveryReceipt->toArray();
            $deliveryReceipt->update([
                'status' => 'cancelled',
                'updated_by' => auth()->id(),
            ]);
            app(AuditTrailService::class)->record(self::MODULE, 'cancelled', $deliveryReceipt, $old, $deliveryReceipt->toArray(), 'Delivery receipt cancelled: '.$deliveryReceipt->delivery_receipt_no);

            return $deliveryReceipt->refresh();
        });
    }

    public function updateUploadDetails(DeliveryReceipt $deliveryReceipt, array $payload, array $attachments = []): DeliveryReceipt
    {
        Gate::authorize('update', $deliveryReceipt);

        return DB::transaction(function () use ($deliveryReceipt, $payload, $attachments): DeliveryReceipt {
            $old = $deliveryReceipt->load('attachments')->toArray();

            $deliveryReceipt->update([
                'received_date' => $payload['received_date'],
                'received_by' => $payload['received_by'],
                'delivered_by' => $payload['delivered_by'],
                'remarks' => 'completed',
                'updated_by' => auth()->id(),
            ]);

            foreach ($attachments as $attachment) {
                $path = $attachment->store('delivery-receipts/attachments', 'public');

                DeliveryReceiptAttachment::query()->create([
                    'delivery_receipt_id' => $deliveryReceipt->id,
                    'file_name' => $attachment->getClientOriginalName(),
                    'file_path' => $path,
                    'mime_type' => $attachment->getMimeType(),
                    'file_size' => $attachment->getSize() ?: 0,
                    'uploaded_by' => auth()->id(),
                ]);
            }

            app(AuditTrailService::class)->record(
                self::MODULE,
                'upload_details_updated',
                $deliveryReceipt,
                $old,
                $deliveryReceipt->fresh('attachments')->toArray(),
                'Delivery receipt upload details updated: '.$deliveryReceipt->delivery_receipt_no
            );

            return $deliveryReceipt->refresh();
        });
    }

    public function deleteAttachment(DeliveryReceiptAttachment $attachment): void
    {
        Gate::authorize('update', $attachment->deliveryReceipt);

        DB::transaction(function () use ($attachment): void {
            $old = $attachment->toArray();
            Storage::disk('public')->delete($attachment->file_path);
            $deliveryReceipt = $attachment->deliveryReceipt;
            $attachment->delete();

            app(AuditTrailService::class)->record(
                self::MODULE,
                'attachment_deleted',
                $deliveryReceipt,
                $old,
                null,
                'Delivery receipt attachment deleted: '.$deliveryReceipt->delivery_receipt_no
            );
        });
    }

    private function headerPayload(array $payload): array
    {
        return [
            'dr_date' => $payload['dr_date'],
            'sales_order_id' => $payload['sales_order_id'],
            'sales_order_no' => $payload['sales_order_no'],
            'customer_po' => $payload['customer_po'] ?? null,
            'agent_name' => $payload['agent_name'],
            'business_partner_id' => $payload['business_partner_id'],
            'company_name' => $payload['company_name'],
            'terms' => (int) ($payload['terms'] ?? 30),
            'company_address' => $payload['company_address'] ?? null,
            'contact_person' => $payload['contact_person'] ?? null,
            'contact_no' => $payload['contact_no'] ?? null,
            'remarks' => 'on_hold',
            'status' => 'pending',
        ];
    }

    private function saveItemsAndDeductStock(DeliveryReceipt $deliveryReceipt, SalesOrder $salesOrder, array $items): int
    {
        $saved = 0;
        $previouslyDeliveredMap = $this->deliveredTotalsBySalesOrderItem($salesOrder->id);
        foreach ($items as $row) {
            $soItem = $salesOrder->items->firstWhere('id', (int) ($row['sales_order_item_id'] ?? 0));
            if (! $soItem) {
                continue;
            }

            $ordered = (float) $soItem->order_quantity;
            $previouslyDelivered = max((float) ($previouslyDeliveredMap[$soItem->id] ?? 0), 0);
            $remaining = max($ordered - $previouslyDelivered, 0);
            $available = max((float) ($soItem->item?->available_stock ?? 0), 0);
            $requested = max((int) ($row['delivered_quantity'] ?? 0), 0);
            $deliverable = (int) floor(min($remaining, $available));
            $delivered = (int) min($requested, $deliverable);

            if ($delivered <= 0) {
                continue;
            }

            $balance = max($remaining - $delivered, 0);
            $stockStatus = $available <= 0 ? 'no_stock' : ($available < $remaining ? 'partial_stock' : 'available');

            DeliveryReceiptItem::query()->create([
                'delivery_receipt_id' => $deliveryReceipt->id,
                'sales_order_item_id' => $soItem->id,
                'item_id' => $soItem->item_id,
                'item_name' => (string) ($soItem->item?->item_name ?? 'N/A'),
                'ordered_quantity' => $ordered,
                'previously_delivered_quantity' => $previouslyDelivered,
                'remaining_balance_quantity' => $remaining,
                'available_stock' => $available,
                'delivered_quantity' => $delivered,
                'balance_quantity' => $balance,
                'unit_measure_id' => $soItem->unit_measure_id,
                'stock_status' => $stockStatus,
                'remarks' => $row['remarks'] ?? null,
            ]);

            app(InventoryService::class)->stockOut(
                itemId: (int) $soItem->item_id,
                quantity: $delivered,
                referenceType: 'delivery_receipt',
                referenceId: $deliveryReceipt->id,
                remarks: 'Delivery receipt '.$deliveryReceipt->delivery_receipt_no
            );

            $soItem->update([
                'balance_quantity' => max($ordered - ($previouslyDelivered + $delivered), 0),
            ]);
            $saved++;
        }

        return $saved;
    }

    private function deliveredTotalsBySalesOrderItem(int $salesOrderId): array
    {
        return DeliveryReceiptItem::query()
            ->select('sales_order_item_id', DB::raw('SUM(delivered_quantity) as delivered_total'))
            ->whereHas('deliveryReceipt', function (Builder $query) use ($salesOrderId): void {
                $query->where('sales_order_id', $salesOrderId)
                    ->where('status', '!=', 'cancelled');
            })
            ->groupBy('sales_order_item_id')
            ->pluck('delivered_total', 'sales_order_item_id')
            ->map(fn ($value) => (float) $value)
            ->all();
    }
}
