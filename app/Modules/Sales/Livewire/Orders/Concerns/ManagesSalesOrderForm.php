<?php

namespace App\Modules\Sales\Livewire\Orders\Concerns;

use App\Modules\BusinessPartners\Models\BusinessPartner;
use App\Modules\Items\Models\Item;
use App\Modules\Quotations\Models\Quotation;
use App\Modules\Sales\Models\SalesOrder;
use App\Modules\Sales\Requests\StoreSalesOrderRequest;
use App\Modules\Sales\Requests\UpdateSalesOrderRequest;
use App\Modules\Sales\Services\SalesOrderService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

trait ManagesSalesOrderForm
{
    public ?SalesOrder $salesOrderRecord = null;

    public string $sales_order_no = '';
    public string $order_date = '';
    public int $no_of_days = 0;
    public string $delivery_date = '';
    public string $customer_po = '';
    public string $agent_name = '';
    public string $remarks = '';
    public string $business_partner_id = '';
    public int $terms = 30;
    public string $company_address = '';
    public string $contact_person = '';
    public string $contact_no = '';
    public string $quotation_id = '';
    public string $currency = 'php';
    public string $tax_rate = '0';
    public string $status = 'pending';
    public float $subtotal = 0;
    public float $tax_amount = 0;
    public float $total_amount = 0;
    public ?string $existing_po_attachment = null;
    public $po_attachment_upload = null;
    public array $items = [];

    public bool $showQuickItemModal = false;
    public string $quick_item_source = 'local';
    public string $quick_item_name = '';
    public string $quick_supplier_price = '0.00';
    public string $quick_markup_percentage = '0.00';
    public string $quick_item_price = '0.00';
    public $quick_item_image_upload = null;

    public bool $showQuotationModal = false;
    public string $selected_quotation_id = '';
    public bool $attachmentOnlyMode = false;

    protected function formRules(): array
    {
        return $this->salesOrderRecord
            ? UpdateSalesOrderRequest::rulesArray($this->salesOrderRecord->id)
            : StoreSalesOrderRequest::rulesArray();
    }

    protected function validationAttributes(): array
    {
        return [
            'business_partner_id' => 'company name',
            'no_of_days' => 'number of days',
            'po_attachment_upload' => 'P.O. attachment',
            'items.*.item_id' => 'item',
            'items.*.order_quantity' => 'order quantity',
            'items.*.unit_measure_id' => 'unit',
            'items.*.lead_time' => 'lead time',
        ];
    }

    protected function bootSalesOrderForm(): void
    {
        if ($this->order_date === '') {
            $this->order_date = now()->toDateString();
        }

        if ($this->delivery_date === '') {
            $this->delivery_date = app(SalesOrderService::class)->deliveryDate($this->order_date, $this->no_of_days);
        }

        if ($this->remarks === '') {
            $this->remarks = $this->defaultRemarksTemplate();
        }

        if ($this->items === []) {
            $this->items = [$this->blankItemRow()];
        }
        $this->ensureItemRowKeys();

        $this->sales_order_no = $this->sales_order_no ?: app(SalesOrderService::class)->nextSalesOrderNo();
        $this->recalculateTotals();
    }

    protected function fillFromSalesOrder(SalesOrder $salesOrder): void
    {
        $salesOrder->load(['items']);
        $this->salesOrderRecord = $salesOrder;
        $this->attachmentOnlyMode = app(SalesOrderService::class)->isAttachmentOnlyMode($salesOrder);
        $this->sales_order_no = $salesOrder->sales_order_no;
        $this->order_date = $salesOrder->order_date?->toDateString() ?? now()->toDateString();
        $this->no_of_days = (int) $salesOrder->no_of_days;
        $this->delivery_date = $salesOrder->delivery_date?->toDateString() ?? '';
        $this->customer_po = (string) $salesOrder->customer_po;
        $this->agent_name = $salesOrder->agent_name;
        $this->remarks = (string) $salesOrder->remarks;
        $this->business_partner_id = (string) $salesOrder->business_partner_id;
        $this->terms = (int) $salesOrder->terms;
        $this->company_address = (string) $salesOrder->company_address;
        $this->contact_person = (string) $salesOrder->contact_person;
        $this->contact_no = (string) $salesOrder->contact_no;
        $this->quotation_id = (string) $salesOrder->quotation_id;
        $this->currency = $salesOrder->currency;
        $this->tax_rate = (string) (float) $salesOrder->tax_rate;
        $this->status = $salesOrder->status;
        $this->existing_po_attachment = $salesOrder->po_attachment;
        $this->items = $salesOrder->items->map(fn ($row): array => [
            'id' => (string) $row->id,
            'row_key' => $this->newItemRowKey(),
            'item_id' => (string) $row->item_id,
            'item_image' => (string) ($row->item?->item_image ?? ''),
            'description' => (string) $row->description,
            'lead_time' => (string) $row->lead_time,
            'order_quantity' => (string) (float) $row->order_quantity,
            'unit_measure_id' => (string) $row->unit_measure_id,
            'price' => number_format((float) $row->price, 2, '.', ''),
            'available_stock' => number_format((float) $row->available_stock, 2, '.', ''),
            'remarks' => (string) $row->remarks,
            'total' => number_format((float) $row->total, 2, '.', ''),
        ])->values()->all();
        $this->recalculateTotals();
    }

