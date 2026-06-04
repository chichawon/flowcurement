<form wire:submit.prevent="save" class="space-y-5" x-data @quotation-select2-refresh.window="void 0">
    <div class="grid gap-5">
        <section class="erp-panel">
            <div class="erp-panel-header">
                <h3 class="text-sm font-semibold text-slate-950">{{ $title }}</h3>
            </div>

            <div class="erp-panel-body space-y-4">
                <div class="grid gap-3 lg:grid-cols-4">
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Quotation No.</span>
                        <input type="text" value="{{ $quotation_no }}" readonly class="mt-1 block h-10 w-full rounded-md border-slate-200 bg-slate-100 px-3 text-sm font-semibold text-slate-950 shadow-sm">
                    </label>

                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Quotation Date</span>
                        <input type="date" wire:model="quotation_date" readonly class="mt-1 block h-10 w-full rounded-md border-slate-200 bg-slate-100 px-3 text-sm text-slate-700 shadow-sm">
                        @error('quotation_date') <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
                    </label>

                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Validity Date</span>
                        <input type="date" wire:model.blur="validity_date" class="mt-1 block h-10 w-full rounded-md border-slate-300 px-3 text-sm shadow-sm erp-focus-ring">
                        @error('validity_date') <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
                    </label>

                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Prepared By</span>
                        <input type="text" value="{{ $prepared_by_name }}" readonly class="mt-1 block h-10 w-full rounded-md border-slate-200 bg-slate-100 px-3 text-sm text-slate-700 shadow-sm">
                    </label>
                </div>

                <div class="grid gap-3 lg:grid-cols-[minmax(0,1.5fr)_minmax(0,1fr)]">
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Company Name</span>
                        <div
                            wire:key="client-select-{{ $business_partner_id ?: 'none' }}-{{ $clients->count() }}"
                            x-data="{
                                open: false,
                                query: '',
                                selected: @entangle('business_partner_id').live,
                                options: @js($clients->map(fn ($client) => [
                                    'id' => (string) $client->id,
                                    'label' => $client->company_name,
                                ])->values()),
                                filtered() {
                                    const term = this.query.toLowerCase().trim();
                                    return term === '' ? this.options : this.options.filter((option) => option.label.toLowerCase().includes(term));
                                },
                                selectedLabel() {
                                    return this.options.find((option) => option.id === String(this.selected))?.label || 'Select client';
                                },
                                choose(id) {
                                    this.selected = String(id);
                                    this.open = false;
                                    this.query = '';
                                },
                            }"
                            class="relative mt-1 quotation-select2"
                            @click.outside="open = false"
                        >
                            <button type="button" class="flex h-10 w-full items-center justify-between gap-3 rounded-md border border-slate-300 bg-white px-3 text-left text-sm shadow-sm erp-focus-ring" @click="open = ! open">
                                <span class="min-w-0 truncate" :class="selected ? 'text-slate-900' : 'text-slate-400'" x-text="selectedLabel()"></span>
                                <svg class="size-4 shrink-0 text-slate-500 transition-transform" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>
                            <div x-show="open" x-transition class="absolute z-40 mt-1 w-full overflow-hidden rounded-md border border-slate-200 bg-white shadow-lg">
                                <div class="border-b border-slate-200 p-2">
                                    <input type="search" x-model="query" class="block h-9 w-full rounded-md border-slate-300 px-3 text-sm shadow-sm erp-focus-ring" placeholder="Search client" @keydown.escape.stop="open = false">
                                </div>
                                <div class="max-h-60 overflow-y-auto py-1">
                                    <template x-for="option in filtered()" :key="option.id">
                                        <button type="button" class="flex w-full items-center justify-between gap-3 px-3 py-2 text-left text-sm hover:bg-slate-100" :class="String(selected) === option.id ? 'bg-cyan-50 text-cyan-800' : 'text-slate-700'" @click="choose(option.id)">
                                            <span class="min-w-0 truncate" x-text="option.label"></span>
                                            <svg x-show="String(selected) === option.id" class="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                            </svg>
                                        </button>
                                    </template>
                                    <div x-show="filtered().length === 0" class="px-3 py-4 text-center text-sm text-slate-500">No clients found.</div>
                                </div>
                            </div>
                        </div>
                        @error('business_partner_id') <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
                    </label>

                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Agent Name <span class="text-red-600">*</span></span>
                        <input type="text" wire:model="agent_name" readonly class="mt-1 block h-10 w-full rounded-md border-slate-200 bg-slate-100 px-3 text-sm text-slate-700 shadow-sm">
                        @error('agent_name') <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
                    </label>
                </div>

                <div class="grid gap-3 lg:grid-cols-3">
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Company Address</span>
                        <input type="text" wire:model="company_address" readonly class="mt-1 block h-10 w-full rounded-md border-slate-200 bg-slate-100 px-3 text-sm text-slate-700 shadow-sm">
                    </label>
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Contact Person</span>
                        <input type="text" wire:model="contact_person" readonly class="mt-1 block h-10 w-full rounded-md border-slate-200 bg-slate-100 px-3 text-sm text-slate-700 shadow-sm">
                    </label>
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Contact No.</span>
                        <input type="text" wire:model="contact_no" readonly class="mt-1 block h-10 w-full rounded-md border-slate-200 bg-slate-100 px-3 text-sm text-slate-700 shadow-sm">
                    </label>
                </div>

                <label class="block">
                    <span class="text-sm font-medium text-slate-700">Remarks</span>
                    <textarea wire:model.blur="remarks" rows="3" class="mt-1 block w-full rounded-md border-slate-300 px-3 py-2 text-sm shadow-sm erp-focus-ring"></textarea>
                    @error('remarks') <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
                </label>
            </div>
        </section>
    </div>

    <section class="erp-panel" x-data="{ imagePreviewOpen: false, imagePreviewUrl: '', imagePreviewTitle: '' }">
        <div class="erp-panel-header flex items-center justify-between gap-3">
            <h3 class="text-sm font-semibold text-slate-950">Item Rows</h3>
            <div class="flex flex-wrap items-center gap-2">
                <button type="button" wire:click="openQuickItemModal" class="inline-flex items-center rounded-md bg-cyan-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-cyan-700 erp-focus-ring">
                    Add New Item
                </button>
                <button type="button" wire:click="addRow" class="inline-flex items-center gap-1.5 rounded-md bg-slate-950 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-slate-800 erp-focus-ring">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Add Row
                </button>
            </div>
        </div>

        <div class="erp-panel-body">
            <div class="relative overflow-visible border border-slate-400 bg-white">
                <table class="w-full table-fixed border-collapse text-sm">
                    <colgroup>
                        <col class="w-[21%]">
                        <col class="w-[20%]">
                        <col class="w-[12%]">
                        <col class="w-[13%]">
                        <col class="w-[12%]">
                        <col class="w-[8%]">
                        <col class="w-[12%]">
                        <col class="w-14">
                    </colgroup>
                    <thead class="bg-slate-200 text-xs font-bold uppercase text-slate-700">
                        <tr>
                            <th class="border border-slate-400 px-2 py-2 text-left">Item</th>
                            <th class="border border-slate-400 px-2 py-2 text-left">Description</th>
                            <th class="border border-slate-400 px-2 py-2 text-left">Lead Time</th>
                            <th class="border border-slate-400 px-2 py-2 text-left">Unit</th>
                            <th class="border border-slate-400 px-2 py-2 text-right">Item Price</th>
                            <th class="border border-slate-400 px-2 py-2 text-center">Qty</th>
                            <th class="border border-slate-400 px-2 py-2 text-right">Total</th>
                            <th class="border border-slate-400 px-2 py-2 text-center"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @foreach ($items as $index => $row)
                            @php
                                $itemImageUrl = ! empty($row['item_image'] ?? null)
                                    ? \Illuminate\Support\Facades\Storage::disk('public')->url($row['item_image'])
                                    : null;
                                $itemName = $availableItems->firstWhere('id', (int) ($row['item_id'] ?? 0))?->item_name ?? 'Item';
                            @endphp
                            <tr wire:key="quotation-item-row-{{ $index }}">
                                <td class="border border-slate-300 p-2 align-top">
                                    <div class="flex items-center gap-2">
                                        @if ($itemImageUrl)
                                            <button type="button" class="size-10 shrink-0 overflow-hidden rounded-md border border-slate-200 bg-white" @click="imagePreviewUrl = @js($itemImageUrl); imagePreviewTitle = @js($itemName); imagePreviewOpen = true">
                                                <img src="{{ $itemImageUrl }}" alt="{{ $itemName }}" class="h-full w-full object-cover">
                                            </button>
                                        @else
                                            <span class="grid size-10 shrink-0 place-items-center rounded-md border border-slate-200 bg-slate-100 text-xs font-bold text-slate-500">{{ strtoupper(substr($itemName, 0, 1)) }}</span>
                                        @endif
                                        <div
                                            wire:key="item-select-{{ $index }}-{{ $row['item_id'] ?: 'none' }}-{{ $availableItems->count() }}"
                                            x-data="{
                                                open: false,
                                                query: '',
                                                selected: @entangle('items.'.$index.'.item_id').live,
                                                options: @js($availableItems->map(fn ($item) => [
                                                    'id' => (string) $item->id,
                                                    'label' => $item->item_name,
                                                ])->values()),
                                                filtered() {
                                                    const term = this.query.toLowerCase().trim();
                                                    return term === '' ? this.options : this.options.filter((option) => option.label.toLowerCase().includes(term));
                                                },
                                                selectedLabel() {
                                                    return this.options.find((option) => option.id === String(this.selected))?.label || 'Select item';
                                                },
                                                choose(id) {
                                                    this.selected = String(id);
                                                    this.open = false;
                                                    this.query = '';
                                                },
                                            }"
                                            class="relative min-w-0 flex-1 quotation-select2"
                                            @click.outside="open = false"
                                        >
                                            <button type="button" class="flex h-9 w-full items-center justify-between gap-3 border border-slate-400 bg-white px-2 text-left text-sm erp-focus-ring" @click="open = ! open">
                                                <span class="min-w-0 truncate" :class="selected ? 'text-slate-900' : 'text-slate-400'" x-text="selectedLabel()"></span>
                                                <svg class="size-4 shrink-0 text-slate-500 transition-transform" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                                </svg>
                                            </button>
                                            <div x-show="open" x-transition class="absolute z-40 mt-1 w-[24rem] overflow-hidden rounded-md border border-slate-200 bg-white shadow-lg">
                                                <div class="border-b border-slate-200 p-2">
                                                    <input type="search" x-model="query" class="block h-9 w-full rounded-md border-slate-300 px-3 text-sm shadow-sm erp-focus-ring" placeholder="Search item" @keydown.escape.stop="open = false">
                                                </div>
                                                <div class="max-h-60 overflow-y-auto py-1">
                                                    <template x-for="option in filtered()" :key="option.id">
                                                        <button type="button" class="flex w-full items-center justify-between gap-3 px-3 py-2 text-left text-sm hover:bg-slate-100" :class="String(selected) === option.id ? 'bg-cyan-50 text-cyan-800' : 'text-slate-700'" @click="choose(option.id)">
                                                            <span class="min-w-0 truncate" x-text="option.label"></span>
                                                            <svg x-show="String(selected) === option.id" class="size-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                                            </svg>
                                                        </button>
                                                    </template>
                                                    <div x-show="filtered().length === 0" class="px-3 py-4 text-center text-sm text-slate-500">No items found.</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @error('items.'.$index.'.item_id') <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
                                </td>
                                <td class="border border-slate-300 p-2 align-top">
                                    <input type="text" wire:model.blur="items.{{ $index }}.description" class="block h-9 w-full border border-slate-400 px-2 text-sm erp-focus-ring">
                                    @error('items.'.$index.'.description') <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
                                </td>
                                <td class="border border-slate-300 p-2 align-top">
                                    <input type="text" wire:model.blur="items.{{ $index }}.lead_time" class="block h-9 w-full border border-slate-400 px-2 text-sm erp-focus-ring" placeholder="e.g. 7 days">
                                    @error('items.'.$index.'.lead_time') <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
                                </td>
                                <td class="border border-slate-300 p-2 align-top">
                                    <select wire:model.live="items.{{ $index }}.unit_measure_id" class="block h-9 w-full border border-slate-400 text-sm erp-focus-ring quotation-select2">
                                        <option value="">Select unit</option>
                                        @foreach ($unitMeasures as $unit)
                                            <option value="{{ $unit->id }}">{{ str($unit->name)->headline() }}</option>
                                        @endforeach
                                    </select>
                                    @error('items.'.$index.'.unit_measure_id') <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
                                </td>
                                <td class="border border-slate-300 p-2 text-right align-top">
                                    <input type="number" wire:model.live.debounce.250ms="items.{{ $index }}.item_price" min="0" step="0.01" class="block h-9 w-full border border-slate-400 px-2 text-right text-sm font-semibold text-slate-950 erp-focus-ring">
                                    @error('items.'.$index.'.item_price') <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
                                </td>
                                <td class="border border-slate-300 p-2 align-top">
                                    <input type="number" wire:model.live.debounce.250ms="items.{{ $index }}.quantity" min="1" step="1" class="block h-9 w-full border border-slate-400 px-2 text-center text-sm erp-focus-ring">
                                    @error('items.'.$index.'.quantity') <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
                                </td>
                                <td class="border border-slate-300 p-2 text-right align-top">
                                    <input type="text" value="{{ number_format((float) $row['total'], 2) }}" readonly class="block h-9 w-full border border-slate-300 bg-slate-100 px-2 text-right text-sm font-semibold text-slate-950">
                                </td>
                                <td class="border border-slate-300 p-2 text-center align-top">
                                    <button type="button" wire:click="removeRow({{ $index }})" class="inline-flex size-9 items-center justify-center border border-red-700 bg-red-600 text-white hover:bg-red-700" aria-label="Remove row">
                                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @error('items') <span class="mt-2 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
        </div>
        <div x-show="imagePreviewOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 px-4">
            <div class="w-full max-w-2xl overflow-hidden rounded-lg bg-white shadow-2xl">
                <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3">
                    <h4 class="text-sm font-semibold text-slate-950" x-text="imagePreviewTitle || 'Item Image'"></h4>
                    <button type="button" @click="imagePreviewOpen = false" class="rounded-md px-2 py-1 text-sm font-semibold text-slate-500 hover:bg-slate-100">Close</button>
                </div>
                <div class="bg-slate-50 p-4">
                    <img :src="imagePreviewUrl" alt="Item preview" class="mx-auto max-h-[32rem] max-w-full rounded-md object-contain">
                </div>
            </div>
        </div>
    </section>

    <section class="erp-panel">
        <div class="erp-panel-header">
            <h3 class="text-sm font-semibold text-slate-950">Summary</h3>
        </div>
        <div class="erp-panel-body space-y-4">
            <div class="grid gap-3 lg:grid-cols-2">
                <x-admin.select-field label="Currency" name="currency" wire:model.live="currency" required>
                    <option value="php">PHP</option>
                    <option value="dollar">Dollar</option>
                </x-admin.select-field>

                <x-admin.select-field label="Tax Rate" name="tax_rate" wire:model.live="tax_rate" required>
                    <option value="0">0%</option>
                    <option value="12">12%</option>
                </x-admin.select-field>
            </div>

            <div class="overflow-hidden border border-slate-400 bg-white">
                <table class="w-full table-fixed border-collapse text-sm">
                    <thead class="bg-slate-200 text-xs font-bold uppercase text-slate-700">
                        <tr><th class="border border-slate-400 px-3 py-2 text-left">Summary</th><th class="border border-slate-400 px-3 py-2 text-right">Amount</th></tr>
                    </thead>
                    <tbody>
                        <tr><td class="border border-slate-300 px-3 py-2 font-semibold text-slate-700">Subtotal</td><td class="border border-slate-300 px-3 py-2 text-right font-semibold text-slate-950">{{ number_format($subtotal, 2) }}</td></tr>
                        <tr><td class="border border-slate-300 px-3 py-2 font-semibold text-slate-700">Tax Amount</td><td class="border border-slate-300 px-3 py-2 text-right font-semibold text-slate-950">{{ number_format($tax_amount, 2) }}</td></tr>
                    </tbody>
                    <tfoot>
                        <tr class="bg-slate-950 text-white"><td class="border border-slate-950 px-3 py-3 text-sm font-bold uppercase">Total Amount</td><td class="border border-slate-950 px-3 py-3 text-right text-base font-bold">{{ number_format($total_amount, 2) }}</td></tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </section>

    <div class="sticky bottom-0 flex items-center justify-end gap-2 border-t border-slate-200 bg-slate-100/95 py-4 backdrop-blur">
        <a href="{{ $cancelRoute }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancel</a>
        <button type="submit" class="rounded-md bg-slate-950 px-4 py-2 text-sm font-semibold text-white shadow-sm erp-focus-ring hover:bg-slate-800">
            {{ $submitLabel }}
        </button>
    </div>

    <div
        x-data="{
            open: @entangle('showQuickItemModal').live,
            previewUrl: '',
            setPreview(event) {
                if (this.previewUrl) URL.revokeObjectURL(this.previewUrl);
                const file = event.target.files && event.target.files[0] ? event.target.files[0] : null;
                this.previewUrl = file ? URL.createObjectURL(file) : '';
            },
            clearPreview() {
                if (this.previewUrl) URL.revokeObjectURL(this.previewUrl);
                this.previewUrl = '';
                this.previewOpen = false;
            },
            previewOpen: false,
        }"
        x-show="open"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center px-4"
    >
        <div class="absolute inset-0 bg-slate-950/60" @click="clearPreview(); $wire.closeQuickItemModal()"></div>
        <div class="relative w-full max-w-lg rounded-xl bg-white shadow-2xl">
            <div class="border-b border-slate-200 px-5 py-4"><h3 class="text-base font-semibold text-slate-950">Add New Item</h3></div>
            <div class="space-y-4 px-5 py-4">
                <x-admin.select-field label="Origin" name="quick_item_source" wire:model.live="quick_item_source"><option value="local">Local</option><option value="import">Imported</option></x-admin.select-field>
                <x-admin.form-field label="Item Name" name="quick_item_name" wire:model.blur="quick_item_name" />
                <div class="grid gap-3 sm:grid-cols-[minmax(0,1fr)_6rem] sm:items-end">
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Item Image</span>
                        <input type="file" wire:model="quick_item_image_upload" accept="image/*" class="mt-1 block w-full rounded-md border border-slate-300 text-sm text-slate-700 shadow-sm file:mr-3 file:border-0 file:bg-slate-950 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-slate-800 erp-focus-ring" @change="setPreview($event)">
                        @error('quick_item_image_upload') <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
                        <span wire:loading wire:target="quick_item_image_upload" class="mt-1 block text-xs font-semibold text-cyan-700">Uploading image...</span>
                    </label>
                    <button type="button" x-bind:disabled="! previewUrl" @click="previewOpen = true" class="inline-flex h-10 items-center justify-center rounded-md border border-cyan-200 bg-cyan-50 px-3 text-sm font-semibold text-cyan-800 shadow-sm hover:bg-cyan-100 disabled:cursor-not-allowed disabled:border-slate-200 disabled:bg-slate-100 disabled:text-slate-400">Preview</button>
                </div>
                <div class="grid gap-3 sm:grid-cols-3">
                    <x-admin.form-field label="Supplier Price" name="quick_supplier_price" type="number" step="0.01" wire:model.live="quick_supplier_price" />
                    <x-admin.form-field label="Markup %" name="quick_markup_percentage" type="number" step="0.01" wire:model.live="quick_markup_percentage" />
                    <label class="block"><span class="text-sm font-medium text-slate-700">Item Price</span><input type="text" value="{{ $quick_item_price }}" readonly class="mt-1 block h-10 w-full rounded-md border-slate-200 bg-slate-100 px-3 text-sm font-semibold text-slate-950 shadow-sm"></label>
                </div>
            </div>
            <div class="flex justify-end gap-2 border-t border-slate-200 px-5 py-4"><button type="button" @click="clearPreview(); $wire.closeQuickItemModal()" class="rounded-md border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700">Cancel</button><button type="button" wire:click="createQuickItem" class="rounded-md bg-cyan-600 px-3 py-2 text-sm font-semibold text-white hover:bg-cyan-700">Add Item</button></div>
        </div>
        <div x-show="previewOpen" x-cloak class="absolute inset-0 z-10 flex items-center justify-center rounded-xl bg-slate-950/40 px-4">
            <div class="w-full max-w-sm overflow-hidden rounded-lg bg-white shadow-2xl">
                <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3">
                    <h4 class="text-sm font-semibold text-slate-950">Item Image Preview</h4>
                    <button type="button" @click="previewOpen = false" class="rounded-md px-2 py-1 text-sm font-semibold text-slate-500 hover:bg-slate-100">Close</button>
                </div>
                <div class="bg-slate-50 p-4">
                    <img :src="previewUrl" alt="Item preview" class="mx-auto max-h-72 max-w-full rounded-md object-contain">
                </div>
            </div>
        </div>
    </div>
</form>
