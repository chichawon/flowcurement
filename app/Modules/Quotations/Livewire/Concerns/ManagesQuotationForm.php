<?php

namespace App\Modules\Quotations\Livewire\Concerns;

use App\Modules\BusinessPartners\Models\BusinessPartner;
use App\Modules\Items\Models\Item;
use App\Modules\Quotations\Models\Quotation;
use App\Modules\Quotations\Requests\StoreQuotationRequest;
use App\Modules\Quotations\Requests\UpdateQuotationRequest;
use App\Modules\Quotations\Services\QuotationService;
use Illuminate\Validation\Rule;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

trait ManagesQuotationForm
{
    public ?Quotation $quotationRecord = null;

    public string $quotation_no = '';

    public string $quotation_date = '';

    public string $validity_date = '';

    public string $business_partner_id = '';

    public string $company_address = '';

    public string $contact_person = '';

    public string $contact_no = '';

    public string $agent_name = '';

    public string $prepared_by_name = '';

    public string $remarks = '';

    public string $currency = 'php';

    public string $tax_rate = '0';

    public float $subtotal = 0;

    public float $tax_amount = 0;

    public float $total_amount = 0;

    public array $items = [];

    public bool $showQuickItemModal = false;

    public string $quick_item_source = 'local';

    public string $quick_item_name = '';

    public string $quick_supplier_price = '0.00';

    public string $quick_markup_percentage = '0.00';

    public string $quick_item_price = '0.00';

    public $quick_item_image_upload = null;

    protected function formRules(): array
    {
        return $this->quotationRecord
            ? UpdateQuotationRequest::rulesArray()
            : StoreQuotationRequest::rulesArray();
    }

    protected function validationAttributes(): array
    {
        return [
            'business_partner_id' => 'company name',
            'agent_name' => 'agent name',
            'tax_rate' => 'tax rate',
            'items.*.item_id' => 'item name',
            'items.*.unit_measure_id' => 'unit measure',
            'items.*.item_price' => 'item price',
            'items.*.quantity' => 'quantity',
            'items.*.lead_time' => 'lead time',
        ];
    }

    protected function bootQuotationForm(): void
    {
        if ($this->items === []) {
            $this->items = [$this->blankItemRow()];
        }

        if ($this->quotation_date === '') {
            $this->quotation_date = now()->toDateString();
        }

        if ($this->remarks === '') {
            $this->remarks = $this->defaultRemarksTemplate();
        }

        $this->prepared_by_name = auth()->user()?->name ?? 'Current user';
        $this->recalculateTotals();
    }

    protected function fillFromQuotation(Quotation $quotation): void
    {
        $this->quotationRecord = $quotation->load(['items']);
        $this->quotation_no = $quotation->quotation_no;
        $this->quotation_date = $quotation->quotation_date?->toDateString() ?? now()->toDateString();
        $this->validity_date = $quotation->validity_date?->toDateString() ?? '';
        $this->business_partner_id = (string) $quotation->business_partner_id;
        $this->company_address = (string) $quotation->company_address;
        $this->contact_person = (string) $quotation->contact_person;
        $this->contact_no = (string) $quotation->contact_no;
        $this->agent_name = $quotation->agent_name;
        $this->remarks = (string) $quotation->remarks;
        $this->currency = $quotation->currency;
        $this->tax_rate = (string) (float) $quotation->tax_rate;
        $this->items = $quotation->items->map(fn ($item): array => [
            'item_id' => (string) $item->item_id,
            'item_image' => (string) ($item->item?->item_image ?? ''),
            'description' => (string) $item->description,
            'lead_time' => (string) $item->lead_time,
            'unit_measure_id' => (string) $item->unit_measure_id,
            'item_price' => number_format((float) $item->item_price, 2, '.', ''),
            'quantity' => (string) (float) $item->quantity,
            'total' => number_format((float) $item->total, 2, '.', ''),
        ])->values()->all();

        $this->recalculateTotals();
    }

    public function updatedBusinessPartnerId(): void
    {
        $client = BusinessPartner::query()
            ->clients()
            ->where('status', 'active')
            ->find($this->business_partner_id);

        $this->company_address = (string) ($client?->company_address ?? '');
        $this->contact_person = (string) ($client?->contact_person ?? '');
        $this->contact_no = (string) ($client?->contact_no ?? '');
        $this->agent_name = (string) ($client?->agent_name ?? '');
    }

    public function updatedTaxRate(): void
    {
        $this->recalculateTotals();
    }

    public function updatedItems($value, ?string $key = null): void
    {
        if ($key === null) {
            $this->recalculateTotals();

            return;
        }

        [$index, $field] = array_pad(explode('.', $key), 2, null);

        if (! isset($this->items[(int) $index])) {
            return;
        }

        if ($field === 'item_id') {
            $this->fillItemPrice((int) $index);
        }

        if (in_array($field, ['item_id', 'item_price', 'quantity'], true)) {
            $this->recalculateRow((int) $index);
            $this->recalculateTotals();
        }
    }

