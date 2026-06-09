<div class="space-y-5">
    <section class="erp-panel">
        <div class="erp-panel-header flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h3 class="text-base font-semibold text-slate-950">Purchase Orders</h3>
                <p class="mt-1 text-sm text-slate-500">Manage supplier purchase order requests and expected deliveries.</p>
            </div>
            @can('create', \App\Modules\Purchasing\Models\PurchaseOrder::class)
                <a href="{{ route('purchasing.orders.create') }}" class="inline-flex items-center justify-center rounded-md bg-slate-950 px-4 py-2 text-sm font-semibold text-white shadow-sm erp-focus-ring hover:bg-slate-800">Create Purchase Order</a>
            @endcan
        </div>

        <div class="erp-panel-body space-y-4">
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
                <label class="block lg:col-span-2">
                    <span class="text-sm font-medium text-slate-700">Search</span>
                    <input type="search" wire:model.live.debounce.350ms="search" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm erp-focus-ring" placeholder="PO no or supplier">
                </label>
                <label class="block">
                    <span class="text-sm font-medium text-slate-700">Status</span>
                    <select wire:model.live="status" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm erp-focus-ring">
                        <option value="">All</option>
                        @foreach ($statuses as $option)
                            <option value="{{ $option }}">{{ str($option)->headline() }}</option>
                        @endforeach
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
            </div>

            <div class="flex justify-end">
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
                        <col class="w-[15%]">
                        <col class="w-[13%]">
                        <col>
                        <col class="w-[12%]">
                        <col class="w-[12%]">
                        <col class="w-[12%]">
                        <col class="w-[10%]">
                    </colgroup>
                    <thead class="bg-slate-50 text-xs font-semibold uppercase text-slate-500">
                        <tr>
                            <th class="px-3 py-3 text-center">Action</th>
                            <th class="px-3 py-3 text-left">P.O No</th>
                            <th class="px-3 py-3 text-center">Date</th>
                            <th class="px-3 py-3 text-left">Supplier</th>
                            <th class="px-3 py-3 text-center">Expected</th>
                            <th class="px-3 py-3 text-right">Total</th>
                            <th class="px-3 py-3 text-center">Items</th>
                            <th class="px-3 py-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @forelse ($orders as $order)
                            <tr>
                                <td class="px-3 py-3 text-center">
                                    <x-dropdown align="left" width="48">
                                        <x-slot name="trigger">
                                            <button type="button" class="mx-auto inline-flex size-8 items-center justify-center rounded-md border border-slate-300 bg-white text-slate-600 hover:bg-slate-50">
                                                <svg class="size-4" viewBox="0 0 20 20" fill="currentColor"><path d="M10 3a1.75 1.75 0 1 0 0 3.5A1.75 1.75 0 0 0 10 3Zm0 5.75A1.75 1.75 0 1 0 10 12.25 1.75 1.75 0 0 0 10 8.75Zm0 5.75a1.75 1.75 0 1 0 0 3.5 1.75 1.75 0 0 0 0-3.5Z" /></svg>
                                            </button>
                                        </x-slot>
                                        <x-slot name="content">
                                            @can('view', $order)<a href="{{ route('purchasing.orders.show', $order) }}" class="flex w-full items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-slate-100"><x-action-icon name="view" /> View</a>@endcan
                                            @can('print', $order)<a href="{{ route('purchasing.orders.print', $order) }}" target="_blank" class="flex w-full items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-slate-100"><x-action-icon name="print" /> Print</a>@endcan
                                            @can('update', $order)<a href="{{ route('purchasing.orders.edit', $order) }}" class="flex w-full items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-slate-100"><x-action-icon name="edit" /> Edit</a>@endcan
                                            @can('cancel', $order)<button type="button" wire:click="cancel({{ $order->id }})" wire:confirm="Cancel this purchase order?" class="flex w-full items-center gap-2 px-4 py-2 text-sm text-red-700 hover:bg-red-50"><x-action-icon name="cancel" /> Cancel</button>@endcan
                                        </x-slot>
                                    </x-dropdown>
                                </td>
                                <td class="px-3 py-3 font-semibold text-slate-950">{{ $order->purchase_order_no }}</td>
                                <td class="px-3 py-3 text-center text-slate-700">{{ $order->purchase_order_date?->format('M d, Y') }}</td>
                                <td class="px-3 py-3">
                                    <p class="font-medium text-slate-900">{{ $order->supplier_name }}</p>
                                    <p class="text-xs text-slate-500">{{ $order->contact_person }}{{ $order->contact_no ? ' | '.$order->contact_no : '' }}</p>
                                </td>
                                <td class="px-3 py-3 text-center text-slate-700">{{ $order->expected_delivery_date?->format('M d, Y') ?: 'Open' }}</td>
                                <td class="px-3 py-3 text-right font-semibold text-slate-950">{{ number_format((float) $order->total_amount, 2) }}</td>
                                <td class="px-3 py-3 text-center text-slate-700">{{ $order->items_count }}</td>
                                <td class="px-3 py-3 text-center"><x-sales.status-badge :status="$order->status" /></td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="px-4 py-10 text-center text-sm text-slate-500">No purchase orders found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @include('modules.purchasing.partials.pagination-footer', ['paginator' => $orders])
        </div>
    </section>
</div>
