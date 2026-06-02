<div class="space-y-5">
    <section class="erp-panel">
        <div class="erp-panel-header">
            <h3 class="text-base font-semibold text-slate-950">Invoice Details</h3>
        </div>
        <div class="erp-panel-body grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <label class="block">
                <span class="text-sm font-medium text-slate-700">Sales Invoice No</span>
                <input type="text" wire:model="sales_invoice_no" readonly class="mt-1 block h-10 w-full rounded-md border-slate-200 bg-slate-100 px-3 text-sm font-semibold text-slate-950 shadow-sm">
            </label>
            <label class="block">
                <span class="text-sm font-medium text-slate-700">Invoice Date</span>
                <input type="date" wire:model.live="invoice_date" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm erp-focus-ring">
                @error('invoice_date') <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
            </label>
            <label class="block">
                <span class="text-sm font-medium text-slate-700">Due Date</span>
                <input type="date" wire:model.live="due_date" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm erp-focus-ring">
                @error('due_date') <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
            </label>
            <label class="block">
                <span class="text-sm font-medium text-slate-700">Delivery Receipt</span>
                @if ($editing)
                    <input type="text" value="{{ $delivery_receipt_no }}" readonly class="mt-1 block h-10 w-full rounded-md border-slate-200 bg-slate-100 px-3 text-sm font-semibold text-slate-950 shadow-sm">
                @else
                    <select wire:model.live="delivery_receipt_id" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm erp-focus-ring">
                        <option value="">Select delivery receipt</option>
                        @foreach ($deliveryReceipts as $receipt)
                            <option value="{{ $receipt->id }}">{{ $receipt->delivery_receipt_no }} - {{ $receipt->company_name }}</option>
                        @endforeach
                    </select>
                @endif
                @error('delivery_receipt_id') <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
            </label>
            <label class="block">
                <span class="text-sm font-medium text-slate-700">Sales Order</span>
                <input type="text" wire:model="sales_order_no" readonly class="mt-1 block h-10 w-full rounded-md border-slate-200 bg-slate-100 px-3 text-sm font-semibold text-slate-950 shadow-sm">
            </label>
            <label class="block">
                <span class="text-sm font-medium text-slate-700">Company</span>
                <input type="text" wire:model="company_name" readonly class="mt-1 block h-10 w-full rounded-md border-slate-200 bg-slate-100 px-3 text-sm font-semibold text-slate-950 shadow-sm">
            </label>
            <label class="block">
                <span class="text-sm font-medium text-slate-700">Customer PO</span>
                <input type="text" wire:model="customer_po" readonly class="mt-1 block h-10 w-full rounded-md border-slate-200 bg-slate-100 px-3 text-sm text-slate-700 shadow-sm">
            </label>
            <label class="block">
                <span class="text-sm font-medium text-slate-700">Terms</span>
                <input type="text" wire:model="terms" readonly class="mt-1 block h-10 w-full rounded-md border-slate-200 bg-slate-100 px-3 text-sm text-slate-700 shadow-sm">
            </label>
            <label class="block xl:col-span-2">
                <span class="text-sm font-medium text-slate-700">Company Address</span>
                <input type="text" wire:model="company_address" readonly class="mt-1 block h-10 w-full rounded-md border-slate-200 bg-slate-100 px-3 text-sm text-slate-700 shadow-sm">
            </label>
            <label class="block">
                <span class="text-sm font-medium text-slate-700">Contact Person</span>
                <input type="text" wire:model="contact_person" readonly class="mt-1 block h-10 w-full rounded-md border-slate-200 bg-slate-100 px-3 text-sm text-slate-700 shadow-sm">
            </label>
            <label class="block">
                <span class="text-sm font-medium text-slate-700">Contact No</span>
                <input type="text" wire:model="contact_no" readonly class="mt-1 block h-10 w-full rounded-md border-slate-200 bg-slate-100 px-3 text-sm text-slate-700 shadow-sm">
            </label>
            <label class="block md:col-span-2 xl:col-span-4">
                <span class="text-sm font-medium text-slate-700">Remarks</span>
                <textarea wire:model.blur="remarks" rows="3" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm erp-focus-ring"></textarea>
            </label>
        </div>
    </section>

    <section class="erp-panel">
        <div class="erp-panel-header">
            <h3 class="text-base font-semibold text-slate-950">Invoice Items</h3>
        </div>
        <div class="erp-panel-body">
            @error('items') <div class="mb-3 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">{{ $message }}</div> @enderror
            <div class="relative overflow-visible border border-slate-400 bg-white">
                <table class="min-w-[1250px] w-full table-fixed border-collapse text-sm">
                    <colgroup>
                        <col class="w-[16%]">
                        <col class="w-[17%]">
                        <col class="w-[9%]">
                        <col class="w-[10%]">
                        <col class="w-[10%]">
                        <col class="w-[8%]">
                        <col class="w-[14%]">
                        <col class="w-[8%]">
                        <col class="w-[8%]">
                    </colgroup>
                    <thead class="bg-slate-200 text-xs font-bold uppercase text-slate-700">
                        <tr>
                            <th class="border border-slate-400 px-3 py-2 text-left">Item Name</th>
                            <th class="border border-slate-400 px-3 py-2 text-left">Description</th>
                            <th class="border border-slate-400 px-3 py-2 text-left">Unit</th>
                            <th class="border border-slate-400 px-3 py-2 text-right">Delivered</th>
                            <th class="border border-slate-400 px-3 py-2 text-right">Invoiceable</th>
                            <th class="border border-slate-400 px-3 py-2 text-right">Qty</th>
                            <th class="border border-slate-400 px-3 py-2 text-right">Price</th>
                            <th class="border border-slate-400 px-3 py-2 text-right">Tax</th>
                            <th class="border border-slate-400 px-3 py-2 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @forelse ($items as $index => $row)
                            <tr>
                                <td class="border border-slate-300 px-3 py-2 align-middle font-semibold text-slate-950">{{ $row['item_name'] ?? 'N/A' }}</td>
                                <td class="border border-slate-300 px-3 py-2 align-middle">
                                    <input type="text" wire:model.blur="items.{{ $index }}.description" class="block w-full rounded-md border-slate-300 text-sm shadow-sm erp-focus-ring">
                                </td>
                                <td class="border border-slate-300 px-3 py-2 align-middle text-slate-700">{{ str($row['unit_measure_name'] ?? '')->headline() }}</td>
                                <td class="border border-slate-300 px-3 py-2 text-right align-middle text-slate-700">{{ number_format((float) ($row['delivered_quantity'] ?? 0), 0) }}</td>
                                <td class="border border-slate-300 px-3 py-2 text-right align-middle text-slate-700">{{ number_format((float) ($row['invoiceable_quantity'] ?? 0), 0) }}</td>
                                <td class="border border-slate-300 px-3 py-2 align-middle">
                                    <input type="number" min="0" step="1" max="{{ (float) ($row['invoiceable_quantity'] ?? 0) }}" wire:model.blur="items.{{ $index }}.quantity" class="block w-full rounded-md border-slate-300 text-right text-sm shadow-sm erp-focus-ring">
                                    @error("items.$index.quantity") <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
                                </td>
                                <td class="border border-slate-300 px-3 py-2 align-middle">
                                    <input type="number" min="0" step="0.01" wire:model.blur="items.{{ $index }}.price" class="block w-full rounded-md border-slate-300 text-right text-sm shadow-sm erp-focus-ring">
                                </td>
                                <td class="border border-slate-300 px-3 py-2 text-right align-middle text-slate-700">{{ number_format((float) ($row['tax_amount'] ?? 0), 2) }}</td>
                                <td class="border border-slate-300 px-3 py-2 text-right align-middle font-semibold text-slate-950">{{ number_format((float) ($row['total'] ?? 0), 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="border border-slate-300 px-4 py-10 text-center text-sm text-slate-500">Select a delivery receipt to load invoiceable items.</td></tr>
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
                        @foreach ($currencies as $option)
                            <option value="{{ $option }}">{{ strtoupper($option) }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="block">
                    <span class="text-sm font-medium text-slate-700">Tax Rate</span>
                    <select wire:model.live="tax_rate" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm erp-focus-ring">
                        @foreach ($taxRates as $rate)
                            <option value="{{ $rate }}">{{ $rate }}%</option>
                        @endforeach
                    </select>
                </label>
            </div>

            <div class="mt-4 overflow-hidden border border-slate-400 bg-white">
                <table class="w-full table-fixed border-collapse text-sm">
                    <thead class="bg-slate-200 text-xs font-bold uppercase text-slate-700">
                        <tr>
                            <th class="border border-slate-400 px-3 py-2 text-left">Summary</th>
                            <th class="border border-slate-400 px-3 py-2 text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="border border-slate-300 px-3 py-2 font-semibold text-slate-700">Subtotal</td>
                            <td class="border border-slate-300 px-3 py-2 text-right font-semibold text-slate-950">{{ number_format((float) ($totals['subtotal'] ?? 0), 2) }}</td>
                        </tr>
                        <tr>
                            <td class="border border-slate-300 px-3 py-2 font-semibold text-slate-700">Tax Amount</td>
                            <td class="border border-slate-300 px-3 py-2 text-right font-semibold text-slate-950">{{ number_format((float) ($totals['tax_amount'] ?? 0), 2) }}</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="bg-slate-950 text-white">
                            <td class="border border-slate-950 px-3 py-3 text-sm font-bold uppercase">Total Amount</td>
                            <td class="border border-slate-950 px-3 py-3 text-right text-base font-bold">{{ number_format((float) ($totals['total_amount'] ?? 0), 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </section>

    <div class="sticky bottom-0 flex items-center justify-end gap-2 border-t border-slate-200 bg-slate-100/95 py-4 backdrop-blur">
        <a href="{{ route('sales.invoices.index') }}" class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancel</a>
        <button type="button" wire:click="save" class="rounded-md bg-slate-950 px-4 py-2 text-sm font-semibold text-white shadow-sm erp-focus-ring hover:bg-slate-800">
            {{ $editing ? 'Update Invoice' : 'Save Invoice' }}
        </button>
    </div>
</div>
