<?php

namespace App\Modules\Items\Livewire\Concerns;

use App\Modules\Items\Models\Item;
use App\Modules\Items\Requests\StoreItemRequest;
use App\Modules\Items\Requests\UpdateItemRequest;
use App\Modules\Items\Services\ItemPricingService;
use App\Modules\Items\Services\ItemService;
use App\Modules\BusinessPartners\Models\BusinessPartner;
use App\Modules\BusinessPartners\Services\BusinessPartnerService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

trait ManagesItemForm
{
    public ?Item $itemRecord = null;

    public string $item_name = '';

    public string $item_code = '';

    public string $item_type = '';

    public bool $showQuickItemTypeModal = false;

    public string $quick_item_type_name = '';

    public string $item_source = 'local';

    public string $supplier_id = '';

    public bool $showQuickSupplierModal = false;

    public string $quick_supplier_company_name = '';

    public string $quick_supplier_company_code = '';

    public string $quick_supplier_contact_person = '';

    public string $quick_supplier_company_address = '';

    public string $supplier_price = '0.00';

    public string $percentage = '0.00';

    public string $item_price = '0.00';

    public int $available_stock = 0;

    public int $reorder_point = 0;

    public string $taxable = 'no';

    public string $status = 'active';

    public ?string $existing_item_image = null;

    public $item_image_upload = null;

    protected function formRules(): array
    {
        return $this->itemRecord
            ? UpdateItemRequest::rulesArray($this->itemRecord->id)
            : StoreItemRequest::rulesArray();
    }

    /**
     * @return array<string, string>
     */
    protected function validationAttributes(): array
    {
        return [
            'item_name' => 'item name',
            'item_code' => 'item code',
            'item_type' => 'item type',
            'item_source' => 'item source',
            'supplier_id' => 'supplier',
            'supplier_price' => 'supplier price',
            'available_stock' => 'available stock',
            'reorder_point' => 'reorder point',
            'item_image_upload' => 'item image',
        ];
    }

    public function updatedItemCode(string $value): void
    {
        $this->item_code = strtoupper($value);
    }

    public function updatedItemType(): void
    {
        if ($this->itemRecord) {
            return;
        }

        $this->refreshGeneratedItemCode();
    }

    public function updatedSupplierPrice(): void
    {
        $this->recalculateItemPrice();
    }

    public function updatedPercentage(): void
    {
        $this->recalculateItemPrice();
    }

    public function updatedItemImageUpload(): void
    {
        $this->validateOnly('item_image_upload', $this->formRules(), [], $this->validationAttributes());
    }

    public function updatedQuickSupplierCompanyCode(string $value): void
    {
        $this->quick_supplier_company_code = strtoupper($value);
    }

    public function openQuickSupplierModal(): void
    {
        $this->authorize('create', BusinessPartner::class);
        $this->resetQuickSupplierFields();
        $this->showQuickSupplierModal = true;
    }

    public function openQuickItemTypeModal(): void
    {
        $this->authorize('create', Item::class);
        $this->quick_item_type_name = '';
        $this->showQuickItemTypeModal = true;
    }

    public function closeQuickItemTypeModal(): void
    {
        $this->showQuickItemTypeModal = false;
        $this->resetValidation('quick_item_type_name');
    }

    public function createQuickItemType(): void
    {
        $this->authorize('create', Item::class);

        $payload = $this->validate([
            'quick_item_type_name' => ['required', 'string', 'max:100', Rule::unique('item_types', 'name')],
        ], [], [
            'quick_item_type_name' => 'item type name',
        ]);

        $itemType = app(ItemService::class)->createItemType($payload['quick_item_type_name']);

        $this->item_type = $itemType->name;
        $this->refreshGeneratedItemCode();
        $this->quick_item_type_name = '';
        $this->showQuickItemTypeModal = false;
    }

    public function closeQuickSupplierModal(): void
    {
        $this->showQuickSupplierModal = false;
        $this->resetValidation([
            'quick_supplier_company_name',
            'quick_supplier_company_code',
            'quick_supplier_contact_person',
            'quick_supplier_company_address',
        ]);
    }

