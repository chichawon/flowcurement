<?php

namespace App\Modules\Purchasing\Livewire\Orders;

use App\Modules\Purchasing\Helpers\PurchaseOrderOptions;
use App\Modules\Purchasing\Models\PurchaseOrder;
use App\Modules\Purchasing\Requests\StorePurchaseOrderRequest;
use App\Modules\Purchasing\Services\PurchaseOrderService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class Create extends Component
{
    use AuthorizesRequests;

    public string $purchase_order_no = '';
    public string $purchase_order_date = '';
    public string $expected_delivery_date = '';
    public string $supplier_id = '';
    public string $supplier_name = '';
    public string $supplier_address = '';
    public string $contact_person = '';
    public string $contact_no = '';
    public string $terms = '';
    public string $remarks = '';
    public string $currency = 'php';
    public string $tax_rate = '0';
    public string $status = 'pending';
    public array $items = [];
    public array $totals = ['subtotal' => 0, 'tax_amount' => 0, 'total_amount' => 0];

    public function mount(): void
    {
        $this->authorize('create', PurchaseOrder::class);
        $this->purchase_order_no = app(PurchaseOrderService::class)->nextPurchaseOrderNo();
        $this->purchase_order_date = now()->toDateString();
        $this->addRow();
    }

    public function updatedSupplierId(): void
    {
        $this->fillSupplierDetails();
    }

    public function updatedItems(): void
    {
        $this->normalizeItemRows();
        $this->recomputeTotals();
    }

    public function updatedTaxRate(): void
    {
        $this->normalizeItemRows();
        $this->recomputeTotals();
    }

    public function updatedPurchaseOrderNo(): void
    {
        $this->purchase_order_no = strtoupper(trim($this->purchase_order_no));
    }

    public function updated($property, $value): void
    {
        if (preg_match('/^items\.(\d+)\.item_id$/', (string) $property, $matches)) {
            $this->fillSelectedItem((int) $matches[1]);
            $this->recomputeTotals();
        }
    }

    public function addRow(): void
    {
        $this->items[] = [
            'item_id' => '',
            'item_name' => '',
            'item_image' => '',
            'description' => '',
            'lead_time' => '',
            'unit_measure_id' => '',
            'quantity' => '1',
            'price' => '0.00',
            'remarks' => '',
            'subtotal' => '0.00',
            'tax_amount' => '0.00',
            'total' => '0.00',
        ];
    }

    public function removeRow(int $index): void
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->recomputeTotals();
    }

    public function save(): mixed
    {
        $this->normalizeItemRows();
        $this->recomputeTotals();
        $this->validate(StorePurchaseOrderRequest::rulesArray());
        app(PurchaseOrderService::class)->create($this->payload());
        session()->flash('toast', 'Purchase order saved successfully.');

        return redirect()->route('purchasing.orders.index');
    }

    public function render()
    {
        $service = app(PurchaseOrderService::class);

        return view('modules.purchasing.orders.livewire.form', [
            'suppliers' => $service->suppliers(),
            'itemOptions' => $service->items($this->supplier_id ? (int) $this->supplier_id : null),
            'unitMeasures' => $service->unitMeasures(),
            'statuses' => PurchaseOrderOptions::STATUSES,
            'currencies' => PurchaseOrderOptions::CURRENCIES,
            'taxRates' => PurchaseOrderOptions::TAX_RATES,
            'editing' => false,
        ]);
    }

    protected function fillSupplierDetails(): void
    {
        if (! $this->supplier_id) {
            return;
        }

        $details = app(PurchaseOrderService::class)->supplierDetails((int) $this->supplier_id);
        foreach ($details ?? [] as $key => $value) {
            $this->{$key} = (string) $value;
        }
    }

    protected function normalizeItemRows(): void
    {
        $items = app(PurchaseOrderService::class)->items($this->supplier_id ? (int) $this->supplier_id : null)->keyBy('id');
        foreach ($this->items as $index => $row) {
            $item = $items->get((int) ($row['item_id'] ?? 0));
            if ($item) {
                $this->items[$index]['item_name'] = $item->item_name;
                $this->items[$index]['item_image'] = (string) $item->item_image;
                if ((float) ($row['price'] ?? 0) <= 0) {
                    $this->items[$index]['price'] = number_format((float) ($item->item_price ?: $item->supplier_price), 2, '.', '');
                }
            }
            $subtotal = round((int) ($this->items[$index]['quantity'] ?? 0) * (float) ($this->items[$index]['price'] ?? 0), 2);
            $tax = round($subtotal * ((float) $this->tax_rate / 100), 2);
            $this->items[$index]['quantity'] = (string) max(1, (int) ($this->items[$index]['quantity'] ?? 1));
            $this->items[$index]['price'] = number_format(max(0, (float) ($this->items[$index]['price'] ?? 0)), 2, '.', '');
            $this->items[$index]['subtotal'] = number_format($subtotal, 2, '.', '');
            $this->items[$index]['tax_amount'] = number_format($tax, 2, '.', '');
            $this->items[$index]['total'] = number_format($subtotal + $tax, 2, '.', '');
        }
    }

    protected function recomputeTotals(): void
    {
        $this->totals = app(PurchaseOrderService::class)->totals($this->items, $this->tax_rate);
    }

    protected function fillSelectedItem(int $index): void
    {
        $itemId = (int) ($this->items[$index]['item_id'] ?? 0);
        if (! $itemId) {
            return;
        }

        $item = app(PurchaseOrderService::class)
            ->items($this->supplier_id ? (int) $this->supplier_id : null)
            ->firstWhere('id', $itemId);

        if (! $item) {
            return;
        }

        $this->items[$index]['item_name'] = $item->item_name;
        $this->items[$index]['item_image'] = (string) $item->item_image;
        $this->items[$index]['price'] = number_format((float) ($item->item_price ?: $item->supplier_price), 2, '.', '');

        $quantity = max(1, (int) ($this->items[$index]['quantity'] ?? 1));
        $subtotal = round($quantity * (float) $this->items[$index]['price'], 2);
        $tax = round($subtotal * ((float) $this->tax_rate / 100), 2);

        $this->items[$index]['quantity'] = (string) $quantity;
        $this->items[$index]['subtotal'] = number_format($subtotal, 2, '.', '');
        $this->items[$index]['tax_amount'] = number_format($tax, 2, '.', '');
        $this->items[$index]['total'] = number_format($subtotal + $tax, 2, '.', '');
    }

    protected function payload(): array
    {
        return [
            'purchase_order_no' => $this->purchase_order_no,
            'purchase_order_date' => $this->purchase_order_date,
            'expected_delivery_date' => $this->expected_delivery_date,
            'supplier_id' => $this->supplier_id,
            'remarks' => $this->remarks,
            'currency' => $this->currency,
            'tax_rate' => $this->tax_rate,
            'status' => $this->status,
            'items' => $this->items,
        ];
    }
}
