<form wire:submit.prevent="save" class="space-y-5">
    <section class="erp-panel">
        <div class="erp-panel-header">
            <h3 class="text-sm font-semibold text-slate-950">{{ $title }}</h3>
        </div>
        <div class="erp-panel-body space-y-4">
            <div class="grid gap-3 lg:grid-cols-4">
                <label class="block">
                    <span class="text-sm font-medium text-slate-700">Delivery Receipt No.</span>
                    <input type="text" wire:model.blur="delivery_receipt_no" class="mt-1 block h-10 w-full rounded-md border-slate-300 px-3 text-sm font-semibold text-slate-950 shadow-sm erp-focus-ring" @disabled($deliveryReceiptRecord !== null)>
                    @error('delivery_receipt_no') <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
                </label>
                <label class="block">
                    <span class="text-sm font-medium text-slate-700">DR Date</span>
                    <input type="date" wire:model.live="dr_date" class="mt-1 block h-10 w-full rounded-md border-slate-300 px-3 text-sm shadow-sm erp-focus-ring">
                    @error('dr_date') <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
                </label>
                @php
                    $salesOrderOptions = $salesOrders->map(fn ($so): array => [
                        'id' => (string) $so->id,
                        'sales_order_no' => (string) $so->sales_order_no,
                        'company' => (string) ($so->businessPartner?->company_name ?? 'No company'),
                        'customer_po' => (string) ($so->customer_po ?: 'No P.O.'),
                        'label' => trim(($so->customer_po ?: 'No P.O.').' - '.($so->businessPartner?->company_name ?? 'No company')),
                        'search' => strtolower(trim($so->sales_order_no.' '.($so->businessPartner?->company_name ?? '').' '.($so->customer_po ?? ''))),
                    ])->values();
                    $selectedSalesOrderLabel = $sales_order_no
                        ? trim(($customer_po ?: 'No P.O.').' - '.($company_name ?: 'No company'))
                        : 'Select sales order';
                @endphp
                <div
                    class="block lg:col-span-2"
                    wire:ignore.self
                    x-data="{
                        open: false,
                        search: '',
                        selected: @js((string) $sales_order_id),
                        disabled: @js($deliveryReceiptRecord !== null),
                        fallbackLabel: @js($selectedSalesOrderLabel),
                        options: @js($salesOrderOptions),
                        get selectedOption() {
                            return this.options.find((option) => String(option.id) === String(this.selected));
                        },
                        get selectedLabel() {
                            return this.selectedOption ? this.selectedOption.label : this.fallbackLabel;
                        },
                        get filteredOptions() {
                            const term = this.search.trim().toLowerCase();
                            if (! term) {
                                return this.options;
                            }

                            return this.options.filter((option) => option.search.includes(term));
                        },
                        choose(option) {
                            if (this.disabled) {
                                return;
                            }

                            this.selected = String(option.id);
                            this.open = false;
                            this.search = '';
                        },
                    }"
                    @click.away="open = false"
                >
                    <span class="text-sm font-medium text-slate-700">Sales Order</span>
                    <div class="relative mt-1">
                        <button
                            type="button"
                            class="flex h-10 w-full items-center justify-between gap-3 rounded-md border border-slate-300 bg-white px-3 text-left text-sm shadow-sm erp-focus-ring disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-500"
                            @click="if (! disabled) { open = ! open; $nextTick(() => $refs.salesOrderSearch?.focus()); }"
                            :disabled="disabled"
                        >
                            <span class="min-w-0" :class="selected ? 'text-slate-950' : 'text-slate-500'">
                                <span class="block truncate" x-text="selectedLabel"></span>
                                <span x-show="selectedOption" class="block truncate text-xs font-medium text-slate-500" x-text="selectedOption?.sales_order_no"></span>
                            </span>
                            <svg class="size-4 shrink-0 text-slate-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.22 7.22a.75.75 0 0 1 1.06 0L10 10.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 8.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                            </svg>
                        </button>

                        <div x-show="open" x-cloak class="absolute z-40 mt-1 w-full overflow-hidden rounded-md border border-slate-300 bg-white shadow-lg">
                            <div class="border-b border-slate-200 p-2">
                                <input
                                    x-ref="salesOrderSearch"
                                    type="search"
                                    x-model="search"
                                    class="block h-9 w-full rounded-md border-slate-300 px-3 text-sm shadow-sm erp-focus-ring"
                                    placeholder="Search sales order, company, or P.O."
                                >
                            </div>
                            <div class="max-h-64 overflow-y-auto py-1">
                                <template x-for="option in filteredOptions" :key="option.id">
                                    <button
                                        type="button"
                                        class="flex w-full flex-col gap-0.5 px-3 py-2 text-left text-sm hover:bg-cyan-50"
                                        :class="String(option.id) === String(selected) ? 'bg-cyan-50 text-cyan-900' : 'text-slate-800'"
                                        @click="choose(option); $wire.set('sales_order_id', String(option.id))"
                                    >
                                        <span class="font-semibold" x-text="`${option.customer_po} - ${option.company}`"></span>
                                        <span class="text-xs text-slate-500" x-text="option.sales_order_no"></span>
                                    </button>
                                </template>

                                <div x-show="filteredOptions.length === 0" class="px-3 py-4 text-center text-sm text-slate-500">
                                    No sales orders found.
                                </div>
                            </div>
                        </div>
                    </div>
                    @error('sales_order_id') <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid gap-3 lg:grid-cols-4">
                <label class="block"><span class="text-sm font-medium text-slate-700">Sales Order No.</span><input type="text" value="{{ $sales_order_no }}" readonly class="mt-1 block h-10 w-full rounded-md border-slate-200 bg-slate-100 px-3 text-sm text-slate-700 shadow-sm"></label>
                <label class="block"><span class="text-sm font-medium text-slate-700">Customer PO</span><input type="text" value="{{ $customer_po }}" readonly class="mt-1 block h-10 w-full rounded-md border-slate-200 bg-slate-100 px-3 text-sm text-slate-700 shadow-sm"></label>
                <label class="block"><span class="text-sm font-medium text-slate-700">Agent Name</span><input type="text" value="{{ $agent_name }}" readonly class="mt-1 block h-10 w-full rounded-md border-slate-200 bg-slate-100 px-3 text-sm text-slate-700 shadow-sm"></label>
                <label class="block"><span class="text-sm font-medium text-slate-700">Company</span><input type="text" value="{{ $company_name }}" readonly class="mt-1 block h-10 w-full rounded-md border-slate-200 bg-slate-100 px-3 text-sm text-slate-700 shadow-sm"></label>
            </div>
            <div class="grid gap-3 lg:grid-cols-4">
                <label class="block"><span class="text-sm font-medium text-slate-700">Terms</span><input type="text" value="{{ $terms }} days" readonly class="mt-1 block h-10 w-full rounded-md border-slate-200 bg-slate-100 px-3 text-sm text-slate-700 shadow-sm"></label>
                <label class="block lg:col-span-2"><span class="text-sm font-medium text-slate-700">Company Address</span><input type="text" value="{{ $company_address }}" readonly class="mt-1 block h-10 w-full rounded-md border-slate-200 bg-slate-100 px-3 text-sm text-slate-700 shadow-sm"></label>
                <label class="block"><span class="text-sm font-medium text-slate-700">Contact</span><input type="text" value="{{ $contact_person }}{{ $contact_no ? ' | '.$contact_no : '' }}" readonly class="mt-1 block h-10 w-full rounded-md border-slate-200 bg-slate-100 px-3 text-sm text-slate-700 shadow-sm"></label>
            </div>
        </div>
    </section>

    <section class="erp-panel">
        <div class="erp-panel-header">
            <h3 class="text-sm font-semibold text-slate-950">Items</h3>
        </div>
        <div class="erp-panel-body space-y-3">
            @if ($notice)
                <div class="rounded-md border border-red-300 bg-red-50 px-3 py-2 text-sm text-red-800">
                    <span class="font-extrabold text-red-700">*Notice:</span>
                    <span class="font-semibold">{{ $notice }}</span>
                </div>
            @endif
            <div class="overflow-x-auto border border-slate-400 bg-white">
                <table class="min-w-[760px] w-full table-fixed border-collapse text-xs">
                    <thead class="bg-slate-200 uppercase text-slate-700">
                        <tr>
                            <th class="border border-slate-400 px-2 py-2 text-right">Quantity</th>
                            <th class="border border-slate-400 px-2 py-2 text-left">Unit</th>
                            <th class="border border-slate-400 px-2 py-2 text-left">Item Name</th>
                            <th class="border border-slate-400 px-2 py-2 text-left">Stock Availability</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @forelse ($items as $index => $row)
                            <tr>
                                <td class="border border-slate-300 px-2 py-2 text-right align-top">
                                    <input type="number" min="0" step="1" wire:model.live.debounce.250ms="items.{{ $index }}.delivered_quantity" class="h-8 w-24 border border-slate-400 bg-white px-2 text-right text-xs shadow-sm erp-focus-ring" @disabled($deliveryReceiptRecord !== null)>
                                </td>
                                <td class="border border-slate-300 px-2 py-2 align-top">{{ str($row['unit_measure_name'])->headline() }}</td>
                                <td class="border border-slate-300 px-2 py-2 align-top font-medium text-slate-900">{{ $row['item_name'] }}</td>
                                <td class="border border-slate-300 px-2 py-2 align-top">
                                    @if ($row['stock_status'] === 'no_stock')
                                        <span class="rounded-full bg-red-600 px-2 py-0.5 text-[11px] font-semibold text-white">No stock available</span>
                                    @elseif ($row['stock_status'] === 'partial_stock')
                                        <span class="rounded-full bg-amber-500 px-2 py-0.5 text-[11px] font-semibold text-white">Partial stock available</span>
                                    @else
                                        <span class="rounded-full bg-emerald-600 px-2 py-0.5 text-[11px] font-semibold text-white">Available</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="border border-slate-300 px-3 py-5 text-center text-sm text-slate-500">Select a sales order to load items.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @error('items') <span class="block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
        </div>
    </section>

    <div class="sticky bottom-0 flex items-center justify-end gap-2 border-t border-slate-200 bg-slate-100/95 py-4 backdrop-blur">
        <a href="{{ $cancelRoute }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancel</a>
        <button type="submit" class="rounded-md bg-slate-950 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-slate-800">{{ $submitLabel }}</button>
    </div>

    <div
        x-data="{ open: @entangle('showDuplicateDeliveryReceiptNoModal').live }"
        x-show="open"
        x-transition.opacity
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center px-4 py-6 sm:px-0"
        role="dialog"
        aria-modal="true"
    >
        <div class="absolute inset-0 bg-slate-950/60" @click="$wire.closeDuplicateDeliveryReceiptNoModal()"></div>

        <div class="relative w-full max-w-sm rounded-xl bg-white shadow-2xl">
            <div class="border-b border-slate-200 px-5 py-4">
                <h3 class="text-base font-semibold text-slate-950">Existing D.R number</h3>
                <p class="mt-1 text-sm text-slate-500">Please use another Delivery Receipt number.</p>
            </div>
            <div class="px-5 py-4">
                <p class="text-sm text-slate-600">This D.R number already exists:</p>
                <p class="mt-1 text-sm font-semibold text-red-700">{{ $duplicateDeliveryReceiptNo }}</p>
            </div>
            <div class="flex items-center justify-end border-t border-slate-200 px-5 py-4">
                <button type="button" wire:click="closeDuplicateDeliveryReceiptNoModal" class="rounded-md bg-slate-950 px-3 py-2 text-sm font-semibold text-white hover:bg-slate-800">Okay</button>
            </div>
        </div>
    </div>
</form>
