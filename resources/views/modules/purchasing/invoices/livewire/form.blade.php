<div class="space-y-5">
    <section class="erp-panel">
        <div class="erp-panel-header"><h3 class="text-base font-semibold text-slate-950">Purchase Invoice Details</h3></div>
        <div class="erp-panel-body grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <label class="block">
                <span class="text-sm font-medium text-slate-700">Invoice No</span>
                <input type="text" wire:model="purchase_invoice_no" readonly class="mt-1 block h-10 w-full rounded-md border-slate-200 bg-slate-100 px-3 text-sm font-semibold text-slate-950 shadow-sm">
            </label>
            <label class="block">
                <span class="text-sm font-medium text-slate-700">Invoice Date</span>
                <input type="date" wire:model.live="invoice_date" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm erp-focus-ring">
                @error('invoice_date') <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
            </label>
            <label class="block">
                <span class="text-sm font-medium text-slate-700">Supplier Invoice No</span>
                <input type="text" wire:model.blur="supplier_invoice_no" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm erp-focus-ring">
                @error('supplier_invoice_no') <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
            </label>
            <label class="block">
                <span class="text-sm font-medium text-slate-700">Purchase Order</span>
                @if ($editing)
                    <input type="text" wire:model="purchase_order_no" readonly class="mt-1 block h-10 w-full rounded-md border-slate-200 bg-slate-100 px-3 text-sm font-semibold text-slate-950 shadow-sm">
                @else
                    <select wire:model.live="purchase_order_id" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm erp-focus-ring">
                        <option value="">Select purchase order</option>
                        @foreach ($purchaseOrders as $order)
                            <option value="{{ $order->id }}">{{ $order->purchase_order_no }} - {{ $order->supplier_name }}</option>
                        @endforeach
                    </select>
                @endif
                @error('purchase_order_id') <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
            </label>
            <label class="block">
                <span class="text-sm font-medium text-slate-700">Supplier</span>
                <input type="text" wire:model="supplier_name" readonly class="mt-1 block h-10 w-full rounded-md border-slate-200 bg-slate-100 px-3 text-sm font-semibold text-slate-950 shadow-sm">
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
                <span class="text-sm font-medium text-slate-700">Due Date</span>
                <input type="date" wire:model.live="due_date" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm erp-focus-ring">
                @error('due_date') <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
            </label>
            <label class="block md:col-span-2">
                <span class="text-sm font-medium text-slate-700">Supplier Address</span>
                <input type="text" wire:model="supplier_address" readonly class="mt-1 block h-10 w-full rounded-md border-slate-200 bg-slate-100 px-3 text-sm text-slate-700 shadow-sm">
            </label>
            <label class="block">
                <span class="text-sm font-medium text-slate-700">Status</span>
                <select wire:model.live="status" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm erp-focus-ring">
                    @foreach ($statuses as $option)
                        <option value="{{ $option }}">{{ str($option)->headline() }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block md:col-span-3">
                <span class="text-sm font-medium text-slate-700">Remarks</span>
                <textarea wire:model.blur="remarks" rows="3" class="mt-1 block w-full rounded-md border-slate-300 px-3 py-2 text-sm shadow-sm erp-focus-ring"></textarea>
            </label>
        </div>
    </section>

    <section class="erp-panel">
        <div class="erp-panel-header"><h3 class="text-base font-semibold text-slate-950">Invoice Items</h3></div>
        <div class="erp-panel-body">
            @error('items') <div class="mb-3 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">{{ $message }}</div> @enderror
            <div class="overflow-x-auto border border-slate-400 bg-white">
                <table class="min-w-[1120px] w-full table-fixed border-collapse text-sm">
                    <colgroup>
                        <col style="width:220px"><col style="width:280px"><col style="width:150px"><col style="width:120px"><col style="width:150px"><col style="width:150px"><col style="width:150px">
                    </colgroup>
                    <thead class="bg-slate-200 text-xs font-bold uppercase text-slate-700">
                        <tr>
                            <th class="border border-slate-400 px-3 py-2 text-left">Item</th>
                            <th class="border border-slate-400 px-3 py-2 text-left">Description</th>
                            <th class="border border-slate-400 px-3 py-2 text-left">Unit</th>
                            <th class="border border-slate-400 px-3 py-2 text-right">Qty</th>
                            <th class="border border-slate-400 px-3 py-2 text-right">Price</th>
                            <th class="border border-slate-400 px-3 py-2 text-right">Subtotal</th>
                            <th class="border border-slate-400 px-3 py-2 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($items as $index => $row)
                            <tr>
                                <td class="border border-slate-300 px-3 py-2 font-semibold text-slate-950">{{ $row['item_name'] ?? 'Item' }}</td>
                                <td class="border border-slate-300 px-2 py-2"><input type="text" wire:model.blur="items.{{ $index }}.description" class="block w-full rounded-md border-slate-300 text-sm shadow-sm erp-focus-ring"></td>
                                <td class="border border-slate-300 px-3 py-2 text-slate-700">{{ str($row['unit_measure_name'] ?? '')->headline() }}</td>
                                <td class="border border-slate-300 px-2 py-2"><input type="number" min="1" step="1" wire:model.live.debounce.250ms="items.{{ $index }}.quantity" class="block w-full rounded-md border-slate-300 text-right text-sm shadow-sm erp-focus-ring"></td>
                                <td class="border border-slate-300 px-2 py-2"><input type="number" min="0" step="0.01" wire:model.live.debounce.250ms="items.{{ $index }}.price" class="block w-full rounded-md border-slate-300 text-right text-sm shadow-sm erp-focus-ring"></td>
                                <td class="border border-slate-300 px-3 py-2 text-right">{{ number_format((float) ($row['subtotal'] ?? 0), 2) }}</td>
                                <td class="border border-slate-300 px-3 py-2 text-right font-semibold">{{ number_format((float) ($row['total'] ?? 0), 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="border border-slate-300 px-4 py-10 text-center text-sm text-slate-500">Select a purchase order to load items.</td></tr>
                        @endforelse
                    </tbody>
                </table>
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
        <a href="{{ route('purchasing.invoices.index') }}" class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancel</a>
        <button type="button" wire:click="save" class="rounded-md bg-slate-950 px-4 py-2 text-sm font-semibold text-white shadow-sm erp-focus-ring hover:bg-slate-800">{{ $editing ? 'Update Purchase Invoice' : 'Save Purchase Invoice' }}</button>
    </div>
</div>
