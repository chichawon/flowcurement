<div class="space-y-5">
    <section class="erp-panel">
        <div class="erp-panel-header flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h3 class="text-base font-semibold text-slate-950">Sales Collections</h3>
                <p class="mt-1 text-sm text-slate-500">Landing page for collection processing and invoice payment tracking.</p>
            </div>
            @can('sales-collections.create')
                <a href="{{ route('sales.collections.create') }}" class="inline-flex items-center justify-center rounded-md bg-slate-950 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-slate-800">
                    Create Collection
                </a>
            @endcan
        </div>

        <div class="erp-panel-body space-y-4">
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                <label class="block lg:col-span-2">
                    <span class="text-sm font-medium text-slate-700">Search</span>
                    <input type="search" wire:model.live.debounce.350ms="search" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm erp-focus-ring" placeholder="Collection no, invoice no, OR no, company">
                </label>
                <label class="block">
                    <span class="text-sm font-medium text-slate-700">Status</span>
                    <select wire:model.live="status" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm erp-focus-ring">
                        <option value="">All statuses</option>
                        <option value="pending">Pending</option>
                        <option value="cancelled">Cancelled</option>
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
                        <col class="w-[15%]">
                        <col class="w-[12%]">
                        <col class="w-[15%]">
                        <col class="w-[16%]">
                        <col class="w-[14%]">
                        <col class="w-[12%]">
                        <col class="w-[10%]">
                    </colgroup>
                    <thead class="bg-slate-50 text-xs font-semibold uppercase text-slate-500">
                        <tr>
                            <th class="px-3 py-3 text-center">Action</th>
                            <th class="px-3 py-3 text-left">Collection No</th>
                            <th class="px-3 py-3 text-center">Date</th>
                            <th class="px-3 py-3 text-left">Invoice No</th>
                            <th class="px-3 py-3 text-left">Company</th>
                            <th class="px-3 py-3 text-left">Payment Ref</th>
                            <th class="px-3 py-3 text-right">Amount</th>
                            <th class="px-3 py-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @forelse ($collections as $collection)
                            <tr>
                                <td class="px-3 py-3 text-center align-middle">
                                    <button type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-md border border-slate-300 bg-white text-slate-600 shadow-sm" title="Actions">
                                        <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path d="M10 3.5a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM10 8.5a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM11.5 15a1.5 1.5 0 1 0-3 0 1.5 1.5 0 0 0 3 0Z" />
                                        </svg>
                                    </button>
                                </td>
                                <td class="px-3 py-3 align-middle">
                                    <p class="font-semibold text-slate-950">{{ $collection->collection_no }}</p>
                                    <p class="mt-0.5 text-xs text-slate-500">{{ $collection->collection_receipt_no }}</p>
                                </td>
                                <td class="px-3 py-3 text-center align-middle text-slate-700">{{ $collection->collection_receipt_date?->format('M d, Y') }}</td>
                                <td class="px-3 py-3 align-middle text-slate-700">
                                    {{ $collection->invoices->pluck('sales_invoice_no')->take(2)->join(', ') ?: '-' }}
                                    @if ($collection->invoices->count() > 2)
                                        <span class="text-xs text-slate-500">+{{ $collection->invoices->count() - 2 }} more</span>
                                    @endif
                                </td>
                                <td class="px-3 py-3 align-middle text-slate-700">{{ $collection->company_name }}</td>
                                <td class="px-3 py-3 align-middle text-slate-700">
                                    <p>{{ $collection->bank_name ?: '-' }}</p>
                                    <p class="mt-0.5 text-xs text-slate-500">Check No: {{ $collection->check_number }}</p>
                                </td>
                                <td class="px-3 py-3 text-right align-middle font-semibold text-slate-950">{{ number_format((float) $collection->applied_amount, 2) }}</td>
                                <td class="px-3 py-3 text-center align-middle">
                                    @php
                                        $statusClass = $collection->status === 'cancelled' ? 'bg-red-600' : 'bg-amber-600';
                                    @endphp
                                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold text-white {{ $statusClass }}">{{ str($collection->status)->headline() }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-10 text-center text-sm text-slate-500">
                                    No sales collections found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="flex flex-col gap-3 border-t border-slate-200 pt-4 text-sm sm:flex-row sm:items-center sm:justify-between">
                <p class="text-slate-500">
                    Showing <span class="font-semibold text-slate-700">{{ $collections->firstItem() ?? 0 }}</span>
                    to <span class="font-semibold text-slate-700">{{ $collections->lastItem() ?? 0 }}</span>
                    of <span class="font-semibold text-slate-700">{{ $collections->total() }}</span> records
                </p>
                <div class="flex flex-wrap items-center gap-1">
                    {{ $collections->links() }}
                </div>
            </div>
        </div>
    </section>
</div>
