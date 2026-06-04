<div class="space-y-5" x-data="{ imagePreviewOpen: false, imagePreviewUrl: '', imagePreviewTitle: '' }">
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
            <label class="block w-full" style="grid-column: 1 / -1;">
                <span class="text-sm font-medium text-slate-700">Remarks</span>
                <textarea wire:model.blur="remarks" rows="4" class="mt-1 block h-24 w-full rounded-md border-slate-300 px-3 py-2 text-sm leading-6 shadow-sm erp-focus-ring"></textarea>
            </label>
        </div>
    </section>

    <section class="erp-panel">
        <div class="erp-panel-header">
            <h3 class="text-base font-semibold text-slate-950">Invoice Items</h3>
        </div>
        <div class="erp-panel-body">
            @error('items') <div class="mb-3 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">{{ $message }}</div> @enderror
            <div class="relative w-full max-w-full overflow-x-auto overflow-y-visible border border-slate-400 bg-white pb-2">
                <table class="table-fixed border-collapse text-sm" style="min-width: 1540px; width: 1540px;">
                    <colgroup>
                        <col style="width: 170px;">
                        <col style="width: 210px;">
                        <col style="width: 115px;">
                        <col style="width: 110px;">
                        <col style="width: 125px;">
                        <col style="width: 125px;">
                        <col style="width: 115px;">
                        <col style="width: 145px;">
                        <col style="width: 120px;">
                        <col style="width: 120px;">
                        <col style="width: 120px;">
                        <col style="width: 165px;">
                    </colgroup>
                    <thead class="bg-slate-200 text-xs font-bold uppercase text-slate-700">
                        <tr>
                            <th class="border border-slate-400 px-3 py-2 text-left">Item Name</th>
                            <th class="border border-slate-400 px-3 py-2 text-left">Description</th>
                            <th class="border border-slate-400 px-3 py-2 text-right">WHT</th>
                            <th class="border border-slate-400 px-3 py-2 text-left">Unit</th>
                            <th class="border border-slate-400 px-3 py-2 text-right">Delivered</th>
                            <th class="border border-slate-400 px-3 py-2 text-right">Invoiceable</th>
                            <th class="border border-slate-400 px-3 py-2 text-right">Qty</th>
                            <th class="border border-slate-400 px-3 py-2 text-right">Price</th>
                            <th class="border border-slate-400 px-3 py-2 text-right">Subtotal</th>
                            <th class="border border-slate-400 px-3 py-2 text-right">Tax</th>
                            <th class="border border-slate-400 px-3 py-2 text-right">WHT Amt</th>
                            <th class="border border-slate-400 px-3 py-2 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @forelse ($items as $index => $row)
                            @php
                                $itemImageUrl = ! empty($row['item_image'] ?? null)
                                    ? \Illuminate\Support\Facades\Storage::disk('public')->url($row['item_image'])
                                    : null;
                                $itemName = $row['item_name'] ?? 'Item';
                            @endphp
                            <tr>
                                <td class="border border-slate-300 px-3 py-2 align-middle font-semibold text-slate-950">
                                    <div class="flex items-center gap-2">
                                        @if ($itemImageUrl)
                                            <button type="button" class="size-10 shrink-0 overflow-hidden rounded-md border border-slate-200 bg-white" @click="imagePreviewUrl = @js($itemImageUrl); imagePreviewTitle = @js($itemName); imagePreviewOpen = true">
                                                <img src="{{ $itemImageUrl }}" alt="{{ $itemName }}" class="h-full w-full object-cover">
                                            </button>
                                        @else
                                            <span class="grid size-10 shrink-0 place-items-center rounded-md border border-slate-200 bg-slate-100 text-xs font-bold text-slate-500">{{ strtoupper(substr($itemName, 0, 1)) }}</span>
                                        @endif
                                        <span class="min-w-0 truncate">{{ $itemName }}</span>
                                    </div>
                                </td>
                                <td class="border border-slate-300 px-3 py-2 align-middle">
                                    <input type="text" wire:model.blur="items.{{ $index }}.description" class="block w-full rounded-md border-slate-300 text-sm shadow-sm erp-focus-ring">
                                </td>
                                <td class="border border-slate-300 px-3 py-2 align-middle">
                                    <select wire:model.live="items.{{ $index }}.withholding_tax_rate" class="block w-full rounded-md border-slate-300 text-right text-sm shadow-sm erp-focus-ring">
                                        @foreach ($withholdingTaxRates as $option)
                                            <option value="{{ $option }}">{{ $option }}%</option>
                                        @endforeach
                                    </select>
                                    @error("items.$index.withholding_tax_rate") <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
                                </td>
                                <td class="border border-slate-300 px-3 py-2 align-middle text-slate-700">{{ str($row['unit_measure_name'] ?? '')->headline() }}</td>
                                <td class="border border-slate-300 px-3 py-2 text-right align-middle text-slate-700">{{ number_format((float) ($row['delivered_quantity'] ?? 0), 0) }}</td>
                                <td class="border border-slate-300 px-3 py-2 text-right align-middle text-slate-700">{{ number_format((float) ($row['invoiceable_quantity'] ?? 0), 0) }}</td>
                                <td class="border border-slate-300 px-3 py-2 align-middle">
                                    <input type="number" min="0" step="1" max="{{ (float) ($row['invoiceable_quantity'] ?? 0) }}" wire:model.live.debounce.250ms="items.{{ $index }}.quantity" class="block w-full rounded-md border-slate-300 text-right text-sm shadow-sm erp-focus-ring">
                                    @error("items.$index.quantity") <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
                                </td>
                                <td class="border border-slate-300 px-3 py-2 text-right align-middle font-medium text-slate-700">{{ number_format((float) ($row['price'] ?? 0), 2) }}</td>
                                <td class="border border-slate-300 px-3 py-2 text-right align-middle text-slate-700">{{ number_format((float) ($row['subtotal'] ?? 0), 2) }}</td>
                                <td class="border border-slate-300 px-3 py-2 text-right align-middle text-slate-700">{{ number_format((float) ($row['tax_amount'] ?? 0), 2) }}</td>
                                <td class="border border-slate-300 px-3 py-2 text-right align-middle text-slate-700">{{ number_format((float) ($row['withholding_tax_amount'] ?? 0), 2) }}</td>
                                <td class="border border-slate-300 px-3 py-2 text-right align-middle font-semibold text-slate-950">{{ number_format((float) ($row['total'] ?? 0), 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="12" class="border border-slate-300 px-4 py-10 text-center text-sm text-slate-500">Select a delivery receipt to load invoiceable items.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
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
                    <input type="text" value="{{ (int) $tax_rate }}%" readonly class="mt-1 block h-10 w-full rounded-md border-slate-200 bg-slate-100 px-3 text-sm font-semibold text-slate-950 shadow-sm">
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
                            <td class="border border-slate-300 px-3 py-2 font-semibold text-slate-700">Subtotal <span class="font-normal text-slate-500">(Total amount no tax)</span></td>
                            <td class="border border-slate-300 px-3 py-2 text-right font-semibold text-slate-950">{{ number_format((float) ($totals['subtotal'] ?? 0), 2) }}</td>
                        </tr>
                        <tr>
                            <td class="border border-slate-300 px-3 py-2 font-semibold text-slate-700">Tax Amount <span class="font-normal text-slate-500">({{ (int) $tax_rate }}%)</span></td>
                            <td class="border border-slate-300 px-3 py-2 text-right font-semibold text-slate-950">{{ number_format((float) ($totals['tax_amount'] ?? 0), 2) }}</td>
                        </tr>
                        <tr>
                            <td class="border border-slate-300 px-3 py-2 font-semibold text-slate-700">Total Invoice Amount</td>
                            <td class="border border-slate-300 px-3 py-2 text-right font-semibold text-slate-950">{{ number_format((float) ($totals['total_amount'] ?? 0), 2) }}</td>
                        </tr>
                        <tr>
                            <td class="border border-slate-300 px-3 py-2 font-semibold text-slate-700">Withholding Tax</td>
                            <td class="border border-slate-300 px-3 py-2 text-right font-semibold text-slate-950">{{ number_format((float) ($totals['withholding_tax_amount'] ?? 0), 2) }}</td>
                        </tr>
                        <tr>
                            <td class="border border-slate-300 px-3 py-2 font-semibold text-slate-700">Balance</td>
                            <td class="border border-slate-300 px-3 py-2 text-right font-semibold text-slate-950">{{ number_format((float) ($totals['balance_amount'] ?? 0), 2) }}</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="bg-slate-950 text-white">
                            <td class="border border-slate-950 px-3 py-3 text-sm font-bold uppercase">Total Amount</td>
                            <td class="border border-slate-950 px-3 py-3 text-right text-base font-bold">{{ number_format((float) ($totals['balance_amount'] ?? 0), 2) }}</td>
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
