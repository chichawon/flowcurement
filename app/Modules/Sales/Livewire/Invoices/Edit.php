<?php

namespace App\Modules\Sales\Livewire\Invoices;

use App\Modules\Sales\Helpers\SalesInvoiceOptions;
use App\Modules\Sales\Models\SalesInvoice;
use App\Modules\Sales\Requests\StoreSalesInvoiceRequest;
use App\Modules\Sales\Services\SalesInvoiceService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class Edit extends Component
{
    use AuthorizesRequests;

    public mixed $salesInvoice = null;
    public SalesInvoice $invoice;
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
    public string $remarks = '';
    public array $items = [];
    public array $totals = ['subtotal' => 0, 'tax_amount' => 0, 'withholding_tax_amount' => 0, 'total_amount' => 0, 'balance_amount' => 0];

    public function mount(int|SalesInvoice $salesInvoice): void
    {
        $this->invoice = $salesInvoice instanceof SalesInvoice
            ? $salesInvoice
            : SalesInvoice::query()->with(['items.unitMeasure'])->findOrFail($salesInvoice);

        $this->authorize('update', $this->invoice);
        $this->fillFromInvoice($this->invoice->load('items.unitMeasure'));
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

    public function save(): mixed
    {
        $payload = $this->payload();
        $this->validate(StoreSalesInvoiceRequest::rulesArray($this->invoice->id));

        try {
            app(SalesInvoiceService::class)->update($this->invoice, $payload);
        } catch (\RuntimeException $exception) {
            $this->addError('items', $exception->getMessage());
            return null;
        }

        session()->flash('toast', 'Sales invoice updated successfully.');
        return redirect()->route('sales.invoices.index');
    }

    public function render()
    {
        return view('modules.sales.invoices.livewire.form', [
            'deliveryReceipts' => collect(),
            'statuses' => SalesInvoiceOptions::STATUSES,
            'currencies' => SalesInvoiceOptions::CURRENCIES,
            'taxRates' => SalesInvoiceOptions::TAX_RATES,
            'withholdingTaxRates' => SalesInvoiceOptions::WITHHOLDING_TAX_RATES,
            'editing' => true,
        ]);
    }

    private function fillFromInvoice(SalesInvoice $invoice): void
    {
        foreach ([
            'sales_invoice_no', 'delivery_receipt_no', 'sales_order_no', 'customer_po', 'company_name',
            'company_address', 'contact_person', 'contact_no', 'currency', 'remarks',
        ] as $field) {
            $this->{$field} = (string) ($invoice->{$field} ?? '');
        }

        $this->invoice_date = $invoice->invoice_date?->toDateString() ?? now()->toDateString();
        $this->due_date = $invoice->due_date?->toDateString() ?? '';
        $this->delivery_receipt_id = (string) $invoice->delivery_receipt_id;
        $this->sales_order_id = (string) $invoice->sales_order_id;
        $this->business_partner_id = (string) $invoice->business_partner_id;
        $this->terms = (string) $invoice->terms;
        $this->tax_rate = (string) (int) $invoice->tax_rate;
        $this->items = $invoice->items->map(fn ($item): array => [
            'delivery_receipt_id' => $item->delivery_receipt_id,
            'delivery_receipt_item_id' => $item->delivery_receipt_item_id,
            'sales_order_item_id' => $item->sales_order_item_id,
            'item_id' => $item->item_id,
            'item_name' => $item->item_name,
            'description' => (string) $item->description,
            'unit_measure_id' => $item->unit_measure_id,
            'unit_measure_name' => (string) ($item->unitMeasure?->name ?? ''),
            'delivered_quantity' => number_format((float) $item->delivered_quantity, 2, '.', ''),
            'previously_invoiced_quantity' => number_format((float) $item->previously_invoiced_quantity, 2, '.', ''),
            'invoiceable_quantity' => number_format((float) $item->invoiceable_quantity, 2, '.', ''),
            'quantity' => (string) (int) floor((float) $item->quantity),
            'price' => number_format((float) $item->price, 2, '.', ''),
            'subtotal' => number_format((float) $item->subtotal, 2, '.', ''),
            'tax_rate' => number_format((float) $item->tax_rate, 2, '.', ''),
            'tax_amount' => number_format((float) $item->tax_amount, 2, '.', ''),
            'withholding_tax_rate' => (string) (int) $item->withholding_tax_rate,
            'withholding_tax_amount' => number_format((float) $item->withholding_tax_amount, 2, '.', ''),
            'total' => number_format((float) $item->total, 2, '.', ''),
        ])->all();
        $this->recomputeTotals();
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
            $this->items[$index]['total'] = number_format($grossTotal - $withholdingTaxAmount, 2, '.', '');
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
            'withholding_tax_rate' => '0',
            'status' => $this->invoice->status,
            'remarks' => $this->remarks,
            'items' => $this->items,
        ];
    }
}
