<?php

namespace App\Modules\Purchasing\Livewire\Orders;

use App\Modules\Purchasing\Models\PurchaseOrder;
use App\Modules\Purchasing\Requests\UpdatePurchaseOrderRequest;
use App\Modules\Purchasing\Services\PurchaseOrderService;

class Edit extends Create
{
    public int $purchaseOrder;

    public function mount(int $purchaseOrder): void
    {
        $order = PurchaseOrder::query()->with(['items.item', 'items.unitMeasure'])->findOrFail($purchaseOrder);
        $this->authorize('update', $order);
        $this->purchaseOrder = $order->id;
        $this->purchase_order_no = $order->purchase_order_no;
        $this->purchase_order_date = $order->purchase_order_date?->toDateString() ?? now()->toDateString();
        $this->expected_delivery_date = $order->expected_delivery_date?->toDateString() ?? '';
        $this->supplier_id = (string) $order->supplier_id;
        $this->supplier_name = $order->supplier_name;
        $this->supplier_address = $order->supplier_address;
        $this->contact_person = $order->contact_person;
        $this->contact_no = $order->contact_no;
        $this->terms = $order->terms;
        $this->remarks = (string) $order->remarks;
        $this->currency = $order->currency;
        $this->tax_rate = (string) (int) $order->tax_rate;
        $this->status = $order->status;
        $this->items = $order->items->map(fn ($item): array => [
            'item_id' => (string) $item->item_id,
            'item_name' => $item->item?->item_name,
            'item_image' => (string) $item->item?->item_image,
            'description' => (string) $item->description,
            'lead_time' => (string) $item->lead_time,
            'unit_measure_id' => (string) $item->unit_measure_id,
            'quantity' => (string) (int) $item->quantity,
            'price' => number_format((float) $item->price, 2, '.', ''),
            'remarks' => (string) $item->remarks,
            'subtotal' => number_format((float) $item->subtotal, 2, '.', ''),
            'tax_amount' => number_format((float) $item->tax_amount, 2, '.', ''),
            'total' => number_format((float) $item->total, 2, '.', ''),
        ])->all();
        $this->recomputeTotals();
    }

    public function save(): mixed
    {
        $order = PurchaseOrder::query()->findOrFail($this->purchaseOrder);
        $this->normalizeItemRows();
        $this->recomputeTotals();
        $this->validate(UpdatePurchaseOrderRequest::rulesArray($order->id));
        app(PurchaseOrderService::class)->update($order, $this->payload());
        session()->flash('toast', 'Purchase order updated successfully.');

        return redirect()->route('purchasing.orders.index');
    }
}
