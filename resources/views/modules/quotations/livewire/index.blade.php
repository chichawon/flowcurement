<div class="space-y-5">
    <section class="erp-panel">
        <div class="erp-panel-header flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h3 class="text-base font-semibold text-slate-950">Quotations</h3>
                <p class="mt-1 text-sm text-slate-500">Manage standalone client quotation transactions and computed totals.</p>
            </div>
            @can('create', \App\Modules\Quotations\Models\Quotation::class)
                <a href="{{ route('quotations.create') }}" class="inline-flex items-center justify-center rounded-md bg-slate-950 px-4 py-2 text-sm font-semibold text-white shadow-sm erp-focus-ring hover:bg-slate-800">
                    Create Quotation
                </a>
            @endcan
        </div>

        <div class="erp-panel-body space-y-4">
            <div class="grid gap-3 lg:grid-cols-5">
                <div class="lg:col-span-2">
                    <label class="block text-sm font-medium text-slate-700" for="quotation-search">Search</label>
                    <input id="quotation-search" type="search" wire:model.live.debounce.350ms="search" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm erp-focus-ring" placeholder="Quotation no or company">
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
                        <col class="w-20">
                        <col class="w-[22%]">
                        <col class="w-[24%]">
                        <col class="w-[14%]">
                        <col class="w-[14%]">
                        <col class="w-[14%]">
                        <col class="w-[12%]">
                    </colgroup>
                    <thead class="bg-slate-50 text-xs font-semibold uppercase text-slate-500">
                        <tr>
                            <th class="px-4 py-3 text-center">Action</th>
                            <th class="px-4 py-3 text-left">Quotation</th>
                            <th class="px-3 py-3 text-left">Client</th>
                            <th class="px-3 py-3 text-center">Dates</th>
                            <th class="px-3 py-3 text-right">Total</th>
                            <th class="px-3 py-3 text-center">Prepared By</th>
                            <th class="px-3 py-3 text-center">Reference</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @forelse ($quotations as $quotation)
                            <tr>
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
                                            @can('view', $quotation)
                                                <a href="{{ route('quotations.show', $quotation) }}" class="block w-full px-4 py-2 text-start text-sm text-slate-700 hover:bg-slate-100">View</a>
                                            @endcan
                                            @can('print', $quotation)
                                                <a href="{{ route('quotations.print', $quotation) }}" target="_blank" class="block w-full px-4 py-2 text-start text-sm text-slate-700 hover:bg-slate-100">Print</a>
                                            @endcan
                                            @can('update', $quotation)
                                                <a href="{{ route('quotations.edit', $quotation) }}" class="block w-full px-4 py-2 text-start text-sm text-slate-700 hover:bg-slate-100">Edit</a>
                                            @endcan
                                            @can('delete', $quotation)
                                                <button type="button" wire:click="promptDeleteQuotation({{ $quotation->id }})" class="block w-full px-4 py-2 text-start text-sm text-red-700 hover:bg-red-50">Delete</button>
                                            @endcan
                                        </x-slot>
                                    </x-dropdown>
                                </td>
                                <td class="px-4 py-3 align-middle">
                                    <p class="truncate font-semibold text-slate-950">{{ $quotation->quotation_no }}</p>
                                    <p class="mt-0.5 truncate text-xs text-slate-500">{{ str($quotation->currency)->upper() }} | Tax {{ number_format((float) $quotation->tax_rate, 0) }}%</p>
                                </td>
                                <td class="px-3 py-3 align-middle">
                                    <p class="truncate font-medium text-slate-800">{{ $quotation->businessPartner?->company_name ?? 'No client' }}</p>
                                    <p class="mt-0.5 truncate text-xs text-slate-500">{{ $quotation->contact_person }} {{ $quotation->contact_no ? '| '.$quotation->contact_no : '' }}</p>
                                </td>
                                <td class="px-3 py-3 text-center align-middle">
                                    <p class="font-medium text-slate-800">{{ $quotation->quotation_date?->format('M d, Y') }}</p>
                                    <p class="mt-0.5 text-xs text-slate-500">Valid {{ $quotation->validity_date?->format('M d, Y') }}</p>
                                </td>
                                <td class="px-3 py-3 text-right align-middle">
                                    <p class="font-semibold text-slate-950">{{ number_format((float) $quotation->total_amount, 2) }}</p>
                                    <p class="mt-0.5 text-xs text-slate-500">Base {{ number_format((float) $quotation->subtotal, 2) }}</p>
                                </td>
                                <td class="px-3 py-3 text-center align-middle">
                                    <p class="truncate text-sm text-slate-700">{{ $quotation->preparedBy?->name ?? 'System' }}</p>
                                </td>
                                <td class="px-3 py-3 text-center align-middle">
                                    @if ($quotation->referenceSalesOrder)
                                        <span class="inline-flex rounded-full bg-emerald-600 px-2.5 py-1 text-xs font-semibold text-white">
                                            {{ $quotation->referenceSalesOrder->sales_order_no }}
                                        </span>
                                    @else
                                        <span class="inline-flex rounded-full bg-slate-500 px-2.5 py-1 text-xs font-semibold text-white">
                                            No Reference
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-10 text-center text-sm text-slate-500">No quotations found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($quotations->total() > 0)
                @php
                    $currentPage = $quotations->currentPage();
                    $lastPage = $quotations->lastPage();
                    $startPage = max(1, $currentPage - 2);
                    $endPage = min($lastPage, $currentPage + 2);
                @endphp

                <div class="flex flex-col gap-3 border-t border-slate-200 pt-4 text-sm sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-slate-500">
                        Showing <span class="font-semibold text-slate-700">{{ $quotations->firstItem() }}</span>
                        to <span class="font-semibold text-slate-700">{{ $quotations->lastItem() }}</span>
                        of <span class="font-semibold text-slate-700">{{ $quotations->total() }}</span> records
                    </p>

                    <div class="flex flex-wrap items-center gap-1">
                        <button type="button" wire:click="previousPage" @disabled($quotations->onFirstPage()) class="inline-flex min-h-9 items-center rounded-md border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50">Previous</button>

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

                        <button type="button" wire:click="nextPage" @disabled(! $quotations->hasMorePages()) class="inline-flex min-h-9 items-center rounded-md border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50">Next</button>
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
        <div class="absolute inset-0 bg-slate-950/60" @click="$wire.cancelDeleteConfirmation()"></div>

        <div class="relative w-full max-w-sm rounded-xl bg-white shadow-2xl">
            <div class="border-b border-slate-200 px-5 py-4">
                <h3 class="text-base font-semibold text-slate-950">Delete quotation?</h3>
                <p class="mt-1 text-sm text-slate-500">The quotation will be moved to deleted records.</p>
            </div>
            <div class="px-5 py-4">
                <p class="text-sm text-slate-600">Delete:</p>
                <p class="mt-1 text-sm font-semibold text-slate-950">{{ $pendingDeleteNo }}</p>
            </div>
            <div class="flex items-center justify-end gap-2 border-t border-slate-200 px-5 py-4">
                <button type="button" wire:click="cancelDeleteConfirmation" class="rounded-md border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancel</button>
                <button type="button" wire:click="deleteConfirmedQuotation" class="rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-700">Delete</button>
            </div>
        </div>
    </div>
</div>
