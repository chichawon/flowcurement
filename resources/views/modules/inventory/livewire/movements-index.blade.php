<div class="space-y-5">
    <section class="erp-panel">
        <div class="erp-panel-header flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h3 class="text-base font-semibold text-slate-950">Stock Movements</h3>
                <p class="mt-1 text-sm text-slate-500">Inventory ledger showing stock in, stock out, and manual adjustments.</p>
            </div>
            @canany(['inventory.create', 'inventory.update'])
                <a href="{{ route('inventory.adjustments.create') }}" class="inline-flex items-center justify-center rounded-md bg-slate-950 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-slate-800">
                    Create Adjustment
                </a>
            @endcanany
        </div>

        <div class="erp-panel-body space-y-4">
            <div class="grid gap-3 lg:grid-cols-[minmax(240px,1fr)_180px_160px_160px_110px] lg:items-end">
                <label class="block">
                    <span class="text-sm font-medium text-slate-700">Search</span>
                    <input type="search" wire:model.live.debounce.350ms="search" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm erp-focus-ring" placeholder="Item, reference, remarks">
                </label>
                <label class="block">
                    <span class="text-sm font-medium text-slate-700">Movement</span>
                    <select wire:model.live="movement_type" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm erp-focus-ring">
                        <option value="">All</option>
                        <option value="stock_out">Stock Out</option>
                        <option value="adjustment_in">Adjustment In</option>
                        <option value="adjustment_out">Adjustment Out</option>
                    </select>
                </label>
                <label class="block">
                    <span class="text-sm font-medium text-slate-700">Date From</span>
                    <input type="date" wire:model.live="date_from" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm erp-focus-ring">
                </label>
                <label class="block">
                    <span class="text-sm font-medium text-slate-700">Date To</span>
                    <input type="date" wire:model.live="date_to" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm erp-focus-ring">
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
                <table class="w-full min-w-[1060px] table-fixed border border-slate-300 text-sm">
                    <colgroup>
                        <col style="width: 150px;">
                        <col style="width: 220px;">
                        <col style="width: 150px;">
                        <col style="width: 120px;">
                        <col style="width: 120px;">
                        <col style="width: 120px;">
                        <col style="width: 180px;">
                        <col style="width: 180px;">
                    </colgroup>
                    <thead class="bg-slate-100 text-xs font-semibold uppercase text-slate-600">
                        <tr>
                            <th class="border border-slate-300 px-3 py-3 text-left">Date / Time</th>
                            <th class="border border-slate-300 px-3 py-3 text-left">Item</th>
                            <th class="border border-slate-300 px-3 py-3 text-center">Movement</th>
                            <th class="border border-slate-300 px-3 py-3 text-right">Qty</th>
                            <th class="border border-slate-300 px-3 py-3 text-right">Before</th>
                            <th class="border border-slate-300 px-3 py-3 text-right">After</th>
                            <th class="border border-slate-300 px-3 py-3 text-left">Reference</th>
                            <th class="border border-slate-300 px-3 py-3 text-left">Encoded By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($movements as $movement)
                            <tr>
                                <td class="border border-slate-300 px-3 py-3">
                                    <p class="font-semibold">{{ $movement->created_at?->timezone(config('app.timezone'))->format('M d, Y') }}</p>
                                    <p class="text-xs text-slate-500">{{ $movement->created_at?->timezone(config('app.timezone'))->format('h:i A') }}</p>
                                </td>
                                <td class="border border-slate-300 px-3 py-3">
                                    <p class="font-semibold text-slate-950">{{ $movement->item?->item_name }}</p>
                                    <p class="text-xs uppercase text-slate-500">{{ $movement->item?->item_code }}</p>
                                </td>
                                <td class="border border-slate-300 px-3 py-3 text-center">
                                    @php
                                        $movementClass = match ($movement->movement_type) {
                                            'stock_out', 'adjustment_out' => 'bg-red-600',
                                            'adjustment_in' => 'bg-emerald-600',
                                            default => 'bg-slate-700',
                                        };
                                    @endphp
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold text-white {{ $movementClass }}">{{ str($movement->movement_type)->headline() }}</span>
                                </td>
                                <td class="border border-slate-300 px-3 py-3 text-right font-semibold">{{ number_format((float) $movement->quantity, 0) }}</td>
                                <td class="border border-slate-300 px-3 py-3 text-right">{{ number_format((float) $movement->before_stock, 0) }}</td>
                                <td class="border border-slate-300 px-3 py-3 text-right">{{ number_format((float) $movement->after_stock, 0) }}</td>
                                <td class="border border-slate-300 px-3 py-3">
                                    <p>{{ str($movement->reference_type)->headline() }}</p>
                                    <p class="text-xs text-slate-500">ID: {{ $movement->reference_id ?? '-' }}</p>
                                </td>
                                <td class="border border-slate-300 px-3 py-3">{{ $movement->creator?->name ?? 'System' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="border border-slate-300 px-3 py-10 text-center text-slate-500">No inventory movements found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="flex flex-col gap-3 border-t border-slate-200 pt-4 text-sm sm:flex-row sm:items-center sm:justify-between">
                <p class="text-slate-500">
                    Showing <span class="font-semibold text-slate-700">{{ $movements->firstItem() ?? 0 }}</span>
                    to <span class="font-semibold text-slate-700">{{ $movements->lastItem() ?? 0 }}</span>
                    of <span class="font-semibold text-slate-700">{{ $movements->total() }}</span> records
                </p>
                <div class="flex flex-wrap items-center gap-1">{{ $movements->links() }}</div>
            </div>
        </div>
    </section>
</div>
