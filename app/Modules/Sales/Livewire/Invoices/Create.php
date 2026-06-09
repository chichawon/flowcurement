<?php

namespace App\Modules\Sales\Livewire\Invoices;

use App\Modules\Sales\Helpers\SalesInvoiceOptions;
use App\Modules\Sales\Models\SalesInvoice;
use App\Modules\Sales\Requests\StoreSalesInvoiceRequest;
use App\Modules\Sales\Services\SalesInvoiceService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class Create extends Component
{
    use AuthorizesRequests;

    public string $sales_invoice_no = '';
    public string $invoice_date = '';
    public string $due_date = '';
    public string $delivery_receipt_id = '';
    public string $delivery_receipt_no = '';
    public string $sales_order_id = '';
    public string $sales_order_no = '';
    public string $business_partner_id = '';
    public string $customer_po = '';
    public string $company_name = '';
    public string $terms = '30';
    public string $company_address = '';
    public string $contact_person = '';
    public string $contact_no = '';
    public string $currency = 'php';
    public string $tax_rate = '0';
    public string $withholding_tax_rate = '0';
    public string $remarks = '';
    public array $items = [];
    public array $totals = ['subtotal' => 0, 'tax_amount' => 0, 'withholding_tax_amount' => 0, 'total_amount' => 0, 'balance_amount' => 0];

    public function mount(): void
    {
        $this->authorize('create', SalesInvoice::class);
        $this->sales_invoice_no = app(SalesInvoiceService::class)->nextSalesInvoiceNo();
        $this->invoice_date = now()->toDateString();
        $this->remarks = $this->defaultRemarksTemplate();
    }

    public function updatedDeliveryReceiptId(): void
    {
        $this->loadDeliveryReceipt();
    }

    public function updatedTaxRate(): void
    {
        $this->recomputeTotals();
    }

    public function updatedItems(): void
    {
        $this->normalizeItems();
        $this->recomputeTotals();
    }

    public function loadDeliveryReceipt(): void
    {
        if (! $this->delivery_receipt_id) {
            $this->items = [];
            $this->recomputeTotals();
            return;
        }

        $details = app(SalesInvoiceService::class)->deliveryReceiptDetails((int) $this->delivery_receipt_id);
        if (! $details) {
            session()->flash('toast', 'Delivery receipt no longer exists.');
            return;
        }

        foreach ($details as $key => $value) {
            if ($key !== 'items') {
                $this->{$key} = (string) $value;
            }
        }
        $this->items = $details['items'];
        $this->due_date = now()->parse($this->invoice_date)->addDays((int) $this->terms)->toDateString();
        $this->recomputeTotals();
    }

    public function save(): mixed
    {
        $payload = $this->payload();
        $this->validate(StoreSalesInvoiceRequest::rulesArray(), [], ['delivery_receipt_id' => 'delivery receipt']);

        try {
            app(SalesInvoiceService::class)->create($payload);
        } catch (\RuntimeException $exception) {
            $this->addError('items', $exception->getMessage());
            return null;
        }

        session()->flash('toast', 'Sales invoice saved successfully.');
        return redirect()->route('sales.invoices.index');
    }

    public function render()
    {
        return view('modules.sales.invoices.livewire.form', [
            'deliveryReceipts' => app(SalesInvoiceService::class)->eligibleDeliveryReceipts(),
            'statuses' => SalesInvoiceOptions::STATUSES,
            'currencies' => SalesInvoiceOptions::CURRENCIES,
            'taxRates' => SalesInvoiceOptions::TAX_RATES,
            'withholdingTaxRates' => SalesInvoiceOptions::WITHHOLDING_TAX_RATES,
            'editing' => false,
        ]);
    }

    private function normalizeItems(): void
    {
        foreach ($this->items as $index => $row) {
            $quantity = (int) floor(min(max((float) ($row['quantity'] ?? 0), 0), max((float) ($row['invoiceable_quantity'] ?? 0), 0)));
            $price = max((float) ($row['price'] ?? 0), 0);
            $subtotal = round($quantity * $price, 2);
            $taxAmount = round($subtotal * ((float) $this->tax_rate / 100), 2);
            $withholdingTaxRate = (float) ($row['withholding_tax_rate'] ?? 0);
            $grossTotal = round($subtotal + $taxAmount, 2);
            $withholdingBase = ((float) $this->tax_rate) > 0 ? $subtotal : $grossTotal;
            $withholdingTaxAmount = round($withholdingBase * ($withholdingTaxRate / 100), 2);
            $this->items[$index]['quantity'] = (string) $quantity;
            $this->items[$index]['price'] = number_format($price, 2, '.', '');
            $this->items[$index]['subtotal'] = number_format($subtotal, 2, '.', '');
            $this->items[$index]['tax_rate'] = number_format((float) $this->tax_rate, 2, '.', '');
            $this->items[$index]['tax_amount'] = number_format($taxAmount, 2, '.', '');
            $this->items[$index]['withholding_tax_rate'] = (string) (int) $withholdingTaxRate;
            $this->items[$index]['withholding_tax_amount'] = number_format($withholdingTaxAmount, 2, '.', '');
            $this->items[$index]['total'] = number_format($grossTotal, 2, '.', '');
        }
    }

    private function recomputeTotals(): void
    {
        $this->normalizeItems();
        $this->totals = app(SalesInvoiceService::class)->totals($this->items, $this->tax_rate);
    }

    private function payload(): array
    {
        return [
            'sales_invoice_no' => $this->sales_invoice_no,
            'invoice_date' => $this->invoice_date,
            'due_date' => $this->due_date ?: null,
            'delivery_receipt_id' => $this->delivery_receipt_id,
            'delivery_receipt_no' => $this->delivery_receipt_no,
            'sales_order_id' => $this->sales_order_id,
            'sales_order_no' => $this->sales_order_no,
            'business_partner_id' => $this->business_partner_id,
            'customer_po' => $this->customer_po,
            'company_name' => $this->company_name,
            'terms' => $this->terms,
            'company_address' => $this->company_address,
            'contact_person' => $this->contact_person,
            'contact_no' => $this->contact_no,
            'currency' => $this->currency,
            'tax_rate' => $this->tax_rate,
            'withholding_tax_rate' => $this->withholding_tax_rate,
            'status' => 'unpaid',
            'remarks' => $this->remarks,
            'items' => $this->items,
        ];
    }

    private function defaultRemarksTemplate(): string
    {
        return "*Notes\n\n1. Price are Vat exclusive\n2. 30 Days Terms of Payment\n3. All Prices are Negotiable";
    }
}
