<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-cyan-700">Purchase Invoices</p>
            <h2 class="text-2xl font-semibold text-slate-950">{{ $purchaseInvoice->purchase_invoice_no }}</h2>
        </div>
    </x-slot>

    <div class="space-y-5">
        <section class="erp-panel">
            <div class="erp-panel-header"><h3 class="text-base font-semibold text-slate-950">Purchase Invoice Details</h3></div>
            <div class="erp-panel-body grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <div><p class="text-xs font-semibold uppercase text-slate-500">Invoice Date</p><p class="mt-1 font-semibold text-slate-950">{{ $purchaseInvoice->invoice_date?->format('M d, Y') }}</p></div>
                <div><p class="text-xs font-semibold uppercase text-slate-500">Supplier Invoice</p><p class="mt-1 font-semibold text-slate-950">{{ $purchaseInvoice->supplier_invoice_no }}</p></div>
                <div><p class="text-xs font-semibold uppercase text-slate-500">P.O No</p><p class="mt-1 font-semibold text-slate-950">{{ $purchaseInvoice->purchase_order_no ?: 'Direct' }}</p></div>
                <div><p class="text-xs font-semibold uppercase text-slate-500">Status</p><p class="mt-1"><x-sales.status-badge :status="$purchaseInvoice->status" /></p></div>
                <div><p class="text-xs font-semibold uppercase text-slate-500">Supplier</p><p class="mt-1 text-slate-900">{{ $purchaseInvoice->supplier_name }}</p></div>
                <div><p class="text-xs font-semibold uppercase text-slate-500">Contact Person</p><p class="mt-1 text-slate-900">{{ $purchaseInvoice->contact_person }}</p></div>
                <div><p class="text-xs font-semibold uppercase text-slate-500">Contact No</p><p class="mt-1 text-slate-900">{{ $purchaseInvoice->contact_no }}</p></div>
                <div><p class="text-xs font-semibold uppercase text-slate-500">Due Date</p><p class="mt-1 text-slate-900">{{ $purchaseInvoice->due_date?->format('M d, Y') ?: 'Open' }}</p></div>
            </div>
        </section>
        <section class="erp-panel">
            <div class="erp-panel-header"><h3 class="text-base font-semibold text-slate-950">Items</h3></div>
            <div class="erp-panel-body overflow-x-auto">
                <table class="min-w-[900px] w-full table-fixed border-collapse text-sm">
                    <thead class="bg-slate-200 text-xs font-bold uppercase text-slate-700"><tr><th class="border border-slate-400 px-3 py-2 text-left">Item</th><th class="border border-slate-400 px-3 py-2 text-left">Description</th><th class="border border-slate-400 px-3 py-2 text-left">Unit</th><th class="border border-slate-400 px-3 py-2 text-right">Qty</th><th class="border border-slate-400 px-3 py-2 text-right">Price</th><th class="border border-slate-400 px-3 py-2 text-right">Total</th></tr></thead>
                    <tbody>@foreach ($purchaseInvoice->items as $item)<tr><td class="border border-slate-300 px-3 py-2 font-semibold">{{ $item->item?->item_name }}</td><td class="border border-slate-300 px-3 py-2">{{ $item->description }}</td><td class="border border-slate-300 px-3 py-2">{{ str($item->unitMeasure?->name)->headline() }}</td><td class="border border-slate-300 px-3 py-2 text-right">{{ number_format((float) $item->quantity, 0) }}</td><td class="border border-slate-300 px-3 py-2 text-right">{{ number_format((float) $item->price, 2) }}</td><td class="border border-slate-300 px-3 py-2 text-right font-semibold">{{ number_format((float) $item->total, 2) }}</td></tr>@endforeach</tbody>
                </table>
            </div>
        </section>
        <section class="erp-panel"><div class="erp-panel-body"><x-purchasing.summary-table :totals="$purchaseInvoice->only(['subtotal', 'tax_amount', 'total_amount'])" /></div></section>
        <div class="flex justify-end gap-2">
            <a href="{{ route('purchasing.invoices.index') }}" class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Back</a>
            @can('update', $purchaseInvoice)<a href="{{ route('purchasing.invoices.edit', $purchaseInvoice) }}" class="rounded-md bg-slate-950 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">Edit</a>@endcan
        </div>
    </div>
</x-app-layout>
