<div class="space-y-5">
    <section class="erp-panel">
        <div class="erp-panel-header flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h3 class="text-base font-semibold text-slate-950">Sales Orders</h3>
                <p class="mt-1 text-sm text-slate-500">Manage customer sales order transactions without deducting inventory.</p>
            </div>
            @can('create', \App\Modules\Sales\Models\SalesOrder::class)
                <a href="{{ route('sales.orders.create') }}" class="inline-flex items-center justify-center rounded-md bg-slate-950 px-4 py-2 text-sm font-semibold text-white shadow-sm erp-focus-ring hover:bg-slate-800">
                    Create Sales Order
                </a>
            @endcan
        </div>

        <div class="erp-panel-body space-y-4">
            <div class="grid gap-3 lg:grid-cols-4">
                <button type="button" wire:click="setStatusFilter('')" @class([
                    'flex items-center gap-3 rounded-xl border bg-white px-4 py-3 text-left shadow-sm transition hover:border-emerald-300 hover:shadow',
                    'border-emerald-400 ring-1 ring-emerald-300' => $status === '',
                    'border-slate-200 hover:border-slate-300' => $status !== '',
                ])>
                    <span class="grid size-12 shrink-0 place-items-center rounded-full bg-emerald-600 text-white">
                        <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5v10.5H3.75V6.75Zm4.5 3h2.25v2.25H8.25V9.75Zm5.25 0h2.25v2.25H13.5V9.75ZM8.25 13.5h2.25v2.25H8.25V13.5Zm5.25 0h2.25v2.25H13.5V13.5Z" />
                        </svg>
                    </span>
                    <span>
                        <span class="block text-2xl font-bold leading-7 text-slate-950">{{ $statusCounts['all'] ?? 0 }}</span>
                        <span class="mt-1 block text-xs font-medium uppercase tracking-wider text-slate-600">All Orders</span>
                    </span>
                </button>

                <button type="button" wire:click="setStatusFilter('pending')" @class([
                    'flex items-center gap-3 rounded-xl border bg-white px-4 py-3 text-left shadow-sm transition hover:border-amber-300 hover:shadow',
                    'border-amber-400 ring-1 ring-amber-300' => $status === 'pending',
                    'border-slate-200 hover:border-slate-300' => $status !== 'pending',
                ])>
                    <span class="grid size-12 shrink-0 place-items-center rounded-full bg-amber-500 text-white">
                        <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2m5-2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </span>
                    <span>
                        <span class="block text-2xl font-bold leading-7 text-slate-950">{{ $statusCounts['pending'] ?? 0 }}</span>
                        <span class="mt-1 block text-xs font-medium uppercase tracking-wider text-slate-600">Pending Orders</span>
                    </span>
                </button>

                <button type="button" wire:click="setStatusFilter('partial')" @class([
                    'flex items-center gap-3 rounded-xl border bg-white px-4 py-3 text-left shadow-sm transition hover:border-cyan-300 hover:shadow',
                    'border-cyan-400 ring-1 ring-cyan-300' => $status === 'partial',
                    'border-slate-200 hover:border-slate-300' => $status !== 'partial',
                ])>
                    <span class="grid size-12 shrink-0 place-items-center rounded-full bg-cyan-500 text-white">
                        <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5 12 3 3 7.5m18 0v9L12 21m9-13.5L12 12m0 9v-9m0 0L3 7.5" />
                        </svg>
                    </span>
                    <span>
                        <span class="block text-2xl font-bold leading-7 text-slate-950">{{ $statusCounts['partial'] ?? 0 }}</span>
                        <span class="mt-1 block text-xs font-medium uppercase tracking-wider text-slate-600">Partial Orders</span>
                    </span>
                </button>

                <button type="button" wire:click="setStatusFilter('served')" @class([
                    'flex items-center gap-3 rounded-xl border bg-white px-4 py-3 text-left shadow-sm transition hover:border-slate-300 hover:shadow',
                    'border-slate-500 ring-1 ring-slate-300' => $status === 'served',
                    'border-slate-200 hover:border-slate-300' => $status !== 'served',
                ])>
                    <span class="grid size-12 shrink-0 place-items-center rounded-full" style="background-color:#64748b;color:#ffffff;">
                        <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 5.25 5.25L19.5 7.5" />
                        </svg>
                    </span>
                    <span>
                        <span class="block text-2xl font-bold leading-7 text-slate-950">{{ $statusCounts['served'] ?? 0 }}</span>
                        <span class="mt-1 block text-xs font-medium uppercase tracking-wider text-slate-600">Served Orders</span>
                    </span>
                </button>
            </div>

            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4 lg:items-end">
                <div>
                    <label class="block text-sm font-medium text-slate-700" for="sales-order-search">Search</label>
                    <input id="sales-order-search" type="search" wire:model.live.debounce.350ms="search" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm erp-focus-ring" placeholder="SO no, PO, or company">
                </div>

                <label class="block">
                    <span class="text-sm font-medium text-slate-700">Currency</span>
                    <select wire:model.live="currency" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm erp-focus-ring">
                        <option value="">All</option>
                        <option value="php">PHP</option>
                        <option value="dollar">Dollar</option>
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
                        <col class="w-[8%]">
                        <col class="w-[14%]">
                        <col class="w-[10%]">
                        <col class="w-[12%]">
                        <col class="w-[18%]">
                        <col class="w-[10%]">
                        <col class="w-[10%]">
                        <col class="w-[10%]">
                        <col class="w-[8%]">
                    </colgroup>
                    <thead class="bg-slate-50 text-xs font-semibold uppercase text-slate-500">
                        <tr>
                            <th class="px-3 py-3 text-center">Action</th>
                            <th class="px-3 py-3 text-left">Sales Order No</th>
                            <th class="px-3 py-3 text-center">Date</th>
                            <th class="px-3 py-3 text-left">P.O Number</th>
                            <th class="px-3 py-3 text-left">Company</th>
                            <th class="px-3 py-3 text-center">Items</th>
                            <th class="px-3 py-3 text-right">Total</th>
                            <th class="px-3 py-3 text-center">Attachments</th>
                            <th class="px-3 py-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @forelse ($salesOrders as $salesOrder)
                            <tr>
                                <td class="px-3 py-3 text-center align-middle">
                                    <x-dropdown align="left" width="48">
                                        <x-slot name="trigger">
                                            <button type="button" class="mx-auto inline-flex size-8 items-center justify-center rounded-md border border-slate-300 bg-white text-slate-600 hover:bg-slate-50 hover:text-slate-900" aria-label="Open actions">
                                                <svg class="size-4 text-slate-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path d="M10 3a1.75 1.75 0 1 0 0 3.5A1.75 1.75 0 0 0 10 3Zm0 5.75A1.75 1.75 0 1 0 10 12.25 1.75 1.75 0 0 0 10 8.75Zm0 5.75a1.75 1.75 0 1 0 0 3.5 1.75 1.75 0 0 0 0-3.5Z" />
                                                </svg>
                                            </button>
                                        </x-slot>

                                        <x-slot name="content">
                                            @can('view', $salesOrder)
                                                <a href="{{ route('sales.orders.show', $salesOrder) }}" class="block w-full px-4 py-2 text-start text-sm text-slate-700 hover:bg-slate-100">View</a>
                                            @endcan
                                            <!-- @can('print', $salesOrder)
                                                <button type="button" class="block w-full px-4 py-2 text-start text-sm text-slate-400">Print</button>
                                            @endcan -->
                                            @can('update', $salesOrder)
                                                <a href="{{ route('sales.orders.edit', $salesOrder) }}" class="block w-full px-4 py-2 text-start text-sm text-slate-700 hover:bg-slate-100">Edit</a>
                                            @endcan
                                            @can('delete', $salesOrder)
                                                <button type="button" wire:click="promptDeleteSalesOrder({{ $salesOrder->id }})" class="block w-full px-4 py-2 text-start text-sm text-red-700 hover:bg-red-50">Delete</button>
                                            @endcan
                                        </x-slot>
                                    </x-dropdown>
                                </td>
                                <td class="px-3 py-3 align-middle">
                                    <p class="truncate font-semibold text-slate-950">{{ $salesOrder->sales_order_no }}</p>
                                    <p class="mt-0.5 truncate text-xs text-slate-500">Created by {{ $salesOrder->creator?->name ?? 'System' }}</p>
                                </td>
                                <td class="px-3 py-3 text-center align-middle">
                                    <p class="font-medium text-slate-800">{{ $salesOrder->order_date?->format('M d, Y') }}</p>
                                </td>
                                <td class="px-3 py-3 align-middle">
                                    <p class="truncate font-medium text-slate-800">{{ $salesOrder->customer_po ?: 'No P.O. Number' }}</p>
                                    <p class="mt-0.5 truncate text-xs text-slate-500">Due {{ $salesOrder->delivery_date?->format('M d, Y') }}</p>
                                </td>
                                <td class="px-3 py-3 align-middle">
                                    <p class="truncate font-medium text-slate-800">{{ $salesOrder->businessPartner?->company_name ?? 'No company' }}</p>
                                    <p class="mt-0.5 truncate text-xs text-slate-500">{{ $salesOrder->contact_person }}{{ $salesOrder->contact_no ? ' | '.$salesOrder->contact_no : '' }}</p>
                                </td>
                                <td class="px-3 py-3 text-center align-middle">
                                    <button type="button" wire:click="toggleItemsDetails({{ $salesOrder->id }})" class="inline-flex items-center rounded-md border border-slate-300 bg-white px-2.5 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                        <span>{{ in_array($salesOrder->id, $expandedRows, true) ? 'Hide' : 'View' }}</span>
                                        <span class="ml-1.5">({{ $salesOrder->items->count() }})</span>
                                    </button>
                                </td>
                                <td class="px-3 py-3 text-right align-middle">
                                    <p class="font-semibold text-slate-950">{{ number_format((float) $salesOrder->total_amount, 2) }}</p>
                                    <p class="mt-0.5 text-xs text-slate-500">Tax {{ number_format((float) $salesOrder->tax_amount, 2) }}</p>
                                </td>
                                <td class="px-3 py-3 text-center align-middle">
                                    @if ($salesOrder->po_attachment)
                                        <a href="{{ Storage::disk('public')->url($salesOrder->po_attachment) }}" target="_blank" class="inline-flex items-center rounded-md bg-cyan-100 px-2.5 py-1.5 text-xs font-semibold text-cyan-800 hover:bg-cyan-200">View File</a>
                                    @else
                                        <span class="text-xs text-slate-500">No attachment</span>
                                    @endif
                                </td>
                                <td class="px-3 py-3 text-center align-middle">
                                    <x-sales.status-badge :status="$salesOrder->status" />
                                </td>
                            </tr>
                            @if (in_array($salesOrder->id, $expandedRows, true))
                                <tr class="bg-slate-50/60">
                                    <td colspan="9" class="px-3 py-3">
                                        <div class="overflow-x-auto rounded-md border border-slate-200 bg-white">
                                            <table class="min-w-[1400px] w-full table-fixed divide-y divide-slate-200 text-xs">
                                                <colgroup>
                                                    <col class="w-[180px]">
                                                    <col class="w-[140px]">
                                                    <col class="w-[100px]">
                                                    <col class="w-[110px]">
                                                    <col class="w-[110px]">
                                                    <col class="w-[120px]">
                                                    <col class="w-[130px]">
                                                    <col class="w-[120px]">
                                                    <col class="w-[120px]">
                                                    <col class="w-[90px]">
                                                    <col class="w-[110px]">
                                                    <col class="w-[110px]">
                                                    <col class="w-[120px]">
                                                </colgroup>
                                                <thead class="bg-slate-100 text-[11px] font-semibold uppercase text-slate-600">
                                                    <tr>
                                                        <th class="px-2 py-2 text-left">Item Name</th>
                                                        <th class="px-2 py-2 text-left">Delivery No</th>
                                                        <th class="px-2 py-2 text-left">Unit</th>
                                                        <th class="px-2 py-2 text-right">Ordered Qty</th>
                                                        <th class="px-2 py-2 text-right">Balance Qty</th>
                                                        <th class="px-2 py-2 text-right">Delivered Qty</th>
                                                        <th class="px-2 py-2 text-left">Delivered Date</th>
                                                        <th class="px-2 py-2 text-left">Delivered By</th>
                                                        <th class="px-2 py-2 text-left">Received By</th>
                                                        <th class="px-2 py-2 text-right">Tax Rate</th>
                                                        <th class="px-2 py-2 text-right">Tax Amount</th>
                                                        <th class="px-2 py-2 text-right">Subtotal</th>
                                                        <th class="px-2 py-2 text-right">Total Amount</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-slate-200">
                                                    @foreach ($salesOrder->items as $itemRow)
                                                        @php
                                                            $taxRate = (float) $salesOrder->tax_rate;
                                                            $subtotal = (float) $itemRow->total;
                                                            $taxAmount = round($subtotal * ($taxRate / 100), 2);
                                                            $totalAmount = round($subtotal + $taxAmount, 2);
                                                            $orderedQty = (float) $itemRow->order_quantity;
                                                            $balanceQty = (float) ($itemRow->balance_quantity ?? $orderedQty);
                                                            $activeDrItems = $itemRow->deliveryReceiptItems
                                                                ->filter(fn ($drItem) => optional($drItem->deliveryReceipt)->status !== 'cancelled');
                                                            $latestDrItem = $activeDrItems->sortByDesc('id')->first();
                                                            $latestDr = $latestDrItem?->deliveryReceipt;
                                                            $deliveredQty = (float) $activeDrItems->sum(fn ($drItem) => (float) ($drItem->delivered_quantity ?? 0));
                                                            $deliveryNo = (string) ($latestDrItem?->delivery_no ?: $latestDr?->delivery_receipt_no ?: 'Pending DR');
                                                            $deliveredDateValue = $latestDr?->received_date ?: $latestDrItem?->delivered_date ?: $latestDr?->dr_date;
                                                            $deliveredDate = $deliveredDateValue ? \Illuminate\Support\Carbon::parse($deliveredDateValue)->format('M d, Y') : 'Pending DR';
                                                            $deliveredBy = (string) ($latestDr?->delivered_by ?: $latestDrItem?->delivered_by ?: 'Pending DR');
                                                            $receivedBy = (string) ($latestDr?->received_by ?: $latestDrItem?->received_by ?: 'Pending DR');
                                                        @endphp
                                                        <tr class="text-slate-700">
                                                            <td class="px-2 py-2 font-medium text-slate-900">{{ $itemRow->item?->item_name ?? 'N/A' }}</td>
                                                            <td class="px-2 py-2">{{ $deliveryNo }}</td>
                                                            <td class="px-2 py-2">{{ str($itemRow->unitMeasure?->name)->headline() }}</td>
                                                            <td class="px-2 py-2 text-right">{{ number_format($orderedQty, 2) }}</td>
                                                            <td class="px-2 py-2 text-right">{{ number_format($balanceQty, 2) }}</td>
                                                            <td class="px-2 py-2 text-right">{{ number_format($deliveredQty, 2) }}</td>
                                                            <td class="px-2 py-2">{{ $deliveredDate }}</td>
                                                            <td class="px-2 py-2">{{ $deliveredBy }}</td>
                                                            <td class="px-2 py-2">{{ $receivedBy }}</td>
                                                            <td class="px-2 py-2 text-right">{{ number_format($taxRate, 0) }}%</td>
                                                            <td class="px-2 py-2 text-right">{{ number_format($taxAmount, 2) }}</td>
                                                            <td class="px-2 py-2 text-right">{{ number_format($subtotal, 2) }}</td>
                                                            <td class="px-2 py-2 text-right font-semibold text-slate-900">{{ number_format($totalAmount, 2) }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-10 text-center text-sm text-slate-500">No sales orders found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($salesOrders->total() > 0)
                @php
                    $currentPage = $salesOrders->currentPage();
                    $lastPage = $salesOrders->lastPage();
                    $startPage = max(1, $currentPage - 2);
                    $endPage = min($lastPage, $currentPage + 2);
                @endphp
                <div class="flex flex-col gap-3 border-t border-slate-200 pt-4 text-sm sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-slate-500">
                        Showing <span class="font-semibold text-slate-700">{{ $salesOrders->firstItem() }}</span>
                        to <span class="font-semibold text-slate-700">{{ $salesOrders->lastItem() }}</span>
                        of <span class="font-semibold text-slate-700">{{ $salesOrders->total() }}</span> records
                    </p>
                    <div class="flex flex-wrap items-center gap-1">
                        <button type="button" wire:click="previousPage" @disabled($salesOrders->onFirstPage()) class="inline-flex min-h-9 items-center rounded-md border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50">Previous</button>
                        @for ($page = $startPage; $page <= $endPage; $page++)
                            <button type="button" wire:click="gotoPage({{ $page }})" @class([
                                'inline-flex size-9 items-center justify-center rounded-md border text-sm font-semibold',
                                'border-slate-950 bg-slate-950 text-white' => $page === $currentPage,
                                'border-slate-300 bg-white text-slate-700 hover:bg-slate-50' => $page !== $currentPage,
                            ])>{{ $page }}</button>
                        @endfor
                        <button type="button" wire:click="nextPage" @disabled(! $salesOrders->hasMorePages()) class="inline-flex min-h-9 items-center rounded-md border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50">Next</button>
                    </div>
                </div>
            @endif
        </div>
    </section>

    <div x-data="{ open: @entangle('showDeleteConfirmation').live }" x-show="open" x-transition.opacity x-cloak class="fixed inset-0 z-50 flex items-center justify-center px-4 py-6 sm:px-0" role="dialog" aria-modal="true">
        <div class="absolute inset-0 bg-slate-950/60" @click="$wire.cancelDeleteConfirmation()"></div>
        <div class="relative w-full max-w-sm rounded-xl bg-white shadow-2xl">
            <div class="border-b border-slate-200 px-5 py-4">
                <h3 class="text-base font-semibold text-slate-950">Delete sales order?</h3>
                <p class="mt-1 text-sm text-slate-500">The sales order will be moved to deleted records.</p>
            </div>
            <div class="px-5 py-4">
                <p class="text-sm text-slate-600">Delete:</p>
                <p class="mt-1 text-sm font-semibold text-slate-950">{{ $pendingDeleteNo }}</p>
            </div>
            <div class="flex items-center justify-end gap-2 border-t border-slate-200 px-5 py-4">
                <button type="button" wire:click="cancelDeleteConfirmation" class="rounded-md border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancel</button>
                <button type="button" wire:click="deleteConfirmedSalesOrder" class="rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-700">Delete</button>
            </div>
        </div>
    </div>
</div>
