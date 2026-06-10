<div class="space-y-5">
    <section class="erp-panel">
        <div class="erp-panel-header flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h3 class="text-base font-semibold text-slate-950">Stock List</h3>
                <p class="mt-1 text-sm text-slate-500">Monitor available stocks, reorder points, and stock status.</p>
            </div>
            @canany(['inventory.create', 'inventory.update'])
                <a href="{{ route('inventory.adjustments.create') }}" class="inline-flex items-center justify-center rounded-md bg-slate-950 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-slate-800">
                    Create Adjustment
                </a>
            @endcanany
        </div>

        <div class="erp-panel-body space-y-4">
            <div class="grid gap-3 lg:grid-cols-3">
                <button type="button" wire:click="setStockFilter('all')" @class([
                    'flex items-center gap-3 rounded-lg border bg-white px-4 py-3 text-left shadow-sm transition',
                    'border-emerald-500 ring-1 ring-emerald-500' => $stock_filter === 'all',
                    'border-slate-200 hover:border-emerald-300' => $stock_filter !== 'all',
                ])>
                    <span class="grid size-11 shrink-0 place-items-center rounded-full bg-emerald-600 text-white">
                        <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25m0-9L3 7.5m9 5.25v9m-9-14.25v9l9 5.25" /></svg>
                    </span>
                    <span><span class="block text-2xl font-bold text-slate-950">{{ $stockSummary['all'] }}</span><span class="text-xs font-medium uppercase tracking-wider text-slate-600">All Stock</span></span>
                </button>
                <button type="button" wire:click="setStockFilter('low')" @class([
                    'flex items-center gap-3 rounded-lg border bg-white px-4 py-3 text-left shadow-sm transition',
                    'border-amber-500 ring-1 ring-amber-500' => $stock_filter === 'low',
                    'border-slate-200 hover:border-amber-300' => $stock_filter !== 'low',
                ])>
                    <span class="grid size-11 shrink-0 place-items-center rounded-full bg-amber-500 text-white">
                        <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" /></svg>
                    </span>
                    <span><span class="block text-2xl font-bold text-slate-950">{{ $stockSummary['low'] }}</span><span class="text-xs font-medium uppercase tracking-wider text-slate-600">Low Stock</span></span>
                </button>
                <button type="button" wire:click="setStockFilter('out')" @class([
                    'flex items-center gap-3 rounded-lg border bg-white px-4 py-3 text-left shadow-sm transition',
                    'border-red-500 ring-1 ring-red-500' => $stock_filter === 'out',
                    'border-slate-200 hover:border-red-300' => $stock_filter !== 'out',
                ])>
                    <span class="grid size-11 shrink-0 place-items-center rounded-full bg-red-600 text-white">
                        <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                    </span>
                    <span><span class="block text-2xl font-bold text-slate-950">{{ $stockSummary['out'] }}</span><span class="text-xs font-medium uppercase tracking-wider text-slate-600">Out Of Stock</span></span>
                </button>
            </div>

            <div class="grid gap-3 lg:grid-cols-5">
                <label class="block lg:col-span-2">
                    <span class="text-sm font-medium text-slate-700">Search</span>
                    <input type="search" wire:model.live.debounce.350ms="search" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm erp-focus-ring" placeholder="Item name or code">
                </label>
                <label class="block">
                    <span class="text-sm font-medium text-slate-700">Item Type</span>
                    <select wire:model.live="item_type" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm erp-focus-ring">
                        <option value="">All types</option>
                        @foreach ($itemTypes as $type)
                            <option value="{{ $type->name }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="block">
                    <span class="text-sm font-medium text-slate-700">Supplier</span>
                    <select wire:model.live="supplier_id" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm erp-focus-ring">
                        <option value="">All suppliers</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->company_name }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="block">
                    <span class="text-sm font-medium text-slate-700">Rows</span>
                    <select wire:model.live="perPage" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm erp-focus-ring">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                </label>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[980px] table-fixed border border-slate-300 text-sm">
                    <colgroup>
                        <col style="width: 120px;">
                        <col style="width: 240px;">
                        <col style="width: 180px;">
                        <col style="width: 180px;">
                        <col style="width: 120px;">
                        <col style="width: 120px;">
                        <col style="width: 150px;">
                    </colgroup>
                    <thead class="bg-slate-100 text-xs font-semibold uppercase text-slate-600">
                        <tr>
                            <th class="border border-slate-300 px-3 py-3 text-left">Item Code</th>
                            <th class="border border-slate-300 px-3 py-3 text-left">Item Name</th>
                            <th class="border border-slate-300 px-3 py-3 text-left">Item Type</th>
                            <th class="border border-slate-300 px-3 py-3 text-left">Supplier</th>
                            <th class="border border-slate-300 px-3 py-3 text-right">Available</th>
                            <th class="border border-slate-300 px-3 py-3 text-right">Reorder Point</th>
                            <th class="border border-slate-300 px-3 py-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($items as $item)
                            <tr>
                                <td class="border border-slate-300 px-3 py-3 font-semibold">{{ $item->item_code }}</td>
                                <td class="border border-slate-300 px-3 py-3 font-semibold text-slate-950">{{ $item->item_name }}</td>
                                <td class="border border-slate-300 px-3 py-3">{{ $item->item_type }}</td>
                                <td class="border border-slate-300 px-3 py-3">{{ $item->supplier?->company_name ?? '-' }}</td>
                                <td class="border border-slate-300 px-3 py-3 text-right font-semibold">{{ number_format((int) $item->available_stock) }}</td>
                                <td class="border border-slate-300 px-3 py-3 text-right">{{ number_format((int) $item->reorder_point) }}</td>
                                <td class="border border-slate-300 px-3 py-3 text-center">
                                    @if ((int) $item->available_stock <= 0)
                                        <span class="inline-flex rounded-full bg-red-600 px-3 py-1 text-xs font-semibold text-white">Out of Stock</span>
                                    @elseif ((int) $item->available_stock <= (int) $item->reorder_point)
                                        <span class="inline-flex rounded-full bg-amber-500 px-3 py-1 text-xs font-semibold text-white">Low Stock</span>
                                    @else
                                        <span class="inline-flex rounded-full bg-emerald-600 px-3 py-1 text-xs font-semibold text-white">Available</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="border border-slate-300 px-3 py-10 text-center text-slate-500">No stock records found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="flex flex-col gap-3 border-t border-slate-200 pt-4 text-sm sm:flex-row sm:items-center sm:justify-between">
                <p class="text-slate-500">
                    Showing <span class="font-semibold text-slate-700">{{ $items->firstItem() ?? 0 }}</span>
                    to <span class="font-semibold text-slate-700">{{ $items->lastItem() ?? 0 }}</span>
                    of <span class="font-semibold text-slate-700">{{ $items->total() }}</span> records
                </p>
                <div class="flex flex-wrap items-center gap-1">{{ $items->links() }}</div>
            </div>
        </div>
    </section>
</div>
