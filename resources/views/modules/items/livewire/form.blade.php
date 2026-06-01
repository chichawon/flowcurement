<form wire:submit.prevent="save" class="space-y-5">
    <div class="grid gap-5 xl:grid-cols-[minmax(0,1fr)_22rem]">
        <section class="erp-panel">
            <div class="erp-panel-header">
                <h3 class="text-sm font-semibold text-slate-950">{{ $title }}</h3>
            </div>
            <div class="erp-panel-body space-y-4">
                <div class="grid gap-3 lg:grid-cols-3">
                    <x-admin.form-field label="Item Name" name="item_name" wire:model.blur="item_name" required />
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Item Code</span>
                        <input type="text" wire:model="item_code" readonly class="mt-1 block h-10 w-full rounded-md border-slate-200 bg-slate-100 px-3 text-sm font-semibold uppercase text-slate-950 shadow-sm">
                        <span class="mt-1 block text-xs text-slate-500">Auto-generated based on selected item type.</span>
                        @error('item_code')
                            <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span>
                        @enderror
                    </label>
                    <label class="block">
                        <span class="flex items-center justify-between gap-3">
                            <span class="text-sm font-medium text-slate-700">Item Type</span>
                            @can('create', \App\Modules\Items\Models\Item::class)
                                <button type="button" wire:click="openQuickItemTypeModal" class="inline-flex items-center gap-1.5 rounded-md bg-cyan-600 px-2.5 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-cyan-700 erp-focus-ring">
                                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                    <span>Add Type</span>
                                </button>
                            @endcan
                        </span>
                        <div
                            wire:key="item-type-select-{{ str($item_type)->slug() ?: 'none' }}-{{ $itemTypes->count() }}"
                            x-data="{
                                open: false,
                                query: '',
                                selected: @entangle('item_type').live,
                                itemTypes: @js($itemTypes->map(fn ($type) => [
                                    'id' => $type->name,
                                    'label' => $type->name,
                                ])->values()),
                                filtered() {
                                    const term = this.query.toLowerCase().trim();
                                    return term === ''
                                        ? this.itemTypes
                                        : this.itemTypes.filter((type) => type.label.toLowerCase().includes(term));
                                },
                                selectedLabel() {
                                    return this.itemTypes.find((type) => type.id === String(this.selected))?.label || 'Select item type';
                                },
                                choose(id) {
                                    this.selected = String(id);
                                    this.open = false;
                                    this.query = '';
                                },
                            }"
                            class="relative mt-1"
                            @click.outside="open = false"
                        >
                            <button
                                type="button"
                                class="flex h-10 w-full items-center justify-between gap-3 rounded-md border border-slate-300 bg-white px-3 text-left text-sm shadow-sm erp-focus-ring"
                                @click="open = ! open"
                            >
                                <span class="min-w-0 truncate" :class="selected ? 'text-slate-900' : 'text-slate-400'" x-text="selectedLabel()"></span>
                                <svg class="size-4 shrink-0 text-slate-500 transition-transform" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>

                            <div x-show="open" x-transition class="absolute z-40 mt-1 w-full overflow-hidden rounded-md border border-slate-200 bg-white shadow-lg">
                                <div class="border-b border-slate-200 p-2">
                                    <input type="search" x-model="query" class="block h-9 w-full rounded-md border-slate-300 px-3 text-sm shadow-sm erp-focus-ring" placeholder="Search item type" @keydown.escape.stop="open = false">
                                </div>
                                <div class="max-h-60 overflow-y-auto py-1">
                                    <template x-for="type in filtered()" :key="type.id">
                                        <button type="button" class="flex w-full items-center justify-between gap-3 px-3 py-2 text-left text-sm hover:bg-slate-100" :class="String(selected) === type.id ? 'bg-cyan-50 text-cyan-800' : 'text-slate-700'" @click="choose(type.id)">
                                            <span class="min-w-0 truncate" x-text="type.label"></span>
                                            <svg x-show="String(selected) === type.id" class="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                            </svg>
                                        </button>
                                    </template>
                                    <div x-show="filtered().length === 0" class="px-3 py-4 text-center text-sm text-slate-500">No item types found.</div>
                                </div>
                            </div>
                        </div>
                        @error('item_type')
                            <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span>
                        @enderror
                    </label>
                </div>

                <label class="block">
                    <span class="flex items-center justify-between gap-3">
                        <span class="text-sm font-medium text-slate-700">Supplier</span>
                        @can('create', \App\Modules\BusinessPartners\Models\BusinessPartner::class)
                            <button type="button" wire:click="openQuickSupplierModal" class="inline-flex items-center gap-1.5 rounded-md bg-cyan-600 px-2.5 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-cyan-700 erp-focus-ring">
                                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM4.5 19.5a6 6 0 0 1 12 0v.375c0 .621-.504 1.125-1.125 1.125H5.625A1.125 1.125 0 0 1 4.5 19.875V19.5Z" />
                                </svg>
                                <span>Add Supplier</span>
                            </button>
                        @endcan
                    </span>
                    <div
                        wire:key="supplier-select-{{ $supplier_id ?: 'none' }}-{{ $suppliers->count() }}"
                        x-data="{
                            open: false,
                            query: '',
                            selected: @entangle('supplier_id').live,
                            suppliers: @js($suppliers->map(fn ($supplier) => [
                                'id' => (string) $supplier->id,
                                'label' => $supplier->company_name.' - '.$supplier->company_code,
                            ])->values()),
                            filtered() {
                                const term = this.query.toLowerCase().trim();
                                return term === ''
                                    ? this.suppliers
                                    : this.suppliers.filter((supplier) => supplier.label.toLowerCase().includes(term));
                            },
                            selectedLabel() {
                                return this.suppliers.find((supplier) => supplier.id === String(this.selected))?.label || 'Select supplier';
                            },
                            choose(id) {
                                this.selected = String(id);
                                this.open = false;
                                this.query = '';
                            },
                        }"
                        class="relative mt-1"
                        @click.outside="open = false"
                    >
                        <button
                            type="button"
                            class="flex h-10 w-full items-center justify-between gap-3 rounded-md border border-slate-300 bg-white px-3 text-left text-sm shadow-sm erp-focus-ring"
                            @click="open = ! open"
                        >
                            <span class="min-w-0 truncate" :class="selected ? 'text-slate-900' : 'text-slate-400'" x-text="selectedLabel()"></span>
                            <svg class="size-4 shrink-0 text-slate-500 transition-transform" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                            </svg>
                        </button>

                        <div
                            x-show="open"
                            x-transition
                            class="absolute z-40 mt-1 w-full overflow-hidden rounded-md border border-slate-200 bg-white shadow-lg"
                        >
                            <div class="border-b border-slate-200 p-2">
                                <input
                                    type="search"
                                    x-model="query"
                                    class="block h-9 w-full rounded-md border-slate-300 px-3 text-sm shadow-sm erp-focus-ring"
                                    placeholder="Search supplier"
                                    @keydown.escape.stop="open = false"
                                >
                            </div>

                            <div class="max-h-60 overflow-y-auto py-1">
                                <template x-for="supplier in filtered()" :key="supplier.id">
                                    <button
                                        type="button"
                                        class="flex w-full items-center justify-between gap-3 px-3 py-2 text-left text-sm hover:bg-slate-100"
                                        :class="String(selected) === supplier.id ? 'bg-cyan-50 text-cyan-800' : 'text-slate-700'"
                                        @click="choose(supplier.id)"
                                    >
                                        <span class="min-w-0 truncate" x-text="supplier.label"></span>
                                        <svg x-show="String(selected) === supplier.id" class="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                        </svg>
                                    </button>
                                </template>

                                <div x-show="filtered().length === 0" class="px-3 py-4 text-center text-sm text-slate-500">
                                    No suppliers found.
                                </div>
                            </div>
                        </div>
                    </div>
                    @error('supplier_id')
                        <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span>
                    @enderror
                </label>

                <div class="grid gap-3 lg:grid-cols-3">
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Supplier Price</span>
                        <span class="relative mt-1 block">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex w-10 items-center justify-center rounded-l-md border-r border-slate-200 bg-slate-50 text-sm font-bold text-slate-600">₱</span>
                            <input
                                type="number"
                                name="supplier_price"
                                wire:model.live.debounce.250ms="supplier_price"
                                min="0"
                                step="0.01"
                                required
                                class="block h-10 w-full rounded-md border-slate-300 px-3 pl-12 text-sm shadow-sm erp-focus-ring"
                            >
                        </span>
                        @error('supplier_price')
                            <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span>
                        @enderror
                    </label>
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Markup Percentage</span>
                        <span class="relative mt-1 block">
                            <input
                                type="number"
                                name="percentage"
                                wire:model.live.debounce.250ms="percentage"
                                min="0"
                                step="0.01"
                                required
                                class="block h-10 w-full rounded-md border-slate-300 px-3 pr-10 text-sm shadow-sm erp-focus-ring"
                            >
                            <span class="pointer-events-none absolute inset-y-0 right-0 flex w-11 items-center justify-center rounded-r-md border-l border-slate-200 bg-slate-50 text-lg font-bold leading-none text-slate-600">%</span>
                        </span>
                        @error('percentage')
                            <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span>
                        @enderror
                    </label>
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Computed Item Price</span>
                        <span class="relative mt-1 block">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex w-10 items-center justify-center rounded-l-md border-r border-slate-200 bg-slate-200 text-sm font-bold text-slate-600">₱</span>
                            <input type="text" value="{{ number_format((float) $item_price, 2) }}" readonly class="block h-10 w-full rounded-md border-slate-200 bg-slate-100 px-3 pl-12 text-sm font-semibold text-slate-950 shadow-sm">
                        </span>
                    </label>
                </div>

                <div class="grid gap-3 lg:grid-cols-4">
                    <x-admin.form-field label="Available Stock" name="available_stock" type="number" wire:model.blur="available_stock" min="0" step="1" required />
                    <x-admin.form-field label="Reorder Point" name="reorder_point" type="number" wire:model.blur="reorder_point" min="0" step="1" required />
                    <x-admin.select-field label="Taxable" name="taxable" wire:model.live="taxable" required>
                        <option value="yes">Yes</option>
                        <option value="no">No</option>
                    </x-admin.select-field>
                    <x-admin.select-field label="Status" name="status" wire:model.live="status" required>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </x-admin.select-field>
                </div>
            </div>
        </section>

        <section class="erp-panel">
            <div class="erp-panel-header">
                <h3 class="text-sm font-semibold text-slate-950">Item Image</h3>
            </div>
            <div
                class="erp-panel-body space-y-4"
                x-data="{ localPreview: null }"
                x-init="$watch('localPreview', (value, oldValue) => { if (oldValue) URL.revokeObjectURL(oldValue) })"
            >
                <div class="overflow-hidden rounded-lg border border-slate-200 bg-slate-50">
                    <template x-if="localPreview">
                        <img :src="localPreview" alt="Item image preview" class="h-80 w-full bg-white object-contain">
                    </template>

                    @if ($item_image_upload)
                        <div x-show="! localPreview" class="grid h-80 place-items-center px-4 text-center text-sm text-slate-500">
                            <span>
                                <span class="block font-semibold text-slate-700">{{ $item_image_upload->getClientOriginalName() }}</span>
                                <span class="mt-1 block text-xs">Image selected.</span>
                            </span>
                        </div>
                    @elseif ($existing_item_image)
                        <img x-show="! localPreview" src="{{ \App\Modules\Items\Helpers\ItemImage::url($existing_item_image) }}" alt="Current item image" class="h-80 w-full bg-white object-contain">
                    @else
                        <div x-show="! localPreview" class="grid h-80 place-items-center text-sm text-slate-400">No image selected</div>
                    @endif
                </div>
                <label class="block">
                    <span class="text-sm font-medium text-slate-700">Upload Image</span>
                    <input
                        type="file"
                        wire:model="item_image_upload"
                        accept="image/*,.jpg,.jpeg,.png,.gif,.bmp,.webp,.svg,.avif,.heic,.heif,.tif,.tiff,.ico"
                        class="mt-1 block w-full text-sm text-slate-600 file:mr-3 file:rounded-md file:border-0 file:bg-slate-950 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-slate-800"
                        @change="localPreview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null"
                    >
                    @error('item_image_upload')
                        <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span>
                    @enderror
                </label>
            </div>
        </section>
    </div>

    <div
        x-data="{ open: @entangle('showQuickSupplierModal').live }"
        x-show="open"
        x-transition.opacity
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center px-4 py-6 sm:px-0"
        role="dialog"
        aria-modal="true"
    >
        <div class="absolute inset-0 bg-slate-950/60" @click="$wire.closeQuickSupplierModal()"></div>

        <div class="relative w-full max-w-2xl rounded-xl bg-white shadow-2xl">
            <div class="border-b border-slate-200 px-5 py-4">
                <h3 class="text-base font-semibold text-slate-950">Add New Supplier</h3>
                <p class="mt-1 text-sm text-slate-500">Create a supplier and select it for this item.</p>
            </div>

            <div class="space-y-4 px-5 py-4">
                <div class="grid gap-3 sm:grid-cols-2">
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Company Name</span>
                        <input type="text" wire:model.blur="quick_supplier_company_name" class="mt-1 block h-10 w-full rounded-md border-slate-300 px-3 text-sm shadow-sm erp-focus-ring">
                        @error('quick_supplier_company_name')
                            <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span>
                        @enderror
                    </label>

                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Company Code</span>
                        <input type="text" wire:model.blur="quick_supplier_company_code" class="mt-1 block h-10 w-full rounded-md border-slate-300 px-3 text-sm uppercase shadow-sm erp-focus-ring">
                        @error('quick_supplier_company_code')
                            <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span>
                        @enderror
                    </label>
                </div>

                <label class="block">
                    <span class="text-sm font-medium text-slate-700">Contact Person</span>
                    <input type="text" wire:model.blur="quick_supplier_contact_person" class="mt-1 block h-10 w-full rounded-md border-slate-300 px-3 text-sm shadow-sm erp-focus-ring">
                    @error('quick_supplier_contact_person')
                        <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span>
                    @enderror
                </label>

                <label class="block">
                    <span class="text-sm font-medium text-slate-700">Company Address</span>
                    <textarea wire:model.blur="quick_supplier_company_address" rows="3" class="mt-1 block w-full rounded-md border-slate-300 px-3 py-2 text-sm shadow-sm erp-focus-ring"></textarea>
                    @error('quick_supplier_company_address')
                        <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span>
                    @enderror
                </label>
            </div>

            <div class="flex items-center justify-end gap-2 border-t border-slate-200 px-5 py-4">
                <button type="button" wire:click="closeQuickSupplierModal" class="rounded-md border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancel</button>
                <button type="button" wire:click="createQuickSupplier" class="rounded-md bg-slate-950 px-3 py-2 text-sm font-semibold text-white hover:bg-slate-800">Save Supplier</button>
            </div>
        </div>
    </div>

    <div
        x-data="{ open: @entangle('showQuickItemTypeModal').live }"
        x-show="open"
        x-transition.opacity
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center px-4 py-6 sm:px-0"
        role="dialog"
        aria-modal="true"
    >
        <div class="absolute inset-0 bg-slate-950/60" @click="$wire.closeQuickItemTypeModal()"></div>

        <div class="relative w-full max-w-md rounded-xl bg-white shadow-2xl">
            <div class="border-b border-slate-200 px-5 py-4">
                <h3 class="text-base font-semibold text-slate-950">Add Item Type</h3>
                <p class="mt-1 text-sm text-slate-500">Create a type and select it for this item.</p>
            </div>

            <div class="px-5 py-4">
                <label class="block">
                    <span class="text-sm font-medium text-slate-700">Item Type Name</span>
                    <input type="text" wire:model.blur="quick_item_type_name" class="mt-1 block h-10 w-full rounded-md border-slate-300 px-3 text-sm shadow-sm erp-focus-ring">
                    @error('quick_item_type_name')
                        <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span>
                    @enderror
                </label>
            </div>

            <div class="flex items-center justify-end gap-2 border-t border-slate-200 px-5 py-4">
                <button type="button" wire:click="closeQuickItemTypeModal" class="rounded-md border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancel</button>
                <button type="button" wire:click="createQuickItemType" class="rounded-md bg-slate-950 px-3 py-2 text-sm font-semibold text-white hover:bg-slate-800">Save Item Type</button>
            </div>
        </div>
    </div>

    <div class="sticky bottom-0 flex items-center justify-end gap-2 border-t border-slate-200 bg-slate-100/95 py-4 backdrop-blur">
        <a href="{{ $cancelRoute }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancel</a>
        <button type="submit" class="rounded-md bg-slate-950 px-4 py-2 text-sm font-semibold text-white shadow-sm erp-focus-ring hover:bg-slate-800">
            {{ $submitLabel }}
        </button>
    </div>
</form>
