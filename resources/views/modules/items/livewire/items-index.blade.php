<div class="space-y-5">
    <section class="erp-panel">
        <div class="erp-panel-header flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h3 class="text-base font-semibold text-slate-950">{{ $title }}</h3>
                <p class="mt-1 text-sm text-slate-500">Manage item master records, supplier prices, computed selling prices, and reorder monitoring.</p>
            </div>
            @can('create', \App\Modules\Items\Models\Item::class)
                <a href="{{ route($routePrefix.'.create') }}" class="inline-flex items-center justify-center rounded-md bg-slate-950 px-4 py-2 text-sm font-semibold text-white shadow-sm erp-focus-ring hover:bg-slate-800">
                    Create Item
                </a>
            @endcan
        </div>

        <div class="erp-panel-body space-y-4">
            <div class="grid gap-3 lg:grid-cols-3">
                <button
                    type="button"
                    wire:click="setStockFilter('all')"
                    @class([
                        'flex items-center gap-3 rounded-xl border bg-white px-4 py-3 text-left shadow-sm transition hover:border-emerald-300 hover:shadow',
                        'border-emerald-500 ring-1 ring-emerald-500' => $stock_filter === 'all',
                        'border-slate-200' => $stock_filter !== 'all',
                    ])
                >
                    <span class="grid size-12 shrink-0 place-items-center rounded-full bg-emerald-600 text-white">
                        <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25m0-9L3 7.5m9 5.25v9m-9-14.25v9l9 5.25" />
                        </svg>
                    </span>
                    <span>
                        <span class="block text-2xl font-bold leading-7 text-slate-950">{{ $stockSummary['all'] }}</span>
                        <span class="mt-1 block text-xs font-medium uppercase tracking-wider text-slate-600">All Stock</span>
                    </span>
                </button>

                <button
                    type="button"
                    wire:click="setStockFilter('low')"
                    @class([
                        'flex items-center gap-3 rounded-xl border bg-white px-4 py-3 text-left shadow-sm transition hover:border-amber-300 hover:shadow',
                        'border-amber-500 ring-1 ring-amber-500' => $stock_filter === 'low',
                        'border-slate-200' => $stock_filter !== 'low',
                    ])
                >
                    <span class="grid size-12 shrink-0 place-items-center rounded-full bg-amber-500 text-white">
                        <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25m0-9L3 7.5m9 5.25v9m-9-14.25v9l9 5.25" />
                        </svg>
                    </span>
                    <span>
                        <span class="block text-2xl font-bold leading-7 text-slate-950">{{ $stockSummary['low'] }}</span>
                        <span class="mt-1 block text-xs font-medium uppercase tracking-wider text-slate-600">Low Stock</span>
                    </span>
                </button>

                <button
                    type="button"
                    wire:click="setStockFilter('out')"
                    @class([
                        'flex items-center gap-3 rounded-xl border bg-white px-4 py-3 text-left shadow-sm transition hover:border-red-300 hover:shadow',
                        'border-red-500 ring-1 ring-red-500' => $stock_filter === 'out',
                        'border-slate-200' => $stock_filter !== 'out',
                    ])
                >
                    <span class="grid size-12 shrink-0 place-items-center rounded-full bg-red-500 text-white">
                        <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25m0-9L3 7.5m9 5.25v9m-9-14.25v9l9 5.25" />
                        </svg>
                    </span>
                    <span>
                        <span class="block text-2xl font-bold leading-7 text-slate-950">{{ $stockSummary['out'] }}</span>
                        <span class="mt-1 block text-xs font-medium uppercase tracking-wider text-slate-600">Out Of Stock</span>
                    </span>
                </button>
            </div>

            <div class="grid gap-3 lg:grid-cols-6">
                <div class="lg:col-span-2">
                    <label class="block text-sm font-medium text-slate-700" for="item-search">Search</label>
                    <input id="item-search" type="search" wire:model.live.debounce.350ms="search" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm erp-focus-ring" placeholder="Item name or code">
                </div>

                <label class="block">
                    <span class="text-sm font-medium text-slate-700">Type</span>
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
                    <span class="text-sm font-medium text-slate-700">Taxable</span>
                    <select wire:model.live="taxable" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm erp-focus-ring">
                        <option value="">All</option>
                        <option value="yes">Yes</option>
                        <option value="no">No</option>
                    </select>
                </label>

                <label class="block">
                    <span class="text-sm font-medium text-slate-700">Status</span>
                    <select wire:model.live="status" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm erp-focus-ring">
                        <option value="">All statuses</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </label>
            </div>

            <div class="flex items-center justify-end gap-3">
                <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                    <span>Rows</span>
                    <select wire:model.live="perPage" class="rounded-md border-slate-300 text-sm shadow-sm erp-focus-ring">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                </label>
            </div>

            <div class="relative overflow-visible rounded-lg border border-slate-200">
                <table class="w-full table-fixed divide-y divide-slate-200 text-sm">
                    <colgroup>
                        <col class="w-20">
                        <col class="w-[28%]">
                        <col class="w-[20%]">
                        <col class="w-[16%]">
                        <col class="w-[12%]">
                        <col class="w-[12%]">
                        <col class="w-[12%]">
                    </colgroup>
                    <thead class="bg-slate-50 text-xs font-semibold uppercase text-slate-500">
                        <tr>
                            <th class="px-4 py-3 text-center">Action</th>
                            <th class="px-4 py-3 text-left">Item</th>
                            <th class="px-3 py-3 text-left">Supplier</th>
                            <th class="px-3 py-3 text-right">Pricing</th>
                            <th class="px-3 py-3 text-center">Stock</th>
                            <th class="px-3 py-3 text-center">Tax</th>
                            <th class="px-3 py-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @forelse ($items as $item)
                            <tr class="{{ $item->trashed() ? 'bg-slate-50 text-slate-500' : '' }}">
                                <td class="px-4 py-3 text-center align-middle">
                                    <x-dropdown align="left" width="48">
                                        <x-slot name="trigger">
                                            <button type="button" class="mx-auto inline-flex size-8 items-center justify-center rounded-md border border-slate-300 bg-white text-slate-600 hover:bg-slate-50 hover:text-slate-900" aria-label="Open actions">
                                                <svg class="size-4 text-slate-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path d="M10 3a1.75 1.75 0 1 0 0 3.5A1.75 1.75 0 0 0 10 3Zm0 5.75A1.75 1.75 0 1 0 10 12.25 1.75 1.75 0 0 0 10 8.75Zm0 5.75a1.75 1.75 0 1 0 0 3.5 1.75 1.75 0 0 0 0-3.5Z" />
                                                </svg>
                                            </button>
                                        </x-slot>

                                        <x-slot name="content">
                                            @if ($item->trashed())
                                                @can('restore', $item)
                                                    <button type="button" wire:click="restoreItem({{ $item->id }})" class="block w-full px-4 py-2 text-start text-sm text-slate-700 hover:bg-slate-100">Restore</button>
                                                @endcan
                                                @can('forceDelete', $item)
                                                    <button type="button" wire:click="promptForceDeleteItem({{ $item->id }})" class="block w-full px-4 py-2 text-start text-sm text-red-700 hover:bg-red-50">Delete Forever</button>
                                                @endcan
                                            @else
                                                @can('view', $item)
                                                    <a href="{{ route($routePrefix.'.show', $item) }}" class="block w-full px-4 py-2 text-start text-sm text-slate-700 hover:bg-slate-100">View</a>
                                                @endcan
                                                @can('update', $item)
                                                    <a href="{{ route($routePrefix.'.edit', $item) }}" class="block w-full px-4 py-2 text-start text-sm text-slate-700 hover:bg-slate-100">Edit</a>
                                                @endcan
                                                @can('delete', $item)
                                                    <button type="button" wire:click="promptDeleteItem({{ $item->id }})" class="block w-full px-4 py-2 text-start text-sm text-red-700 hover:bg-red-50">Delete</button>
                                                @endcan
                                            @endif
                                        </x-slot>
                                    </x-dropdown>
                                </td>
                                <td class="px-4 py-3 align-middle">
                                    <div class="grid grid-cols-[3rem_minmax(0,1fr)] items-center gap-3">
                                        <div class="overflow-hidden rounded-md border border-slate-200 bg-slate-50">
                                            @if ($item->item_image)
                                                <img src="{{ \App\Modules\Items\Helpers\ItemImage::url($item->item_image) }}" alt="{{ $item->item_name }}" class="size-12 bg-white object-contain">
                                            @else
                                                <div class="grid size-12 place-items-center bg-slate-100 text-xs font-semibold text-slate-400">{{ strtoupper(substr($item->item_name, 0, 1)) }}</div>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <p class="truncate font-semibold text-slate-950">{{ $item->item_name }}</p>
                                            <p class="mt-0.5 truncate text-xs font-medium uppercase text-slate-500">{{ $item->item_code }} - {{ $item->item_type }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-3 align-middle">
                                    <p class="truncate font-medium text-slate-800">{{ $item->supplier?->company_name ?? 'No supplier' }}</p>
                                    <p class="mt-0.5 text-xs text-slate-500">{{ $item->supplier?->company_code }}</p>
                                </td>
                                <td class="px-3 py-3 text-right align-middle">
                                    <p class="font-semibold text-slate-950">{{ number_format((float) $item->item_price, 2) }}</p>
                                    <p class="mt-0.5 text-xs text-slate-500">Cost {{ number_format((float) $item->supplier_price, 2) }} + {{ number_format((float) $item->percentage, 2) }}%</p>
                                </td>
                                <td class="px-3 py-3 text-center align-middle">
                                    <p class="font-semibold text-slate-950">{{ $item->available_stock }}</p>
                                    <p class="text-xs text-slate-500">ROP {{ $item->reorder_point }}</p>
                                    <div class="mt-1"><x-items.stock-badge :item="$item" /></div>
                                </td>
                                <td class="px-3 py-3 text-center align-middle">
                                    <span class="inline-flex rounded-md bg-cyan-50 px-2 py-1 text-xs font-medium text-cyan-800">{{ str($item->taxable)->headline() }}</span>
                                </td>
                                <td class="px-3 py-3 text-center align-middle">
                                    <div class="flex flex-col items-center gap-1">
                                        <x-items.status-badge :status="$item->status" />
                                        @if ($item->trashed())
                                            <span class="inline-flex rounded-full bg-red-600 px-2.5 py-1 text-xs font-semibold text-white">Deleted</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-10 text-center text-sm text-slate-500">No items found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($items->total() > 0)
                @php
                    $currentPage = $items->currentPage();
                    $lastPage = $items->lastPage();
                    $startPage = max(1, $currentPage - 2);
                    $endPage = min($lastPage, $currentPage + 2);
                @endphp

                <div class="flex flex-col gap-3 border-t border-slate-200 pt-4 text-sm sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-slate-500">
                        Showing <span class="font-semibold text-slate-700">{{ $items->firstItem() }}</span>
                        to <span class="font-semibold text-slate-700">{{ $items->lastItem() }}</span>
                        of <span class="font-semibold text-slate-700">{{ $items->total() }}</span> records
                    </p>

                    <div class="flex flex-wrap items-center gap-1">
                        <button type="button" wire:click="previousPage" @disabled($items->onFirstPage()) class="inline-flex min-h-9 items-center rounded-md border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50">Previous</button>

                        @if ($startPage > 1)
                            <button type="button" wire:click="gotoPage(1)" class="inline-flex size-9 items-center justify-center rounded-md border border-slate-300 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50">1</button>
                            @if ($startPage > 2)
                                <span class="px-2 text-slate-400">...</span>
                            @endif
                        @endif

                        @for ($page = $startPage; $page <= $endPage; $page++)
                            <button type="button" wire:click="gotoPage({{ $page }})" @class([
                                'inline-flex size-9 items-center justify-center rounded-md border text-sm font-semibold',
                                'border-slate-950 bg-slate-950 text-white' => $page === $currentPage,
                                'border-slate-300 bg-white text-slate-700 hover:bg-slate-50' => $page !== $currentPage,
                            ])>{{ $page }}</button>
                        @endfor

                        @if ($endPage < $lastPage)
                            @if ($endPage < $lastPage - 1)
                                <span class="px-2 text-slate-400">...</span>
                            @endif
                            <button type="button" wire:click="gotoPage({{ $lastPage }})" class="inline-flex size-9 items-center justify-center rounded-md border border-slate-300 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50">{{ $lastPage }}</button>
                        @endif

                        <button type="button" wire:click="nextPage" @disabled(! $items->hasMorePages()) class="inline-flex min-h-9 items-center rounded-md border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50">Next</button>
                    </div>
                </div>
            @endif
        </div>
    </section>

    <div
        x-data="{ open: @entangle('showDeleteConfirmation').live }"
        x-show="open"
        x-transition.opacity
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center px-4 py-6 sm:px-0"
        role="dialog"
        aria-modal="true"
    >
        <div class="absolute inset-0 bg-slate-950/60" @click="open = false"></div>

        <div class="relative w-full max-w-sm rounded-xl bg-white shadow-2xl">
            <div class="border-b border-slate-200 px-5 py-4">
                <h3 class="text-base font-semibold text-slate-950">
                    {{ $pendingDeleteMode === 'forceDelete' ? 'Delete forever?' : 'Delete item?' }}
                </h3>
                <p class="mt-1 text-sm text-slate-500">
                    {{ $pendingDeleteMode === 'forceDelete' ? 'This action cannot be undone.' : 'The item will be moved to deleted records.' }}
                </p>
            </div>
            <div class="px-5 py-4">
                <p class="text-sm text-slate-600">Delete:</p>
                <p class="mt-1 text-sm font-semibold text-slate-950">{{ $pendingDeleteName }}</p>
            </div>
            <div class="flex items-center justify-end gap-2 border-t border-slate-200 px-5 py-4">
                <button type="button" class="rounded-md border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50" @click="open = false">Cancel</button>
                <button type="button" class="rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-700" wire:click="deleteConfirmedItem">
                    {{ $pendingDeleteMode === 'forceDelete' ? 'Delete Forever' : 'Delete' }}
                </button>
            </div>
        </div>
    </div>
</div>
