<?php

namespace App\Modules\Purchasing\Livewire\Invoices;

use App\Modules\Purchasing\Helpers\PurchaseInvoiceOptions;
use App\Modules\Purchasing\Models\PurchaseInvoice;
use App\Modules\Purchasing\Requests\StorePurchaseInvoiceRequest;
use App\Modules\Purchasing\Services\PurchaseInvoiceService;
use App\Modules\Purchasing\Services\PurchaseOrderService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class Create extends Component
{
    use AuthorizesRequests;

    public string $purchase_invoice_no = '';
    public string $invoice_date = '';
    public string $supplier_invoice_no = '';
    public string $purchase_order_id = '';
    public string $purchase_order_no = '';
    public string $supplier_id = '';
    public string $supplier_name = '';
    public string $supplier_address = '';
    public string $contact_person = '';
    public string $contact_no = '';
    public string $terms = '';
    public string $due_date = '';
    public string $currency = 'php';
    public string $tax_rate = '0';
    public string $status = 'unpaid';
    public string $remarks = '';
    public array $items = [];
    public array $totals = ['subtotal' => 0, 'tax_amount' => 0, 'total_amount' => 0, 'balance_amount' => 0];

    public function mount(): void
    {
        $this->authorize('create', PurchaseInvoice::class);
        $this->purchase_invoice_no = app(PurchaseInvoiceService::class)->nextPurchaseInvoiceNo();
        $this->invoice_date = now()->toDateString();
    }

    public function updatedPurchaseOrderId(): void
    {
        $this->loadPurchaseOrder();
    }

    public function updatedItems(): void
    {
        $this->recomputeRows();
    }

    public function updatedTaxRate(): void
    {
        $this->recomputeRows();
    }

    public function loadPurchaseOrder(): void
    {
        if (! $this->purchase_order_id) {
            $this->items = [];
            $this->recomputeRows();
            return;
        }

        $details = app(PurchaseInvoiceService::class)->purchaseOrderDetails((int) $this->purchase_order_id);
        if (! $details) {
            session()->flash('toast', 'Purchase order no longer exists.');
            return;
        }

        foreach ($details as $key => $value) {
            if ($key !== 'items') {
                $this->{$key} = (string) $value;
            }
        }
        $this->items = $details['items'];
        $this->recomputeRows();
    }

    public function save(): mixed
    {
        $this->recomputeRows();
        $this->validate(StorePurchaseInvoiceRequest::rulesArray());
        app(PurchaseInvoiceService::class)->create($this->payload());
        session()->flash('toast', 'Purchase invoice saved successfully.');

        return redirect()->route('purchasing.invoices.index');
    }

    public function render()
    {
        return view('modules.purchasing.invoices.livewire.form', [
            'purchaseOrders' => app(PurchaseInvoiceService::class)->purchaseOrders(),
            'unitMeasures' => app(PurchaseOrderService::class)->unitMeasures(),
            'statuses' => PurchaseInvoiceOptions::STATUSES,
            'currencies' => PurchaseInvoiceOptions::CURRENCIES,
            'taxRates' => PurchaseInvoiceOptions::TAX_RATES,
            'editing' => false,
        ]);
    }

    protected function recomputeRows(): void
    {
        foreach ($this->items as $index => $row) {
            $quantity = max(1, (int) ($row['quantity'] ?? 1));
            $price = max(0, (float) ($row['price'] ?? 0));
            $subtotal = round($quantity * $price, 2);
            $taxAmount = round($subtotal * ((float) $this->tax_rate / 100), 2);
            $this->items[$index]['quantity'] = (string) $quantity;
            $this->items[$index]['price'] = number_format($price, 2, '.', '');
            $this->items[$index]['subtotal'] = number_format($subtotal, 2, '.', '');
            $this->items[$index]['tax_amount'] = number_format($taxAmount, 2, '.', '');
            $this->items[$index]['total'] = number_format($subtotal + $taxAmount, 2, '.', '');
        }
        $this->totals = app(PurchaseInvoiceService::class)->totals($this->items, $this->tax_rate);
    }

    protected function payload(): array
    {
        return [
            'purchase_invoice_no' => $this->purchase_invoice_no,
            'invoice_date' => $this->invoice_date,
            'supplier_invoice_no' => $this->supplier_invoice_no,
            'purchase_order_id' => $this->purchase_order_id,
            'purchase_order_no' => $this->purchase_order_no,
            'supplier_id' => $this->supplier_id,
            'due_date' => $this->due_date,
            'currency' => $this->currency,
            'tax_rate' => $this->tax_rate,
            'status' => $this->status,
            'remarks' => $this->remarks,
            'items' => $this->items,
        ];
    }
}
