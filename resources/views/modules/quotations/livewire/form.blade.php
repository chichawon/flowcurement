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

                    <x-admin.form-field label="Agent Name" name="agent_name" wire:model.blur="agent_name" required />
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
                    <input type="text" wire:model.blur="remarks" class="mt-1 block h-10 w-full rounded-md border-slate-300 px-3 text-sm shadow-sm erp-focus-ring">
                    @error('remarks') <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
                </label>
            </div>
        </section>
    </div>

    <section class="erp-panel">
        <div class="erp-panel-header flex items-center justify-between gap-3">
            <h3 class="text-sm font-semibold text-slate-950">Item Rows</h3>
            <button type="button" wire:click="addRow" class="inline-flex items-center gap-1.5 rounded-md bg-cyan-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-cyan-700 erp-focus-ring">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Add Row
            </button>
        </div>

        <div class="erp-panel-body">
            <div class="relative overflow-visible border border-slate-400 bg-white">
                <table class="w-full table-fixed border-collapse text-sm">
                    <colgroup>
                        <col class="w-[24%]">
                        <col class="w-[25%]">
                        <col class="w-[14%]">
                        <col class="w-[13%]">
                        <col class="w-[10%]">
                        <col class="w-[12%]">
                        <col class="w-14">
                    </colgroup>
                    <thead class="bg-slate-200 text-xs font-bold uppercase text-slate-700">
                        <tr>
                            <th class="border border-slate-400 px-2 py-2 text-left">Item</th>
                            <th class="border border-slate-400 px-2 py-2 text-left">Description</th>
                            <th class="border border-slate-400 px-2 py-2 text-left">Unit</th>
                            <th class="border border-slate-400 px-2 py-2 text-right">Item Price</th>
                            <th class="border border-slate-400 px-2 py-2 text-center">Qty</th>
                            <th class="border border-slate-400 px-2 py-2 text-right">Total</th>
                            <th class="border border-slate-400 px-2 py-2 text-center"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @foreach ($items as $index => $row)
                            <tr wire:key="quotation-item-row-{{ $index }}">
                                <td class="border border-slate-300 p-2 align-top">
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
                                        class="relative quotation-select2"
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
                                    @error('items.'.$index.'.item_id') <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
                                </td>
                                <td class="border border-slate-300 p-2 align-top">
                                    <input type="text" wire:model.blur="items.{{ $index }}.description" class="block h-9 w-full border border-slate-400 px-2 text-sm erp-focus-ring">
                                    @error('items.'.$index.'.description') <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
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

            @if ($quotationRecord)
                <div class="max-w-xs">
                    <x-admin.select-field label="Status" name="status" wire:model.live="status" required>
                        @foreach (\App\Modules\Quotations\Helpers\QuotationOptions::STATUSES as $option)
                            <option value="{{ $option }}">{{ str($option)->headline() }}</option>
                        @endforeach
                    </x-admin.select-field>
                </div>
            @endif

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
</form>
