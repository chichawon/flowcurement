<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-cyan-700">Purchase Orders</p>
            <h2 class="text-2xl font-semibold text-slate-950">{{ $purchaseOrder->purchase_order_no }}</h2>
        </div>
    </x-slot>

    <div class="space-y-5">
        <section class="erp-panel">
            <div class="erp-panel-header"><h3 class="text-base font-semibold text-slate-950">Purchase Order Details</h3></div>
            <div class="erp-panel-body grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <div><p class="text-xs font-semibold uppercase text-slate-500">P.O Date</p><p class="mt-1 font-semibold text-slate-950">{{ $purchaseOrder->purchase_order_date?->format('M d, Y') }}</p></div>
                <div><p class="text-xs font-semibold uppercase text-slate-500">Expected Delivery</p><p class="mt-1 font-semibold text-slate-950">{{ $purchaseOrder->expected_delivery_date?->format('M d, Y') ?: 'Open' }}</p></div>
                <div><p class="text-xs font-semibold uppercase text-slate-500">Supplier</p><p class="mt-1 font-semibold text-slate-950">{{ $purchaseOrder->supplier_name }}</p></div>
                <div><p class="text-xs font-semibold uppercase text-slate-500">Status</p><p class="mt-1"><x-sales.status-badge :status="$purchaseOrder->status" /></p></div>
                <div><p class="text-xs font-semibold uppercase text-slate-500">Contact Person</p><p class="mt-1 text-slate-900">{{ $purchaseOrder->contact_person }}</p></div>
                <div><p class="text-xs font-semibold uppercase text-slate-500">Contact No</p><p class="mt-1 text-slate-900">{{ $purchaseOrder->contact_no }}</p></div>
                <div><p class="text-xs font-semibold uppercase text-slate-500">Terms</p><p class="mt-1 text-slate-900">{{ $purchaseOrder->terms }}</p></div>
                <div class="md:col-span-2"><p class="text-xs font-semibold uppercase text-slate-500">Address</p><p class="mt-1 text-slate-900">{{ $purchaseOrder->supplier_address }}</p></div>
            </div>
        </section>

        <section class="erp-panel">
            <div class="erp-panel-header"><h3 class="text-base font-semibold text-slate-950">Items</h3></div>
            <div class="erp-panel-body overflow-x-auto">
                <table class="min-w-[900px] w-full table-fixed border-collapse text-sm">
                    <thead class="bg-slate-200 text-xs font-bold uppercase text-slate-700">
                        <tr><th class="border border-slate-400 px-3 py-2 text-left">Item</th><th class="border border-slate-400 px-3 py-2 text-left">Description</th><th class="border border-slate-400 px-3 py-2 text-left">Unit</th><th class="border border-slate-400 px-3 py-2 text-right">Qty</th><th class="border border-slate-400 px-3 py-2 text-right">Price</th><th class="border border-slate-400 px-3 py-2 text-right">Total</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($purchaseOrder->items as $item)
                            <tr><td class="border border-slate-300 px-3 py-2 font-semibold">{{ $item->item?->item_name }}</td><td class="border border-slate-300 px-3 py-2">{{ $item->description }}</td><td class="border border-slate-300 px-3 py-2">{{ str($item->unitMeasure?->name)->headline() }}</td><td class="border border-slate-300 px-3 py-2 text-right">{{ number_format((float) $item->quantity, 0) }}</td><td class="border border-slate-300 px-3 py-2 text-right">{{ number_format((float) $item->price, 2) }}</td><td class="border border-slate-300 px-3 py-2 text-right font-semibold">{{ number_format((float) $item->total, 2) }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <section class="erp-panel"><div class="erp-panel-body"><x-purchasing.summary-table :totals="$purchaseOrder->only(['subtotal', 'tax_amount', 'total_amount'])" /></div></section>

        <div class="flex justify-end gap-2">
            <a href="{{ route('purchasing.orders.index') }}" class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Back</a>
            @can('update', $purchaseOrder)<a href="{{ route('purchasing.orders.edit', $purchaseOrder) }}" class="rounded-md bg-slate-950 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">Edit</a>@endcan
        </div>
    </div>
</x-app-layout>