    public function updatedBusinessPartnerId(): void
    {
        $client = BusinessPartner::query()->clients()->where('status', 'active')->find($this->business_partner_id);
        $this->terms = (int) ($client?->terms ?? 30);
        $this->company_address = (string) ($client?->company_address ?? '');
        $this->contact_person = (string) ($client?->contact_person ?? '');
        $this->contact_no = (string) ($client?->contact_no ?? '');
        $this->agent_name = (string) ($client?->agent_name ?? '');
    }

    public function updatedOrderDate(): void { $this->updateDeliveryDate(); }
    public function updatedNoOfDays(): void { $this->updateDeliveryDate(); }
    public function updatedTaxRate(): void { $this->recalculateTotals(); }

    public function updatedItems($value, ?string $key = null): void
    {
        if ($this->attachmentOnlyMode) {
            return;
        }

        if ($key === null) {
            $this->recalculateTotals();

            return;
        }

        [$index, $field] = array_pad(explode('.', $key), 2, null);
        if (! isset($this->items[(int) $index])) {
            return;
        }

        if ($field === 'order_quantity') {
            $raw = $this->items[(int) $index]['order_quantity'] ?? 1;
            $sanitized = max((int) floor((float) $raw), 1);
            $this->items[(int) $index]['order_quantity'] = (string) $sanitized;
        }

        if ($field === 'item_id') {
            $this->fillItemDetails((int) $index);
        }
        if (in_array($field, ['item_id', 'price', 'order_quantity'], true)) {
            $this->recalculateRow((int) $index);
            $this->recalculateTotals();
        }
    }

    public function addRow(): void
    {
        if ($this->attachmentOnlyMode) {
            session()->flash('toast', 'Items are locked because a delivery receipt was already issued.');
            return;
        }
        $this->items[] = $this->blankItemRow();
    }

    public function removeRow(int $index): void
    {
        if ($this->attachmentOnlyMode) {
            session()->flash('toast', 'Items are locked because a delivery receipt was already issued.');
            return;
        }
        if (count($this->items) === 1) {
            $this->items = [$this->blankItemRow()];
        } else {
            unset($this->items[$index]);
            $this->items = array_values($this->items);
        }
        $this->recalculateTotals();
    }

    public function openQuickItemModal(): void
    {
        if ($this->attachmentOnlyMode) {
            session()->flash('toast', 'Items are locked because a delivery receipt was already issued.');
            return;
        }
        $this->authorize('create', Item::class);
        $this->showQuickItemModal = true;
    }

    public function closeQuickItemModal(): void
    {
        $this->showQuickItemModal = false;
        $this->quick_item_image_upload = null;
        $this->resetValidation(['quick_item_name', 'quick_supplier_price', 'quick_markup_percentage', 'quick_item_image_upload']);
    }

    public function updatedQuickSupplierPrice(): void { $this->recalculateQuickItemPrice(); }
    public function updatedQuickMarkupPercentage(): void { $this->recalculateQuickItemPrice(); }

