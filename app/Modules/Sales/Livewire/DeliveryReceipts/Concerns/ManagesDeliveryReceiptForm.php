<?php

namespace App\Modules\Sales\Livewire\DeliveryReceipts\Concerns;

use App\Modules\Sales\Models\DeliveryReceipt;
use App\Modules\Sales\Requests\StoreDeliveryReceiptRequest;
use App\Modules\Sales\Requests\UpdateDeliveryReceiptRequest;
use App\Modules\Sales\Services\DeliveryReceiptService;
use Illuminate\Support\Str;

trait ManagesDeliveryReceiptForm
{
    public ?DeliveryReceipt $deliveryReceiptRecord = null;

    public string $delivery_receipt_no = '';
    public string $dr_date = '';
    public string $sales_order_id = '';
    public string $sales_order_no = '';
    public string $customer_po = '';
    public string $agent_name = '';
    public string $business_partner_id = '';
    public string $company_name = '';
    public int $terms = 30;
    public string $company_address = '';
    public string $contact_person = '';
    public string $contact_no = '';
    public string $status = 'pending';
    public array $items = [];
    public ?string $notice = null;

    protected function formRules(): array
    {
        return $this->deliveryReceiptRecord
            ? UpdateDeliveryReceiptRequest::rulesArray($this->deliveryReceiptRecord->id)
            : StoreDeliveryReceiptRequest::rulesArray();
    }

    protected function bootDeliveryReceiptForm(): void
    {
        $this->dr_date = $this->dr_date ?: now()->toDateString();
        $this->delivery_receipt_no = $this->delivery_receipt_no ?: app(DeliveryReceiptService::class)->nextDeliveryReceiptNo();
    }

    protected function fillFromDeliveryReceipt(DeliveryReceipt $deliveryReceipt): void
    {
        $deliveryReceipt->load(['items.unitMeasure', 'salesOrder']);
        $this->deliveryReceiptRecord = $deliveryReceipt;
        $this->delivery_receipt_no = $deliveryReceipt->delivery_receipt_no;
        $this->dr_date = $deliveryReceipt->dr_date?->toDateString() ?? now()->toDateString();
        $this->sales_order_id = (string) $deliveryReceipt->sales_order_id;
        $this->sales_order_no = $deliveryReceipt->sales_order_no;
        $this->customer_po = (string) $deliveryReceipt->customer_po;
        $this->agent_name = $deliveryReceipt->agent_name;
        $this->business_partner_id = (string) $deliveryReceipt->business_partner_id;
        $this->company_name = $deliveryReceipt->company_name;
        $this->terms = (int) $deliveryReceipt->terms;
        $this->company_address = (string) $deliveryReceipt->company_address;
        $this->contact_person = (string) $deliveryReceipt->contact_person;
        $this->contact_no = (string) $deliveryReceipt->contact_no;
        $this->status = $deliveryReceipt->status;
        $this->items = $deliveryReceipt->items->map(function ($row): array {
            $remaining = (float) $row->remaining_balance_quantity;
            $delivered = (float) $row->delivered_quantity;
            return [
                'row_key' => 'dr-item-'.(string) Str::uuid(),
                'sales_order_item_id' => $row->sales_order_item_id,
                'item_id' => $row->item_id,
                'item_name' => $row->item_name,
                'ordered_quantity' => number_format((float) $row->ordered_quantity, 2, '.', ''),
                'previously_delivered_quantity' => number_format((float) $row->previously_delivered_quantity, 2, '.', ''),
                'remaining_balance_quantity' => number_format($remaining, 2, '.', ''),
                'available_stock' => number_format((float) $row->available_stock, 2, '.', ''),
                'deliverable_quantity' => number_format(min($remaining, (float) $row->available_stock), 2, '.', ''),
                'delivered_quantity' => number_format($delivered, 2, '.', ''),
                'balance_quantity' => number_format((float) $row->balance_quantity, 2, '.', ''),
                'unit_measure_id' => $row->unit_measure_id,
                'unit_measure_name' => (string) ($row->unitMeasure?->name ?? ''),
                'stock_status' => $row->stock_status,
                'delivery_no' => (string) ($row->delivery_no ?? ''),
                'delivered_date' => $row->delivered_date?->toDateString() ?? '',
                'delivered_by' => (string) ($row->delivered_by ?? ''),
                'received_by' => (string) ($row->received_by ?? ''),
                'remarks' => (string) ($row->remarks ?? ''),
            ];
        })->values()->all();
    }

