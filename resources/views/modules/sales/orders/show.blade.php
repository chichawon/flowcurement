<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-cyan-700">Sales Orders</p>
            <h2 class="text-2xl font-semibold text-slate-950">{{ $salesOrder->sales_order_no }}</h2>
        </div>
    </x-slot>

    <div class="space-y-5">
        <div class="grid gap-5 xl:grid-cols-[minmax(0,1fr)_22rem]">
            <section class="erp-panel">
                <div class="erp-panel-header flex items-center justify-between gap-3">
                    <h3 class="text-sm font-semibold text-slate-950">Customer Information</h3>
                    <x-sales.status-badge :status="$salesOrder->status" />
                </div>
                <dl class="erp-panel-body grid gap-4 sm:grid-cols-2">
                    <div><dt class="text-xs font-semibold uppercase text-slate-500">Company</dt><dd class="mt-1 text-sm font-semibold text-slate-950">{{ $salesOrder->businessPartner?->company_name }}</dd></div>
                    <div><dt class="text-xs font-semibold uppercase text-slate-500">Agent</dt><dd class="mt-1 text-sm text-slate-700">{{ $salesOrder->agent_name }}</dd></div>
                    <div><dt class="text-xs font-semibold uppercase text-slate-500">Customer PO</dt><dd class="mt-1 text-sm text-slate-700">{{ $salesOrder->customer_po ?: 'None' }}</dd></div>
                    <div><dt class="text-xs font-semibold uppercase text-slate-500">Terms</dt><dd class="mt-1 text-sm text-slate-700">{{ $salesOrder->terms }} days</dd></div>
                    <div><dt class="text-xs font-semibold uppercase text-slate-500">Contact Person</dt><dd class="mt-1 text-sm text-slate-700">{{ $salesOrder->contact_person }}</dd></div>
                    <div><dt class="text-xs font-semibold uppercase text-slate-500">Contact No.</dt><dd class="mt-1 text-sm text-slate-700">{{ $salesOrder->contact_no }}</dd></div>
                    <div class="sm:col-span-2"><dt class="text-xs font-semibold uppercase text-slate-500">Address</dt><dd class="mt-1 text-sm text-slate-700">{{ $salesOrder->company_address ?: 'No address provided' }}</dd></div>
                    <div class="sm:col-span-2"><dt class="text-xs font-semibold uppercase text-slate-500">Remarks</dt><dd class="mt-1 text-sm text-slate-700">{{ $salesOrder->remarks ?: 'No remarks' }}</dd></div>
                    @if ($salesOrder->po_attachment)
                        <div class="sm:col-span-2"><dt class="text-xs font-semibold uppercase text-slate-500">P.O. Attachment</dt><dd class="mt-1"><a href="{{ Storage::disk('public')->url($salesOrder->po_attachment) }}" target="_blank" class="text-sm font-semibold text-cyan-700 hover:text-cyan-800">View / Download attachment</a></dd></div>
                    @endif
                </dl>
            </section>

            <section class="erp-panel">
                <div class="erp-panel-header"><h3 class="text-sm font-semibold text-slate-950">Order Summary</h3></div>
                <div class="erp-panel-body space-y-4">
                    <div class="space-y-3">
                        <div class="flex justify-between gap-3 text-sm"><span class="text-slate-500">Order Date</span><span class="font-semibold text-slate-950">{{ $salesOrder->order_date?->format('M d, Y') }}</span></div>
                        <div class="flex justify-between gap-3 text-sm"><span class="text-slate-500">Delivery Date</span><span class="font-semibold text-slate-950">{{ $salesOrder->delivery_date?->format('M d, Y') }}</span></div>
                        <div class="flex justify-between gap-3 text-sm"><span class="text-slate-500">Created By</span><span class="truncate font-semibold text-slate-950">{{ $salesOrder->creator?->name ?? 'System' }}</span></div>
                        <div class="flex justify-between gap-3 text-sm"><span class="text-slate-500">Quotation</span><span class="font-semibold text-slate-950">{{ $salesOrder->quotation?->quotation_no ?? 'Manual' }}</span></div>
                    </div>
                    <div class="overflow-hidden border border-slate-400 bg-white">
                        <table class="w-full border-collapse text-sm">
                            <tbody>
                                <tr><td class="border border-slate-300 px-3 py-2 font-semibold text-slate-700">Subtotal</td><td class="border border-slate-300 px-3 py-2 text-right font-semibold text-slate-950">{{ number_format((float) $salesOrder->subtotal, 2) }}</td></tr>
                                <tr><td class="border border-slate-300 px-3 py-2 font-semibold text-slate-700">Tax {{ number_format((float) $salesOrder->tax_rate, 0) }}%</td><td class="border border-slate-300 px-3 py-2 text-right font-semibold text-slate-950">{{ number_format((float) $salesOrder->tax_amount, 2) }}</td></tr>
                            </tbody>
                            <tfoot><tr class="bg-slate-950 text-white"><td class="border border-slate-950 px-3 py-3 font-bold uppercase">Total</td><td class="border border-slate-950 px-3 py-3 text-right font-bold">{{ number_format((float) $salesOrder->total_amount, 2) }}</td></tr></tfoot>
                        </table>
                    </div>
                </div>
            </section>
        </div>

        <section class="erp-panel">
            <div class="erp-panel-header"><h3 class="text-sm font-semibold text-slate-950">Items</h3></div>
            <div class="erp-panel-body">
                <div class="overflow-hidden border border-slate-400 bg-white">
                    <table class="w-full table-fixed border-collapse text-sm">
                        <colgroup><col class="w-[20%]"><col class="w-[24%]"><col class="w-[9%]"><col class="w-[10%]"><col class="w-[11%]"><col class="w-[10%]"><col class="w-[8%]"><col class="w-[8%]"></colgroup>
                        <thead class="bg-slate-200 text-xs font-bold uppercase text-slate-700">
                            <tr><th class="border border-slate-400 px-2 py-2 text-left">Item</th><th class="border border-slate-400 px-2 py-2 text-left">Description</th><th class="border border-slate-400 px-2 py-2 text-center">Qty</th><th class="border border-slate-400 px-2 py-2 text-left">Unit</th><th class="border border-slate-400 px-2 py-2 text-right">Price</th><th class="border border-slate-400 px-2 py-2 text-right">Stock</th><th class="border border-slate-400 px-2 py-2 text-right">Balance</th><th class="border border-slate-400 px-2 py-2 text-right">Total</th></tr>
                        </thead>
                        <tbody>
                            @foreach ($salesOrder->items as $row)
                                <tr>
                                    <td class="border border-slate-300 px-2 py-3"><p class="truncate font-semibold text-slate-950">{{ $row->item?->item_name }}</p></td>
                                    <td class="border border-slate-300 px-2 py-3 text-slate-700">{{ $row->description }}</td>
                                    <td class="border border-slate-300 px-2 py-3 text-center">{{ number_format((float) $row->order_quantity, 0) }}</td>
                                    <td class="border border-slate-300 px-2 py-3">{{ str($row->unitMeasure?->name)->headline() }}</td>
                                    <td class="border border-slate-300 px-2 py-3 text-right font-semibold">{{ number_format((float) $row->price, 2) }}</td>
                                    <td class="border border-slate-300 px-2 py-3 text-right">{{ number_format((float) $row->available_stock, 2) }}</td>
                                    <td class="border border-slate-300 px-2 py-3 text-right font-semibold">{{ number_format((float) ($row->balance_quantity ?? $row->order_quantity), 2) }}</td>
                                    <td class="border border-slate-300 px-2 py-3 text-right font-semibold">{{ number_format((float) $row->total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>

    <div class="sticky bottom-0 mt-5 flex items-center justify-end gap-2 border-t border-slate-200 bg-slate-100/95 py-4 backdrop-blur">
        <a href="{{ route('sales.orders.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Back</a>
        @can('update', $salesOrder)
            <a href="{{ route('sales.orders.edit', $salesOrder) }}" class="rounded-md bg-slate-950 px-4 py-2 text-sm font-semibold text-white shadow-sm erp-focus-ring hover:bg-slate-800">Edit</a>
        @endcan
    </div>
</x-app-layout>