    public function createQuickItem(): void
    {
        if ($this->attachmentOnlyMode) {
            session()->flash('toast', 'Items are locked because a delivery receipt was already issued.');
            return;
        }
        $this->authorize('create', Item::class);
        $payload = $this->validate([
            'quick_item_source' => ['required', Rule::in(['local', 'import'])],
            'quick_item_name' => ['required', 'string', 'max:255'],
            'quick_supplier_price' => ['required', 'numeric', 'min:0'],
            'quick_markup_percentage' => ['required', 'numeric', 'min:0'],
            'quick_item_image_upload' => ['nullable', 'file', 'extensions:jpg,jpeg,png,gif,bmp,webp,svg,avif,heic,heif,tif,tiff,ico', 'max:10240'],
        ], [], [
            'quick_item_source' => 'item origin',
            'quick_item_name' => 'item name',
            'quick_supplier_price' => 'supplier price',
            'quick_markup_percentage' => 'markup percentage',
            'quick_item_image_upload' => 'item image',
        ]);

        $imagePath = null;
        if ($this->quick_item_image_upload instanceof TemporaryUploadedFile) {
            $imagePath = $this->quick_item_image_upload->store('uploads/items/'.$payload['quick_item_source'], 'public');
        }

        $item = app(SalesOrderService::class)->createQuickItem([
            'item_source' => $payload['quick_item_source'],
            'item_name' => $payload['quick_item_name'],
            'supplier_price' => $payload['quick_supplier_price'],
            'percentage' => $payload['quick_markup_percentage'],
            'item_image' => $imagePath,
        ]);

        $this->ensureItemRowKeys();
        $this->items[] = [
            'id' => null,
            'row_key' => $this->newItemRowKey(),
            'item_id' => (string) $item->id,
            'item_image' => (string) ($item->item_image ?? ''),
            'description' => $item->item_name,
            'lead_time' => '',
            'order_quantity' => '1',
            'unit_measure_id' => '',
            'price' => number_format((float) $item->item_price, 2, '.', ''),
            'available_stock' => number_format((float) $item->available_stock, 2, '.', ''),
            'remarks' => '',
            'total' => number_format((float) $item->item_price, 2, '.', ''),
        ];

        $this->quick_item_name = '';
        $this->quick_supplier_price = '0.00';
        $this->quick_markup_percentage = '0.00';
        $this->quick_item_price = '0.00';
        $this->quick_item_image_upload = null;
        $this->showQuickItemModal = false;
        $this->recalculateTotals();
    }

    public function openQuotationModal(): void
    {
        if ($this->attachmentOnlyMode) {
            session()->flash('toast', 'Items are locked because a delivery receipt was already issued.');
            return;
        }
        $this->showQuotationModal = true;
    }

    public function closeQuotationModal(): void
    {
        $this->showQuotationModal = false;
        $this->selected_quotation_id = '';
    }

    public function loadQuotationItems(): void
    {
        if ($this->attachmentOnlyMode) {
            session()->flash('toast', 'Items are locked because a delivery receipt was already issued.');
            return;
        }
        $quotation = Quotation::query()->find($this->selected_quotation_id);
        if (! $quotation) {
            $this->addError('selected_quotation_id', 'Select an existing quotation.');
            return;
        }

        $loaded = app(SalesOrderService::class)->quotationRows($quotation);
        foreach ($loaded['customer'] as $key => $value) {
            $this->{$key} = $value;
        }
        $this->items = collect($loaded['items'])
            ->map(fn (array $row): array => ['id' => null, 'row_key' => $this->newItemRowKey()] + $row)
            ->values()
            ->all() ?: [$this->blankItemRow()];
        $this->showQuotationModal = false;
        $this->selected_quotation_id = '';
        $this->recalculateTotals();
    }

    public function save(): mixed
    {
        if ($this->attachmentOnlyMode && ! ($this->po_attachment_upload instanceof TemporaryUploadedFile)) {
            $this->addError('po_attachment_upload', 'Upload a new attachment to update this sales order.');
            return null;
        }

        $this->updateDeliveryDate();
        $this->recalculateTotals();

        $payload = $this->validate($this->formRules(), [], $this->validationAttributes());
        $payload['sales_order_no'] = $this->sales_order_no;
        $payload['terms'] = $this->terms;
        $payload['remarks'] = $this->remarks;
        $payload['company_address'] = $this->company_address;
        $payload['contact_person'] = $this->contact_person;
        $payload['contact_no'] = $this->contact_no;
        $payload['quotation_id'] = $this->quotation_id ?: null;
        $payload['updated_by'] = auth()->id();
        $payload['po_attachment'] = $this->existing_po_attachment;

        if ($this->po_attachment_upload instanceof TemporaryUploadedFile) {
            if ($this->existing_po_attachment) {
                Storage::disk('public')->delete($this->existing_po_attachment);
            }
            $payload['po_attachment'] = $this->po_attachment_upload->store('sales-orders/attachments', 'public');
        }

        if ($this->salesOrderRecord) {
            $fresh = SalesOrder::query()->find($this->salesOrderRecord->id);
            if (! $fresh) {
                return redirect()->route('sales.orders.index')->with('toast', 'Sales order no longer exists.');
            }
            $this->authorize('update', $fresh);
            app(SalesOrderService::class)->update($fresh, $payload);
            $message = 'Sales order updated successfully.';
        } else {
            $this->authorize('create', SalesOrder::class);
            $payload['created_by'] = auth()->id();
            app(SalesOrderService::class)->create($payload);
            $message = 'Sales order created successfully.';
        }

        return redirect()->route('sales.orders.index')->with('toast', $message);
    }

