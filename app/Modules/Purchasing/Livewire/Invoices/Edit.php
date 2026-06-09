<?php

namespace App\Modules\Purchasing\Livewire\Invoices;

use App\Modules\Purchasing\Models\PurchaseInvoice;
use App\Modules\Purchasing\Requests\UpdatePurchaseInvoiceRequest;
use App\Modules\Purchasing\Services\PurchaseInvoiceService;

class Edit extends Create
{
    public int $purchaseInvoice;

    public function mount(int $purchaseInvoice): void
    {
        $invoice = PurchaseInvoice::query()->with(['items.item', 'items.unitMeasure'])->findOrFail($purchaseInvoice);
        $this->authorize('update', $invoice);
        $this->purchaseInvoice = $invoice->id;
        $this->purchase_invoice_no = $invoice->purchase_invoice_no;
        $this->invoice_date = $invoice->invoice_date?->toDateString() ?? now()->toDateString();
        $this->supplier_invoice_no = $invoice->supplier_invoice_no;
        $this->purchase_order_id = (string) $invoice->purchase_order_id;
        $this->purchase_order_no = (string) $invoice->purchase_order_no;
        $this->supplier_id = (string) $invoice->supplier_id;
        $this->supplier_name = $invoice->supplier_name;
        $this->supplier_address = $invoice->supplier_address;
        $this->contact_person = $invoice->contact_person;
        $this->contact_no = $invoice->contact_no;
        $this->terms = $invoice->terms;
        $this->due_date = $invoice->due_date?->toDateString() ?? '';
        $this->currency = $invoice->currency;
        $this->tax_rate = (string) (int) $invoice->tax_rate;
        $this->status = $invoice->status;
        $this->remarks = (string) $invoice->remarks;
        $this->items = $invoice->items->map(fn ($item): array => [
            'purchase_order_item_id' => (string) $item->purchase_order_item_id,
            'item_id' => (string) $item->item_id,
            'item_name' => $item->item?->item_name,
            'item_image' => $item->item?->item_image,
            'description' => (string) $item->description,
            'unit_measure_id' => (string) $item->unit_measure_id,
            'unit_measure_name' => $item->unitMeasure?->name,
            'quantity' => (string) (int) $item->quantity,
            'price' => number_format((float) $item->price, 2, '.', ''),
            'subtotal' => number_format((float) $item->subtotal, 2, '.', ''),
            'tax_amount' => number_format((float) $item->tax_amount, 2, '.', ''),
            'total' => number_format((float) $item->total, 2, '.', ''),
        ])->all();
        $this->recomputeRows();
    }

    public function save(): mixed
    {
        $invoice = PurchaseInvoice::query()->findOrFail($this->purchaseInvoice);
        $this->recomputeRows();
        $this->validate(UpdatePurchaseInvoiceRequest::rulesArray($invoice->id));
        app(PurchaseInvoiceService::class)->update($invoice, $this->payload());
        session()->flash('toast', 'Purchase invoice updated successfully.');

        return redirect()->route('purchasing.invoices.index');
    }
}