    public function createQuickSupplier(): void
    {
        $this->authorize('create', BusinessPartner::class);

        $payload = $this->validate([
            'quick_supplier_company_name' => ['required', 'string', 'max:255'],
            'quick_supplier_company_code' => ['required', 'string', 'max:50', Rule::unique('business_partners', 'company_code')],
            'quick_supplier_contact_person' => ['required', 'string', 'max:255'],
            'quick_supplier_company_address' => ['nullable', 'string'],
        ], [], [
            'quick_supplier_company_name' => 'company name',
            'quick_supplier_company_code' => 'company code',
            'quick_supplier_contact_person' => 'contact person',
            'quick_supplier_company_address' => 'company address',
        ]);

        $supplier = app(BusinessPartnerService::class)->create('supplier', [
            'company_name' => $payload['quick_supplier_company_name'],
            'company_code' => $payload['quick_supplier_company_code'],
            'tin_number' => '000-000-000-000',
            'contact_person' => $payload['quick_supplier_contact_person'],
            'contact_no' => '0',
            'credit_limit' => 0,
            'company_address' => $payload['quick_supplier_company_address'] ?? null,
            'under_pesa' => 'no',
            'vatable' => 'non_vat',
            'terms' => 30,
            'status' => 'active',
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        $this->supplier_id = (string) $supplier->id;
        $this->showQuickSupplierModal = false;
        $this->resetQuickSupplierFields();
    }

    protected function recalculateItemPrice(): void
    {
        $this->item_price = number_format(app(ItemPricingService::class)->compute($this->supplier_price, $this->percentage), 2, '.', '');
    }

    private function resetQuickSupplierFields(): void
    {
        $this->quick_supplier_company_name = '';
        $this->quick_supplier_company_code = '';
        $this->quick_supplier_contact_person = '';
        $this->quick_supplier_company_address = '';
    }

    protected function fillFromItem(Item $item): void
    {
        $this->itemRecord = $item;
        $this->item_name = $item->item_name;
        $this->item_code = $item->item_code;
        $this->item_type = $item->item_type;
        $this->item_source = $item->item_source;
        $this->supplier_id = (string) $item->supplier_id;
        $this->supplier_price = number_format((float) $item->supplier_price, 2, '.', '');
        $this->percentage = number_format((float) $item->percentage, 2, '.', '');
        $this->item_price = number_format((float) $item->item_price, 2, '.', '');
        $this->available_stock = $item->available_stock;
        $this->reorder_point = $item->reorder_point;
        $this->taxable = $item->taxable;
        $this->status = $item->status;
        $this->existing_item_image = $item->item_image;
    }

    public function save(): mixed
    {
        if (! $this->itemRecord) {
            $this->refreshGeneratedItemCode();
        }

        $this->recalculateItemPrice();

        $payload = $this->validate(
            $this->formRules(),
            [],
            $this->validationAttributes()
        );

        $payload['item_price'] = $this->item_price;
        $payload['item_source'] = $this->itemSource();
        $payload['updated_by'] = auth()->id();

        if ($this->item_image_upload instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
            if ($this->existing_item_image) {
                Storage::disk('public')->delete($this->existing_item_image);
            }

            $payload['item_image'] = $this->item_image_upload->store('uploads/items/'.$this->itemSource(), 'public');
        } elseif ($this->existing_item_image) {
            $payload['item_image'] = $this->existing_item_image;
        }

        if ($this->itemRecord) {
            $freshItem = Item::query()
                ->where('item_source', $this->itemSource())
                ->find($this->itemRecord->id);

            if (! $freshItem) {
                return redirect()
                    ->route($this->indexRoute())
                    ->with('toast', 'Item record was already deleted or no longer exists.');
            }

            $this->authorize('update', $freshItem);
            app(ItemService::class)->update($freshItem, $payload);
            $message = 'Item updated successfully.';
        } else {
            $this->authorize('create', Item::class);
            $payload['created_by'] = auth()->id();
            app(ItemService::class)->create($payload);
            $message = 'Item created successfully.';
        }

        return redirect()
            ->route($this->indexRoute())
            ->with('toast', $message);
    }

    protected function itemSource(): string
    {
        return $this->item_source;
    }

    protected function indexRoute(): string
    {
        return 'items.index';
    }

    private function refreshGeneratedItemCode(): void
    {
        if (blank($this->item_type)) {
            $this->item_code = '';

            return;
        }

        $this->item_code = app(ItemService::class)->nextItemCode($this->item_type);
    }
}
