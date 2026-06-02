<div class="space-y-5">
    <section class="erp-panel">
        <div class="erp-panel-header flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h3 class="text-base font-semibold text-slate-950">Delivery Receipts</h3>
                <p class="mt-1 text-sm text-slate-500">Create delivery receipts from sales orders with partial stock support.</p>
            </div>
            @can('create', \App\Modules\Sales\Models\DeliveryReceipt::class)
                <a href="{{ route('sales.delivery-receipts.create') }}" class="inline-flex items-center justify-center rounded-md bg-slate-950 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-slate-800">Create Delivery Receipt</a>
            @endcan
        </div>

        <div class="erp-panel-body space-y-4">
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                <label class="block">
                    <span class="text-sm font-medium text-slate-700">Search</span>
                    <input type="search" wire:model.live.debounce.350ms="search" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm erp-focus-ring" placeholder="DR no, SO no, PO, company">
                </label>
                <label class="block">
                    <span class="text-sm font-medium text-slate-700">Status</span>
                    <select wire:model.live="status" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm erp-focus-ring">
                        <option value="">All statuses</option>
                        @foreach (\App\Modules\Sales\Helpers\DeliveryReceiptOptions::STATUSES as $option)
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
                        <col class="w-[17%]">
                        <col class="w-[13%]">
                        <col class="w-[17%]">
                        <col class="w-[13%]">
                        <col class="w-[16%]">
                        <col class="w-[11%]">
                        <col class="w-[13%]">
                    </colgroup>
                    <thead class="bg-slate-50 text-xs font-semibold uppercase text-slate-500">
                        <tr>
                            <th class="px-3 py-3 text-center">Action</th>
                            <th class="px-3 py-3 text-left">Delivery Receipt No</th>
                            <th class="px-3 py-3 text-left">Sales Order</th>
                            <th class="px-3 py-3 text-left">Company</th>
                            <th class="px-3 py-3 text-left">Customer PO</th>
                            <th class="px-3 py-3 text-left">Invoice Reference</th>
                            <th class="px-3 py-3 text-center">Date</th>
                            <th class="px-3 py-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @forelse ($deliveryReceipts as $receipt)
                            <tr>
                                <td class="px-3 py-3 text-center align-middle">
                                    <x-dropdown align="left" width="48">
                                        <x-slot name="trigger">
                                            <button type="button" class="mx-auto inline-flex size-8 items-center justify-center rounded-md border border-slate-300 bg-white text-slate-600 hover:bg-slate-50 hover:text-slate-900" aria-label="Open actions">
                                                <svg class="size-4 text-slate-600" viewBox="0 0 20 20" fill="currentColor"><path d="M10 3a1.75 1.75 0 1 0 0 3.5A1.75 1.75 0 0 0 10 3Zm0 5.75A1.75 1.75 0 1 0 10 12.25 1.75 1.75 0 0 0 10 8.75Zm0 5.75a1.75 1.75 0 1 0 0 3.5 1.75 1.75 0 0 0 0-3.5Z" /></svg>
                                            </button>
                                        </x-slot>
                                        <x-slot name="content">
                                            @can('view', $receipt)
                                                <a href="{{ route('sales.delivery-receipts.show', $receipt) }}" class="block w-full px-4 py-2 text-start text-sm text-slate-700 hover:bg-slate-100">View</a>
                                            @endcan
                                            @can('update', $receipt)
                                                <button type="button" wire:click="openUploadDetails({{ $receipt->id }})" class="block w-full px-4 py-2 text-start text-sm text-slate-700 hover:bg-slate-100">Upload Details</button>
                                            @endcan
                                            @can('cancel', $receipt)
                                                <button type="button" wire:click="promptVoidReceipt({{ $receipt->id }})" class="block w-full px-4 py-2 text-start text-sm text-red-700 hover:bg-red-50">Void</button>
                                            @endcan
                                        </x-slot>
                                    </x-dropdown>
                                </td>
                                <td class="px-3 py-3 align-middle">
                                    <p class="truncate font-semibold text-slate-900">{{ $receipt->delivery_receipt_no }}</p>
                                </td>
                                <td class="px-3 py-3 align-middle text-slate-700">{{ $receipt->sales_order_no }}</td>
                                <td class="px-3 py-3 align-middle text-slate-700">{{ $receipt->company_name }}</td>
                                <td class="px-3 py-3 align-middle text-slate-700">{{ $receipt->customer_po ?: 'None' }}</td>
                                <td class="px-3 py-3 align-middle">
                                    @if ($receipt->salesInvoices->isEmpty())
                                        <span class="inline-flex rounded-full bg-slate-600 px-2.5 py-1 text-xs font-semibold text-white">No Invoice</span>
                                    @else
                                        <div class="flex flex-wrap gap-1">
                                            @foreach ($receipt->salesInvoices->take(2) as $invoice)
                                                @if (auth()->user()?->can('sales-invoices.view'))
                                                    <a href="{{ route('sales.invoices.show', $invoice) }}" class="inline-flex rounded-full bg-cyan-600 px-2.5 py-1 text-xs font-semibold text-white hover:bg-cyan-700">{{ $invoice->sales_invoice_no }}</a>
                                                @else
                                                    <span class="inline-flex rounded-full bg-cyan-600 px-2.5 py-1 text-xs font-semibold text-white">{{ $invoice->sales_invoice_no }}</span>
                                                @endif
                                            @endforeach
                                            @if ($receipt->salesInvoices->count() > 2)
                                                <span class="inline-flex rounded-full bg-slate-700 px-2.5 py-1 text-xs font-semibold text-white">+{{ $receipt->salesInvoices->count() - 2 }}</span>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td class="px-3 py-3 text-center align-middle text-slate-700">{{ $receipt->dr_date?->format('M d, Y') }}</td>
                                <td class="px-3 py-3 text-center align-middle"><x-sales.status-badge :status="$receipt->status" /></td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="px-4 py-10 text-center text-sm text-slate-500">No delivery receipts found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($deliveryReceipts->total() > 0)
                @php
                    $currentPage = $deliveryReceipts->currentPage();
                    $lastPage = $deliveryReceipts->lastPage();
                    $startPage = max(1, $currentPage - 2);
                    $endPage = min($lastPage, $currentPage + 2);
                @endphp
                <div class="flex flex-col gap-3 border-t border-slate-200 pt-4 text-sm sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-slate-500">
                        Showing <span class="font-semibold text-slate-700">{{ $deliveryReceipts->firstItem() }}</span>
                        to <span class="font-semibold text-slate-700">{{ $deliveryReceipts->lastItem() }}</span>
                        of <span class="font-semibold text-slate-700">{{ $deliveryReceipts->total() }}</span> records
                    </p>
                    <div class="flex flex-wrap items-center gap-1">
                        <button type="button" wire:click="previousPage" @disabled($deliveryReceipts->onFirstPage()) class="inline-flex min-h-9 items-center rounded-md border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50">Previous</button>

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

                        <button type="button" wire:click="nextPage" @disabled(! $deliveryReceipts->hasMorePages()) class="inline-flex min-h-9 items-center rounded-md border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50">Next</button>
                    </div>
                </div>
            @endif
        </div>
    </section>

    <div x-data="{ open: @entangle('showVoidConfirmation').live }" x-show="open" x-transition.opacity x-cloak class="fixed inset-0 z-50 flex items-center justify-center px-4 py-6 sm:px-0" role="dialog" aria-modal="true">
        <div class="absolute inset-0 bg-slate-950/60" @click="$wire.cancelVoidConfirmation()"></div>
        <div class="relative w-full max-w-sm rounded-xl bg-white shadow-2xl">
            <div class="border-b border-slate-200 px-5 py-4">
                <h3 class="text-base font-semibold text-slate-950">Void delivery receipt?</h3>
                <p class="mt-1 text-sm text-slate-500">This receipt will be marked as void/cancelled.</p>
            </div>
            <div class="px-5 py-4">
                <p class="text-sm text-slate-600">Void:</p>
                <p class="mt-1 text-sm font-semibold text-slate-950">{{ $pendingVoidReceiptNo }}</p>
            </div>
            <div class="flex items-center justify-end gap-2 border-t border-slate-200 px-5 py-4">
                <button type="button" wire:click="cancelVoidConfirmation" class="rounded-md border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancel</button>
                <button type="button" wire:click="voidConfirmedReceipt" class="rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-700">Void</button>
            </div>
        </div>
    </div>
</div>
