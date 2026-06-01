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
                <div><dt class="text-xs font-semibold uppercase text-slate-500">Sales Order</dt><dd class="mt-1 text-sm text-slate-800">{{ $deliveryReceipt->sales_order_no }}</dd></div>
                <div><dt class="text-xs font-semibold uppercase text-slate-500">Company</dt><dd class="mt-1 text-sm text-slate-800">{{ $deliveryReceipt->company_name }}</dd></div>
                <div><dt class="text-xs font-semibold uppercase text-slate-500">Contact</dt><dd class="mt-1 text-sm text-slate-800">{{ $deliveryReceipt->contact_person }}{{ $deliveryReceipt->contact_no ? ' | '.$deliveryReceipt->contact_no : '' }}</dd></div>
            </dl>
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
    </div>

    <div class="sticky bottom-0 mt-5 flex items-center justify-end gap-2 border-t border-slate-200 bg-slate-100/95 py-4 backdrop-blur">
        <a href="{{ route('sales.delivery-receipts.index') }}" class="rounded-md bg-slate-950 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">Back</a>
        @can('update', $deliveryReceipt)
            <a href="{{ route('sales.delivery-receipts.edit', $deliveryReceipt) }}" class="rounded-md bg-slate-950 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800" hidden>Edit</a>
        @endcan
    </div>
</x-app-layout>