    public function attachmentPreviewUrl(): ?string
    {
        if ($this->po_attachment_upload instanceof TemporaryUploadedFile) {
            return $this->po_attachment_upload->temporaryUrl();
        }

        if ($this->existing_po_attachment) {
            return Storage::disk('public')->url($this->existing_po_attachment);
        }

        return null;
    }

    public function attachmentIsImage(): bool
    {
        $name = $this->po_attachment_upload instanceof TemporaryUploadedFile
            ? $this->po_attachment_upload->getClientOriginalName()
            : (string) $this->existing_po_attachment;

        return in_array(strtolower(pathinfo($name, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp'], true);
    }

    public function attachmentIsPdf(): bool
    {
        $name = $this->po_attachment_upload instanceof TemporaryUploadedFile
            ? $this->po_attachment_upload->getClientOriginalName()
            : (string) $this->existing_po_attachment;

        return strtolower(pathinfo($name, PATHINFO_EXTENSION)) === 'pdf';
    }

    private function updateDeliveryDate(): void
    {
        if ($this->order_date !== '') {
            $this->delivery_date = app(SalesOrderService::class)->deliveryDate($this->order_date, $this->no_of_days);
        }
    }

    private function recalculateQuickItemPrice(): void
    {
        $this->quick_item_price = number_format(app(\App\Modules\Items\Services\ItemPricingService::class)->compute($this->quick_supplier_price, $this->quick_markup_percentage), 2, '.', '');
    }

    public function quickItemImagePreviewUrl(): ?string
    {
        if ($this->quick_item_image_upload instanceof TemporaryUploadedFile) {
            return $this->quick_item_image_upload->temporaryUrl();
        }

        return null;
    }

    private function fillItemDetails(int $index): void
    {
        $item = Item::query()->where('status', 'active')->find($this->items[$index]['item_id'] ?? null);
        if (! $item) {
            $this->items[$index]['price'] = '0.00';
            $this->items[$index]['available_stock'] = '0.00';
            $this->items[$index]['item_image'] = '';
            return;
        }
        $this->items[$index]['price'] = number_format((float) $item->item_price, 2, '.', '');
        $this->items[$index]['available_stock'] = number_format((float) $item->available_stock, 2, '.', '');
        $this->items[$index]['item_image'] = (string) ($item->item_image ?? '');
        $this->items[$index]['description'] = $this->items[$index]['description'] ?: $item->item_name;
    }

    private function recalculateRow(int $index): void
    {
        if (! isset($this->items[$index])) {
            return;
        }
        $price = (float) ($this->items[$index]['price'] ?? 0);
        $qty = max((float) ($this->items[$index]['order_quantity'] ?? 0), 0);
        $this->items[$index]['total'] = number_format(round($price * $qty, 2), 2, '.', '');
    }

    private function recalculateTotals(): void
    {
        $this->ensureItemRowKeys();
        foreach (array_keys($this->items) as $index) {
            $this->recalculateRow((int) $index);
        }
        $totals = app(SalesOrderService::class)->totals($this->items, $this->tax_rate);
        $this->subtotal = $totals['subtotal'];
        $this->tax_amount = $totals['tax_amount'];
        $this->total_amount = $totals['total_amount'];
    }

    private function blankItemRow(): array
    {
        return [
            'id' => null,
            'row_key' => $this->newItemRowKey(),
            'item_id' => '',
            'item_image' => '',
            'description' => '',
            'lead_time' => '',
            'order_quantity' => '1',
            'unit_measure_id' => '',
            'price' => '0.00',
            'available_stock' => '0.00',
            'remarks' => '',
            'total' => '0.00',
        ];
    }

    private function ensureItemRowKeys(): void
    {
        foreach ($this->items as $index => $row) {
            if (! is_array($row)) {
                continue;
            }

            $this->items[$index]['row_key'] = $row['row_key'] ?? $row['_key'] ?? $this->newItemRowKey();
            unset($this->items[$index]['_key']);
        }
    }

    private function newItemRowKey(): string
    {
        return 'so-row-'.(string) Str::uuid();
    }

    private function defaultRemarksTemplate(): string
    {
        return "*Notes\n\n    1. Items not included Packaging, Inventory\n    2. Advanced payment of 30% balance in one month\n    3. Minimum Quantity 2000, pieces.";
    }
}