    public function addRow(): void
    {
        $this->items[] = $this->blankItemRow();
        $this->recalculateTotals();
    }

    public function openQuickItemModal(): void
    {
        $this->authorize('create', Item::class);
        $this->showQuickItemModal = true;
    }

    public function closeQuickItemModal(): void
    {
        $this->showQuickItemModal = false;
        $this->quick_item_image_upload = null;
        $this->resetValidation(['quick_item_name', 'quick_supplier_price', 'quick_markup_percentage', 'quick_item_image_upload']);
    }

    public function updatedQuickSupplierPrice(): void
    {
        $this->recalculateQuickItemPrice();
    }

    public function updatedQuickMarkupPercentage(): void
    {
        $this->recalculateQuickItemPrice();
    }

    public function createQuickItem(): void
    {
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

        $item = app(QuotationService::class)->createQuickItem([
            'item_source' => $payload['quick_item_source'],
            'item_name' => $payload['quick_item_name'],
            'supplier_price' => $payload['quick_supplier_price'],
            'percentage' => $payload['quick_markup_percentage'],
            'item_image' => $imagePath,
        ]);

        $this->items[] = [
            'item_id' => (string) $item->id,
            'item_image' => (string) ($item->item_image ?? ''),
            'description' => $item->item_name,
            'lead_time' => '',
            'unit_measure_id' => '',
            'item_price' => number_format((float) $item->item_price, 2, '.', ''),
            'quantity' => '1',
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

    public function removeRow(int $index): void
    {
        if (count($this->items) === 1) {
            $this->items = [$this->blankItemRow()];
        } else {
            unset($this->items[$index]);
            $this->items = array_values($this->items);
        }

        $this->recalculateTotals();
    }

    public function save(): mixed
    {
        $this->recalculateTotals();

        $payload = $this->validate($this->formRules(), [], $this->validationAttributes());
        $payload['company_address'] = $this->company_address;
        $payload['contact_person'] = $this->contact_person;
        $payload['contact_no'] = $this->contact_no;
        $payload['prepared_by'] = $this->quotationRecord?->prepared_by ?? auth()->id();
        $payload['updated_by'] = auth()->id();

        if ($this->quotationRecord) {
            $freshQuotation = Quotation::query()->find($this->quotationRecord->id);

            if (! $freshQuotation) {
                return redirect()->route('quotations.index')->with('toast', 'Quotation record was already deleted or no longer exists.');
            }

            if ($freshQuotation->reference_sales_order_id) {
                return redirect()->route('quotations.index')->with('toast', 'This quotation is already referenced by a Sales Order and can no longer be edited.');
            }

            $this->authorize('update', $freshQuotation);
            app(QuotationService::class)->update($freshQuotation, $payload);
            $message = 'Quotation updated successfully.';
        } else {
            $this->authorize('create', Quotation::class);
            $payload['created_by'] = auth()->id();
            app(QuotationService::class)->create($payload);
            $message = 'Quotation created successfully.';
        }

        return redirect()->route('quotations.index')->with('toast', $message);
    }

    private function blankItemRow(): array
    {
        return [
            'item_id' => '',
            'item_image' => '',
            'description' => '',
            'lead_time' => '',
            'unit_measure_id' => '',
            'item_price' => '0.00',
            'quantity' => '1',
            'total' => '0.00',
        ];
    }

    private function fillItemPrice(int $index): void
    {
        $item = Item::query()->where('status', 'active')->find($this->items[$index]['item_id'] ?? null);

        if (! $item) {
            $this->items[$index]['item_price'] = '0.00';
            $this->items[$index]['item_image'] = '';

            return;
        }

        $this->items[$index]['item_price'] = number_format((float) $item->item_price, 2, '.', '');
        $this->items[$index]['item_image'] = (string) ($item->item_image ?? '');
        $this->items[$index]['description'] = $this->items[$index]['description'] ?: $item->item_name;
    }

    private function recalculateRow(int $index): void
    {
        if (! isset($this->items[$index])) {
            return;
        }

        $price = (float) ($this->items[$index]['item_price'] ?? 0);
        $quantity = (float) ($this->items[$index]['quantity'] ?? 0);

        $this->items[$index]['total'] = number_format(round($price * $quantity, 2), 2, '.', '');
    }

    private function recalculateTotals(): void
    {
        foreach (array_keys($this->items) as $index) {
            $this->recalculateRow((int) $index);
        }

        $totals = app(QuotationService::class)->totals($this->items, $this->tax_rate);

        $this->subtotal = $totals['subtotal'];
        $this->tax_amount = $totals['tax_amount'];
        $this->total_amount = $totals['total_amount'];
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

    private function defaultRemarksTemplate(): string
    {
        return "*Notes\n\n    1. Items not included Packaging, Inventory\n    2. Advanced payment of 30% balance in one month\n    3. Minimum Quantity 2000, pieces.";
    }
}
