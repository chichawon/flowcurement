<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-cyan-700">Delivery Receipts</p>
            <h2 class="text-2xl font-semibold text-slate-950">{{ $deliveryReceipt->delivery_receipt_no }}</h2>
        </div>
    </x-slot>

    <div class="space-y-5">
        <section class="erp-panel">
            <div class="erp-panel-header flex items-center justify-between">
                <h3 class="text-sm font-semibold text-slate-950">Details</h3>
                <x-sales.status-badge :status="$deliveryReceipt->status" />
            </div>
            <dl class="erp-panel-body grid gap-4 sm:grid-cols-2">
                <div><dt class="text-xs font-semibold uppercase text-slate-500">DR Date</dt><dd class="mt-1 text-sm text-slate-800">{{ $deliveryReceipt->dr_date?->format('M d, Y') }}</dd></div>
                <div><dt class="text-xs font-semibold uppercase text-slate-500">Received Date</dt><dd class="mt-1 text-sm text-slate-800">{{ $deliveryReceipt->received_date?->format('M d, Y') ?? 'Not uploaded' }}</dd></div>
                <div><dt class="text-xs font-semibold uppercase text-slate-500">Sales Order</dt><dd class="mt-1 text-sm text-slate-800">{{ $deliveryReceipt->sales_order_no }}</dd></div>
                <div><dt class="text-xs font-semibold uppercase text-slate-500">Company</dt><dd class="mt-1 text-sm text-slate-800">{{ $deliveryReceipt->company_name }}</dd></div>
                <div><dt class="text-xs font-semibold uppercase text-slate-500">Contact</dt><dd class="mt-1 text-sm text-slate-800">{{ $deliveryReceipt->contact_person }}{{ $deliveryReceipt->contact_no ? ' | '.$deliveryReceipt->contact_no : '' }}</dd></div>
                <div><dt class="text-xs font-semibold uppercase text-slate-500">Remarks</dt><dd class="mt-1"><x-sales.status-badge :status="$deliveryReceipt->remarks ?? 'on_hold'" /></dd></div>
                <div><dt class="text-xs font-semibold uppercase text-slate-500">Received By</dt><dd class="mt-1 text-sm text-slate-800">{{ $deliveryReceipt->received_by ?: 'Not uploaded' }}</dd></div>
                <div><dt class="text-xs font-semibold uppercase text-slate-500">Delivered By</dt><dd class="mt-1 text-sm text-slate-800">{{ $deliveryReceipt->delivered_by ?: 'Not uploaded' }}</dd></div>
            </dl>
        </section>

        <section class="erp-panel">
            <div class="erp-panel-header"><h3 class="text-sm font-semibold text-slate-950">Invoice References</h3></div>
            <div class="erp-panel-body">
                <div class="overflow-x-auto rounded-md border border-slate-200">
                    <table class="w-full table-fixed divide-y divide-slate-200 text-sm">
                        <colgroup>
                            <col class="w-[24%]">
                            <col class="w-[18%]">
                            <col class="w-[18%]">
                            <col class="w-[18%]">
                            <col class="w-[22%]">
                        </colgroup>
                        <thead class="bg-slate-50 uppercase text-slate-500">
                            <tr>
                                <th class="px-3 py-2 text-left">Invoice No</th>
                                <th class="px-3 py-2 text-center">Invoice Date</th>
                                <th class="px-3 py-2 text-right">Total Amount</th>
                                <th class="px-3 py-2 text-right">Balance</th>
                                <th class="px-3 py-2 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @forelse ($deliveryReceipt->salesInvoices as $invoice)
                                <tr>
                                    <td class="px-3 py-2 font-semibold text-slate-900">
                                        @if (auth()->user()?->can('sales-invoices.view'))
                                            <a href="{{ route('sales.invoices.show', $invoice) }}" class="text-cyan-700 hover:text-cyan-800 hover:underline">{{ $invoice->sales_invoice_no }}</a>
                                        @else
                                            {{ $invoice->sales_invoice_no }}
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 text-center text-slate-700">{{ $invoice->invoice_date?->format('M d, Y') }}</td>
                                    <td class="px-3 py-2 text-right font-semibold text-slate-900">{{ number_format((float) $invoice->total_amount, 2) }}</td>
                                    <td class="px-3 py-2 text-right text-slate-700">{{ number_format((float) $invoice->balance_amount, 2) }}</td>
                                    <td class="px-3 py-2 text-center"><x-sales.status-badge :status="$invoice->status" /></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-sm text-slate-500">No invoice has been created from this delivery receipt yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <section class="erp-panel">
            <div class="erp-panel-header"><h3 class="text-sm font-semibold text-slate-950">Delivered Items</h3></div>
            <div class="erp-panel-body">
                <div class="overflow-x-auto rounded-md border border-slate-200">
                    <table class="w-full table-fixed divide-y divide-slate-200 text-sm">
                        <colgroup>
                            <col class="w-[34%]">
                            <col class="w-[16%]">
                            <col class="w-[20%]">
                            <col class="w-[30%]">
                        </colgroup>
                        <thead class="bg-slate-50 uppercase text-slate-500">
                            <tr>
                                <th class="px-3 py-2 text-left">Item Name</th>
                                <th class="px-3 py-2 text-right">Delivered Qty</th>
                                <th class="px-3 py-2 text-left">Unit</th>
                                <th class="px-3 py-2 text-left">Description</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @foreach ($deliveryReceipt->items as $row)
                                <tr>
                                    <td class="px-3 py-2 font-medium text-slate-900">{{ $row->item_name }}</td>
                                    <td class="px-3 py-2 text-right">{{ number_format((float) $row->delivered_quantity, 0) }}</td>
                                    <td class="px-3 py-2">{{ str($row->unitMeasure?->name)->headline() }}</td>
                                    <td class="px-3 py-2 text-slate-700">{{ $row->salesOrderItem?->description ?: '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        @if ($deliveryReceipt->attachments->isNotEmpty())
            <section class="erp-panel">
                <div class="erp-panel-header"><h3 class="text-sm font-semibold text-slate-950">Attachments</h3></div>
                <div class="erp-panel-body grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($deliveryReceipt->attachments as $attachment)
                        <a href="{{ Storage::disk('public')->url($attachment->file_path) }}" target="_blank" class="rounded-md border border-slate-200 bg-slate-50 p-3 hover:border-cyan-300 hover:bg-cyan-50/40">
                            @if (in_array(strtolower(pathinfo($attachment->file_name, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp'], true))
                                <img src="{{ Storage::disk('public')->url($attachment->file_path) }}" alt="{{ $attachment->file_name }}" class="h-28 w-full rounded border border-slate-200 bg-white object-contain">
                            @else
                                <div class="grid h-28 place-items-center rounded border border-slate-200 bg-white text-sm font-semibold text-red-600">PDF</div>
                            @endif
                            <p class="mt-2 truncate text-sm font-semibold text-slate-900">{{ $attachment->file_name }}</p>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    </div>

    <div class="sticky bottom-0 mt-5 flex items-center justify-end gap-2 border-t border-slate-200 bg-slate-100/95 py-4 backdrop-blur">
        <a href="{{ route('sales.delivery-receipts.index') }}" class="rounded-md bg-slate-950 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">Back</a>
        @can('print', $deliveryReceipt)
            <a href="{{ route('sales.delivery-receipts.print', $deliveryReceipt) }}" target="_blank" class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Print</a>
        @endcan
        @can('update', $deliveryReceipt)
            <a href="{{ route('sales.delivery-receipts.edit', $deliveryReceipt) }}" class="rounded-md bg-slate-950 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800" hidden>Edit</a>
        @endcan
    </div>
</x-app-layout>