    public function updatedSalesOrderId(): void
    {
        if ($this->sales_order_id === '') {
            return;
        }
        $loaded = app(DeliveryReceiptService::class)->salesOrderDetails((int) $this->sales_order_id);
        if (! $loaded) {
            $this->notice = 'Selected sales order is no longer available.';
            $this->items = [];
            return;
        }
        $this->sales_order_no = (string) $loaded['sales_order_no'];
        $this->customer_po = (string) $loaded['customer_po'];
        $this->agent_name = (string) $loaded['agent_name'];
        $this->business_partner_id = (string) $loaded['business_partner_id'];
        $this->company_name = (string) $loaded['company_name'];
        $this->terms = (int) $loaded['terms'];
        $this->company_address = (string) $loaded['company_address'];
        $this->contact_person = (string) $loaded['contact_person'];
        $this->contact_no = (string) $loaded['contact_no'];
        $this->items = collect($loaded['items'])->map(fn (array $row): array => ['row_key' => 'dr-item-'.(string) Str::uuid()] + $row)->values()->all();
        $hasAnyDeliverable = collect($this->items)
            ->contains(fn (array $row) => in_array(($row['stock_status'] ?? 'no_stock'), ['available', 'partial_stock'], true));
        $this->notice = $hasAnyDeliverable ? null : 'All items in this Sales Order have no available stocks.';
    }

    public function updatedItems($value, ?string $key = null): void
    {
        if ($key === null) {
            return;
        }

        [$index, $field] = array_pad(explode('.', $key), 2, null);
        if (! isset($this->items[$index])) {
            return;
        }
        if ($field !== 'delivered_quantity') {
            return;
        }
        $deliverable = (int) floor((float) ($this->items[$index]['deliverable_quantity'] ?? 0));
        $delivered = (int) max(min((int) ($this->items[$index]['delivered_quantity'] ?? 0), $deliverable), 0);
        $remaining = (float) ($this->items[$index]['remaining_balance_quantity'] ?? 0);
        $this->items[$index]['delivered_quantity'] = (string) $delivered;
        $this->items[$index]['balance_quantity'] = number_format(max($remaining - $delivered, 0), 2, '.', '');
    }

    public function save(): mixed
    {
        $payload = $this->validate($this->formRules());
        $payload['sales_order_no'] = $this->sales_order_no;
        $payload['customer_po'] = $this->customer_po;
        $payload['agent_name'] = $this->agent_name;
        $payload['business_partner_id'] = $this->business_partner_id;
        $payload['company_name'] = $this->company_name;
        $payload['terms'] = $this->terms;
        $payload['company_address'] = $this->company_address;
        $payload['contact_person'] = $this->contact_person;
        $payload['contact_no'] = $this->contact_no;
        $payload['delivery_receipt_no'] = $this->delivery_receipt_no;

        try {
            if ($this->deliveryReceiptRecord) {
                session()->flash('toast', 'Delivery receipt edit for delivered quantities is restricted.');
                return redirect()->route('sales.delivery-receipts.index');
            }

            app(DeliveryReceiptService::class)->create($payload);
        } catch (\RuntimeException $exception) {
            $this->addError('items', $exception->getMessage());
            return null;
        }

        return redirect()->route('sales.delivery-receipts.index')->with('toast', 'Delivery receipt created successfully.');
    }
}
