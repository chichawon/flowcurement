<div class="space-y-5" x-data="{ imagePreviewOpen: false, imagePreviewUrl: '', imagePreviewTitle: '' }">
    <section class="erp-panel">
        <div class="erp-panel-header"><h3 class="text-base font-semibold text-slate-950">Purchase Order Details</h3></div>
        <div class="erp-panel-body grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <label class="block">
                <span class="text-sm font-medium text-slate-700">P.O No</span>
                <input type="text" wire:model.blur="purchase_order_no" class="mt-1 block h-10 w-full rounded-md border-slate-300 px-3 text-sm font-semibold uppercase text-slate-950 shadow-sm erp-focus-ring">
                @error('purchase_order_no') <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
            </label>
            <label class="block">
                <span class="text-sm font-medium text-slate-700">P.O Date</span>
                <input type="date" wire:model.live="purchase_order_date" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm erp-focus-ring">
                @error('purchase_order_date') <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
            </label>
            <label class="block">
                <span class="text-sm font-medium text-slate-700">Expected Delivery</span>
                <input type="date" wire:model.live="expected_delivery_date" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm erp-focus-ring">
                @error('expected_delivery_date') <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
            </label>
            <label class="block">
                <span class="text-sm font-medium text-slate-700">Supplier</span>
                <select wire:model.live="supplier_id" @disabled($editing) class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm erp-focus-ring disabled:bg-slate-100">
                    <option value="">Select supplier</option>
                    @foreach ($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->company_name }}</option>
                    @endforeach
                </select>
                @error('supplier_id') <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
            </label>
            <label class="block">
                <span class="text-sm font-medium text-slate-700">Contact Person</span>
                <input type="text" wire:model="contact_person" readonly class="mt-1 block h-10 w-full rounded-md border-slate-200 bg-slate-100 px-3 text-sm text-slate-700 shadow-sm">
            </label>
            <label class="block">
                <span class="text-sm font-medium text-slate-700">Contact No</span>
                <input type="text" wire:model="contact_no" readonly class="mt-1 block h-10 w-full rounded-md border-slate-200 bg-slate-100 px-3 text-sm text-slate-700 shadow-sm">
            </label>
            <label class="block">
                <span class="text-sm font-medium text-slate-700">Terms</span>
                <input type="text" wire:model="terms" readonly class="mt-1 block h-10 w-full rounded-md border-slate-200 bg-slate-100 px-3 text-sm text-slate-700 shadow-sm">
            </label>
            <label class="block xl:col-span-1">
                <span class="text-sm font-medium text-slate-700">Status</span>
                <select wire:model.live="status" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm erp-focus-ring">
                    @foreach ($statuses as $option)
                        <option value="{{ $option }}">{{ str($option)->headline() }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block md:col-span-2">
                <span class="text-sm font-medium text-slate-700">Supplier Address</span>
                <input type="text" wire:model="supplier_address" readonly class="mt-1 block h-10 w-full rounded-md border-slate-200 bg-slate-100 px-3 text-sm text-slate-700 shadow-sm">
            </label>
            <label class="block md:col-span-2">
                <span class="text-sm font-medium text-slate-700">Remarks</span>
                <textarea wire:model.blur="remarks" rows="3" class="mt-1 block w-full rounded-md border-slate-300 px-3 py-2 text-sm shadow-sm erp-focus-ring"></textarea>
            </label>
        </div>
    </section>

    <section class="erp-panel">
        <div class="erp-panel-header flex items-center justify-between">
            <h3 class="text-base font-semibold text-slate-950">Item Rows</h3>
            <button type="button" wire:click="addRow" class="rounded-md bg-cyan-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-cyan-700">Add Row</button>
        </div>
        <div class="erp-panel-body">
            @error('items') <div class="mb-3 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">{{ $message }}</div> @enderror
            <div class="overflow-x-auto border border-slate-400 bg-white">
                <table class="min-w-[1280px] w-full table-fixed border-collapse text-sm">
                    <colgroup>
                        <col style="width:220px"><col style="width:260px"><col style="width:140px"><col style="width:150px"><col style="width:120px"><col style="width:140px"><col style="width:140px"><col style="width:140px"><col style="width:240px"><col style="width:70px">
                    </colgroup>
                    <thead class="bg-slate-200 text-xs font-bold uppercase text-slate-700">
                        <tr>
                            <th class="border border-slate-400 px-3 py-2 text-left">Item</th>
                            <th class="border border-slate-400 px-3 py-2 text-left">Description</th>
                            <th class="border border-slate-400 px-3 py-2 text-left">Lead Time</th>
                            <th class="border border-slate-400 px-3 py-2 text-left">Unit</th>
                            <th class="border border-slate-400 px-3 py-2 text-right">Qty</th>
                            <th class="border border-slate-400 px-3 py-2 text-right">Price</th>
                            <th class="border border-slate-400 px-3 py-2 text-right">Subtotal</th>
                            <th class="border border-slate-400 px-3 py-2 text-right">Total</th>
                            <th class="border border-slate-400 px-3 py-2 text-left">Remarks</th>
                            <th class="border border-slate-400 px-3 py-2 text-center"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $index => $row)
                            @php
                                $itemImageUrl = ! empty($row['item_image'] ?? null)
                                    ? \Illuminate\Support\Facades\Storage::disk('public')->url($row['item_image'])
                                    : null;
                                $itemName = $row['item_name'] ?? $itemOptions->firstWhere('id', (int) ($row['item_id'] ?? 0))?->item_name ?? 'Item';
                            @endphp
                            <tr>
                                <td class="border border-slate-300 px-2 py-2">
                                    <div class="flex items-center gap-2">
                                        @if ($itemImageUrl)
                                            <button type="button" class="size-10 shrink-0 overflow-hidden rounded-md border border-slate-200 bg-white" x-on:click="imagePreviewUrl = @js($itemImageUrl); imagePreviewTitle = @js($itemName); imagePreviewOpen = true">
                                                <img src="{{ $itemImageUrl }}" alt="{{ $itemName }}" class="h-full w-full object-cover">
                                            </button>
                                        @else
                                            <span class="grid size-10 shrink-0 place-items-center rounded-md border border-slate-200 bg-slate-100 text-xs font-bold text-slate-500">{{ strtoupper(substr($itemName, 0, 1)) }}</span>
                                        @endif
                                        <select wire:model.live="items.{{ $index }}.item_id" class="block min-w-0 flex-1 rounded-md border-slate-300 text-sm shadow-sm erp-focus-ring">
                                            <option value="">Select item</option>
                                            @foreach ($itemOptions as $item)
                                                <option value="{{ $item->id }}">{{ $item->item_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error("items.$index.item_id") <span class="mt-1 block text-xs text-red-600">{{ $message }}</span> @enderror
                                </td>
                                <td class="border border-slate-300 px-2 py-2"><input type="text" wire:model.blur="items.{{ $index }}.description" class="block w-full rounded-md border-slate-300 text-sm shadow-sm erp-focus-ring"></td>
                                <td class="border border-slate-300 px-2 py-2"><input type="text" wire:model.blur="items.{{ $index }}.lead_time" class="block w-full rounded-md border-slate-300 text-sm shadow-sm erp-focus-ring"></td>
                                <td class="border border-slate-300 px-2 py-2">
                                    <select wire:model.live="items.{{ $index }}.unit_measure_id" class="block w-full rounded-md border-slate-300 text-sm shadow-sm erp-focus-ring">
                                        <option value="">Select unit</option>
                                        @foreach ($unitMeasures as $unit)
                                            <option value="{{ $unit->id }}">{{ str($unit->name)->headline() }}</option>
                                        @endforeach
                                    </select>
                                    @error("items.$index.unit_measure_id") <span class="mt-1 block text-xs text-red-600">{{ $message }}</span> @enderror
                                </td>
                                <td class="border border-slate-300 px-2 py-2"><input type="number" min="1" step="1" wire:model.live.debounce.250ms="items.{{ $index }}.quantity" class="block w-full rounded-md border-slate-300 text-right text-sm shadow-sm erp-focus-ring"></td>
                                <td class="border border-slate-300 px-2 py-2"><input type="number" min="0" step="0.01" wire:model.live.debounce.250ms="items.{{ $index }}.price" class="block w-full rounded-md border-slate-300 text-right text-sm shadow-sm erp-focus-ring"></td>
                                <td class="border border-slate-300 px-3 py-2 text-right">{{ number_format((float) ($row['subtotal'] ?? 0), 2) }}</td>
                                <td class="border border-slate-300 px-3 py-2 text-right font-semibold">{{ number_format((float) ($row['total'] ?? 0), 2) }}</td>
                                <td class="border border-slate-300 px-2 py-2"><input type="text" wire:model.blur="items.{{ $index }}.remarks" class="block w-full rounded-md border-slate-300 text-sm shadow-sm erp-focus-ring"></td>
                                <td class="border border-slate-300 px-2 py-2 text-center"><button type="button" wire:click="removeRow({{ $index }})" class="size-9 rounded-md bg-red-600 text-lg font-bold text-white hover:bg-red-700">&times;</button></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div x-show="imagePreviewOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 px-4">
            <div class="w-full max-w-2xl overflow-hidden rounded-lg bg-white shadow-2xl">
                <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3">
                    <h4 class="text-sm font-semibold text-slate-950" x-text="imagePreviewTitle || 'Item Image'"></h4>
                    <button type="button" x-on:click="imagePreviewOpen = false" class="rounded-md px-2 py-1 text-sm font-semibold text-slate-500 hover:bg-slate-100">Close</button>
                </div>
                <div class="bg-slate-50 p-4">
                    <img :src="imagePreviewUrl" alt="Item preview" class="mx-auto max-h-[32rem] max-w-full rounded-md object-contain">
                </div>
            </div>
        </div>
    </section>

    <section class="erp-panel">
        <div class="erp-panel-header"><h3 class="text-base font-semibold text-slate-950">Summary</h3></div>
        <div class="erp-panel-body">
            <div class="grid gap-3 lg:grid-cols-2">
                <label class="block">
                    <span class="text-sm font-medium text-slate-700">Currency</span>
                    <select wire:model.live="currency" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm erp-focus-ring">
                        @foreach ($currencies as $option)<option value="{{ $option }}">{{ strtoupper($option) }}</option>@endforeach
                    </select>
                </label>
                <label class="block">
                    <span class="text-sm font-medium text-slate-700">Tax Rate</span>
                    <select wire:model.live="tax_rate" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm erp-focus-ring">
                        @foreach ($taxRates as $option)<option value="{{ $option }}">{{ $option }}%</option>@endforeach
                    </select>
                </label>
            </div>
            <x-purchasing.summary-table :totals="$totals" />
        </div>
    </section>

    <div class="sticky bottom-0 flex items-center justify-end gap-2 border-t border-slate-200 bg-slate-100/95 py-4 backdrop-blur">
        <a href="{{ route('purchasing.orders.index') }}" class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancel</a>
        <button type="button" wire:click="save" class="rounded-md bg-slate-950 px-4 py-2 text-sm font-semibold text-white shadow-sm erp-focus-ring hover:bg-slate-800">{{ $editing ? 'Update Purchase Order' : 'Save Purchase Order' }}</button>
    </div>
</div>
