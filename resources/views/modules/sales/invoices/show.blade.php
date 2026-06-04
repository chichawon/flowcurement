<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-cyan-700">Sales Invoice</p>
            <h2 class="text-2xl font-semibold text-slate-950">{{ $salesInvoice->sales_invoice_no }}</h2>
        </div>
    </x-slot>

    <div class="space-y-5" x-data="{ imagePreviewOpen: false, imagePreviewUrl: '', imagePreviewTitle: '' }">
        <section class="erp-panel">
            <div class="erp-panel-header flex items-center justify-between">
                <h3 class="text-base font-semibold text-slate-950">Invoice Details</h3>
                <x-sales.status-badge :status="$salesInvoice->status" />
            </div>
            <div class="erp-panel-body grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <div><p class="text-xs font-semibold uppercase text-slate-500">Invoice Date</p><p class="mt-1 text-sm font-semibold text-slate-950">{{ $salesInvoice->invoice_date?->format('M d, Y') }}</p></div>
                <div><p class="text-xs font-semibold uppercase text-slate-500">Due Date</p><p class="mt-1 text-sm font-semibold text-slate-950">{{ $salesInvoice->due_date?->format('M d, Y') ?? 'Not set' }}</p></div>
                <div><p class="text-xs font-semibold uppercase text-slate-500">Sales Order</p><p class="mt-1 text-sm font-semibold text-slate-950">{{ $salesInvoice->sales_order_no }}</p></div>
                <div><p class="text-xs font-semibold uppercase text-slate-500">Delivery Receipt</p><p class="mt-1 text-sm font-semibold text-slate-950">{{ $salesInvoice->delivery_receipt_no }}</p></div>
                <div><p class="text-xs font-semibold uppercase text-slate-500">Company</p><p class="mt-1 text-sm font-semibold text-slate-950">{{ $salesInvoice->company_name }}</p></div>
                <div><p class="text-xs font-semibold uppercase text-slate-500">Customer PO</p><p class="mt-1 text-sm font-semibold text-slate-950">{{ $salesInvoice->customer_po ?: 'None' }}</p></div>
                <div><p class="text-xs font-semibold uppercase text-slate-500">Contact Person</p><p class="mt-1 text-sm font-semibold text-slate-950">{{ $salesInvoice->contact_person ?: 'None' }}</p></div>
                <div><p class="text-xs font-semibold uppercase text-slate-500">Contact No</p><p class="mt-1 text-sm font-semibold text-slate-950">{{ $salesInvoice->contact_no ?: 'None' }}</p></div>
                <div><p class="text-xs font-semibold uppercase text-slate-500">Tax Rate</p><p class="mt-1 text-sm font-semibold text-slate-950">{{ number_format((float) $salesInvoice->tax_rate, 0) }}%</p></div>
            </div>
        </section>

        <section class="erp-panel">
            <div class="erp-panel-header">
                <h3 class="text-base font-semibold text-slate-950">Invoice Items</h3>
            </div>
            <div class="erp-panel-body">
                <div class="relative overflow-visible border border-slate-400 bg-white">
                    <table class="min-w-[1220px] w-full table-fixed border-collapse text-sm">
                        <colgroup>
                            <col class="w-[16%]">
                            <col class="w-[18%]">
                            <col class="w-[9%]">
                            <col class="w-[7%]">
                            <col class="w-[13%]">
                            <col class="w-[10%]">
                            <col class="w-[8%]">
                            <col class="w-[8%]">
                            <col class="w-[11%]">
                        </colgroup>
                        <thead class="bg-slate-200 text-xs font-bold uppercase text-slate-700">
                            <tr>
                                <th class="border border-slate-400 px-3 py-2 text-left">Item Name</th>
                                <th class="border border-slate-400 px-3 py-2 text-left">Description</th>
                                <th class="border border-slate-400 px-3 py-2 text-right">WHT</th>
                                <th class="border border-slate-400 px-3 py-2 text-left">Unit</th>
                                <th class="border border-slate-400 px-3 py-2 text-right">Qty</th>
                                <th class="border border-slate-400 px-3 py-2 text-right">Price</th>
                                <th class="border border-slate-400 px-3 py-2 text-right">Subtotal</th>
                                <th class="border border-slate-400 px-3 py-2 text-right">Tax</th>
                                <th class="border border-slate-400 px-3 py-2 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            @foreach ($salesInvoice->items as $row)
                                @php
                                    $itemImageUrl = $row->item?->item_image ? \Illuminate\Support\Facades\Storage::disk('public')->url($row->item->item_image) : null;
                                    $itemName = $row->item_name ?? 'Item';
                                @endphp
                                <tr>
                                    <td class="border border-slate-300 px-3 py-2 font-semibold text-slate-950">
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
                                    <td class="border border-slate-300 px-3 py-2 text-slate-700">{{ $row->description ?: '-' }}</td>
                                    <td class="border border-slate-300 px-3 py-2 text-right text-slate-700">{{ number_format((float) $row->withholding_tax_rate, 0) }}% / {{ number_format((float) $row->withholding_tax_amount, 2) }}</td>
                                    <td class="border border-slate-300 px-3 py-2 text-slate-700">{{ str($row->unitMeasure?->name)->headline() }}</td>
                                    <td class="border border-slate-300 px-3 py-2 text-right text-slate-700">{{ number_format((float) $row->quantity, 0) }}</td>
                                    <td class="border border-slate-300 px-3 py-2 text-right text-slate-700">{{ number_format((float) $row->price, 2) }}</td>
                                    <td class="border border-slate-300 px-3 py-2 text-right text-slate-700">{{ number_format((float) $row->subtotal, 2) }}</td>
                                    <td class="border border-slate-300 px-3 py-2 text-right text-slate-700">{{ number_format((float) $row->tax_amount, 2) }}</td>
                                    <td class="border border-slate-300 px-3 py-2 text-right font-semibold text-slate-950">{{ number_format((float) $row->total, 2) }}</td>
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
                @php
                    $summaryTotalInvoiceAmount = round((float) $salesInvoice->subtotal + (float) $salesInvoice->tax_amount, 2);
                    $summaryBalanceAmount = round($summaryTotalInvoiceAmount - (float) $salesInvoice->withholding_tax_amount, 2);
                @endphp
                <div class="overflow-hidden border border-slate-400 bg-white">
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
                                <td class="border border-slate-300 px-3 py-2 text-right font-semibold text-slate-950">{{ number_format((float) $salesInvoice->subtotal, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="border border-slate-300 px-3 py-2 font-semibold text-slate-700">Tax Amount <span class="font-normal text-slate-500">({{ number_format((float) $salesInvoice->tax_rate, 0) }}%)</span></td>
                                <td class="border border-slate-300 px-3 py-2 text-right font-semibold text-slate-950">{{ number_format((float) $salesInvoice->tax_amount, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="border border-slate-300 px-3 py-2 font-semibold text-slate-700">Total Invoice Amount</td>
                                <td class="border border-slate-300 px-3 py-2 text-right font-semibold text-slate-950">{{ number_format($summaryTotalInvoiceAmount, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="border border-slate-300 px-3 py-2 font-semibold text-slate-700">Withholding Tax</td>
                                <td class="border border-slate-300 px-3 py-2 text-right font-semibold text-slate-950">{{ number_format((float) $salesInvoice->withholding_tax_amount, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="border border-slate-300 px-3 py-2 font-semibold text-slate-700">Balance</td>
                                <td class="border border-slate-300 px-3 py-2 text-right font-semibold text-slate-950">{{ number_format($summaryBalanceAmount, 2) }}</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="bg-slate-950 text-white">
                                <td class="border border-slate-950 px-3 py-3 text-sm font-bold uppercase">Total Amount</td>
                                <td class="border border-slate-950 px-3 py-3 text-right text-base font-bold">{{ number_format($summaryBalanceAmount, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @if ($salesInvoice->remarks)
                    <div class="mt-4 whitespace-pre-line rounded-md border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700">{{ $salesInvoice->remarks }}</div>
                @endif
            </div>
        </section>

        <div class="flex items-center justify-end gap-2">
            <a href="{{ route('sales.invoices.index') }}" class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Back</a>
            @can('print', $salesInvoice)
                <a href="{{ route('sales.invoices.print', $salesInvoice) }}" target="_blank" class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Print</a>
            @endcan
            @can('update', $salesInvoice)
                <a href="{{ route('sales.invoices.edit', $salesInvoice) }}" class="rounded-md bg-slate-950 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">Edit</a>
            @endcan
        </div>
    </div>
</x-app-layout>
