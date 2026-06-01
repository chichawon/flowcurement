<div class="space-y-5">
    @if (session('status'))
        <div class="rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
            {{ session('status') }}
        </div>
    @endif

    <section class="erp-panel">
        <div class="erp-panel-header flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h3 class="text-base font-semibold text-slate-950">{{ $title }}</h3>
                <p class="mt-1 text-sm text-slate-500">Search, filter, review commercial terms, and manage deleted records.</p>
            </div>
            @can('create', \App\Modules\BusinessPartners\Models\BusinessPartner::class)
                <a href="{{ route($routePrefix.'.create') }}" class="inline-flex items-center justify-center rounded-md bg-slate-950 px-4 py-2 text-sm font-semibold text-white shadow-sm erp-focus-ring hover:bg-slate-800">
                    Create {{ str($partnerType)->headline() }}
                </a>
            @endcan
        </div>

        <div class="erp-panel-body space-y-4">
            <div class="grid gap-3 lg:grid-cols-6">
                <div class="lg:col-span-2">
                    <label class="block text-sm font-medium text-slate-700" for="{{ $partnerType }}-search">Search</label>
                    <input id="{{ $partnerType }}-search" type="search" wire:model.live.debounce.350ms="search" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm erp-focus-ring" placeholder="Company, code, or contact person">
                </div>

                <label class="block">
                    <span class="text-sm font-medium text-slate-700">Status</span>
                    <select wire:model.live="status" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm erp-focus-ring">
                        <option value="">All statuses</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </label>

                <label class="block">
                    <span class="text-sm font-medium text-slate-700">VAT</span>
                    <select wire:model.live="vatable" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm erp-focus-ring">
                        <option value="">All VAT</option>
                        <option value="non_vat">Non VAT</option>
                        <option value="with_vat">With VAT</option>
                    </select>
                </label>

                <label class="block">
                    <span class="text-sm font-medium text-slate-700">Terms</span>
                    <select wire:model.live="terms" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm erp-focus-ring">
                        <option value="">All terms</option>
                        <option value="30">30 days</option>
                        <option value="60">60 days</option>
                        <option value="90">90 days</option>
                    </select>
                </label>

                <label class="block">
                    <span class="text-sm font-medium text-slate-700">Under PESA</span>
                    <select wire:model.live="under_pesa" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm erp-focus-ring">
                        <option value="">All</option>
                        <option value="yes">Yes</option>
                        <option value="no">No</option>
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
                        <col class="w-[26%]">
                        <col class="w-[18%]">
                        <col class="w-[20%]">
                        <col class="w-[10%]">
                        <col class="w-[14%]">
                        <col class="w-[12%]">
                    </colgroup>
                    <thead class="bg-slate-50 text-xs font-semibold uppercase text-slate-500">
                        <tr>
                            <th class="px-4 py-3 text-center">Action</th>
                            <th class="px-4 py-3 text-left">Company</th>
                            <th class="px-3 py-3 text-left">Contact</th>
                            <th class="px-3 py-3 text-left">Tax</th>
                            <th class="px-3 py-3 text-center">Terms</th>
                            <th class="px-3 py-3 text-right">Credit Limit</th>
                            <th class="px-3 py-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @forelse ($partners as $partner)
                            <tr class="{{ $partner->trashed() ? 'bg-slate-50 text-slate-500' : '' }}">
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
                                            @if ($partner->trashed())
                                                @can('restore', $partner)
                                                    <button type="button" wire:click="restorePartner({{ $partner->id }})" class="block w-full px-4 py-2 text-start text-sm text-slate-700 hover:bg-slate-100">Restore</button>
                                                @endcan
                                                @can('forceDelete', $partner)
                                                    <button type="button" wire:click="promptForceDeletePartner({{ $partner->id }})" class="block w-full px-4 py-2 text-start text-sm text-red-700 hover:bg-red-50">Delete Forever</button>
                                                @endcan
                                            @else
                                                @can('view', $partner)
                                                    <a href="{{ route($routePrefix.'.show', $partner) }}" class="block w-full px-4 py-2 text-start text-sm text-slate-700 hover:bg-slate-100">View</a>
                                                @endcan
                                                @can('update', $partner)
                                                    <a href="{{ route($routePrefix.'.edit', $partner) }}" class="block w-full px-4 py-2 text-start text-sm text-slate-700 hover:bg-slate-100">Edit</a>
                                                @endcan
                                                @can('delete', $partner)
                                                    <button type="button" wire:click="promptDeletePartner({{ $partner->id }})" class="block w-full px-4 py-2 text-start text-sm text-red-700 hover:bg-red-50">Delete</button>
                                                @endcan
                                            @endif
                                        </x-slot>
                                    </x-dropdown>
                                </td>
                                <td class="px-4 py-3 align-middle">
                                    <div class="min-w-0">
                                        <p class="truncate font-semibold text-slate-950">{{ $partner->company_name }}</p>
                                        <p class="mt-0.5 text-xs font-medium uppercase text-slate-500">{{ $partner->company_code }}</p>
                                        <p class="mt-1 max-w-sm truncate text-xs text-slate-400">{{ $partner->company_address ?: 'No address provided' }}</p>
                                    </div>
                                </td>
                                <td class="px-3 py-3 align-middle">
                                    <p class="truncate font-medium text-slate-800">{{ $partner->contact_person }}</p>
                                    <p class="mt-0.5 text-xs text-slate-500">{{ $partner->contact_no }}</p>
                                </td>
                                <td class="px-3 py-3 align-middle">
                                    <p class="text-xs text-slate-500">{{ $partner->tin_number }}</p>
                                    <div class="mt-1 flex flex-wrap gap-1">
                                        <x-business-partners.value-badge :value="$partner->vatable" tone="cyan" />
                                        <x-business-partners.value-badge :value="$partner->under_pesa" tone="amber" />
                                    </div>
                                </td>
                                <td class="px-3 py-3 text-center align-middle">{{ $partner->terms }} days</td>
                                <td class="px-3 py-3 text-right align-middle font-semibold text-slate-900">{{ number_format((float) $partner->credit_limit, 2) }}</td>
                                <td class="px-3 py-3 text-center align-middle">
                                    <div class="flex flex-col items-center gap-1">
                                        <x-business-partners.status-badge :status="$partner->status" />
                                        @if ($partner->trashed())
                                            <span class="inline-flex rounded-full bg-red-600 px-2.5 py-1 text-xs font-semibold text-white">Deleted</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-10 text-center text-sm text-slate-500">No {{ str($partnerType)->plural() }} found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($partners->total() > 0)
                @php
                    $currentPage = $partners->currentPage();
                    $lastPage = $partners->lastPage();
                    $startPage = max(1, $currentPage - 2);
                    $endPage = min($lastPage, $currentPage + 2);
                @endphp

                <div class="flex flex-col gap-3 border-t border-slate-200 pt-4 text-sm sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-slate-500">
                        Showing <span class="font-semibold text-slate-700">{{ $partners->firstItem() }}</span>
                        to <span class="font-semibold text-slate-700">{{ $partners->lastItem() }}</span>
                        of <span class="font-semibold text-slate-700">{{ $partners->total() }}</span> records
                    </p>

                    <div class="flex flex-wrap items-center gap-1">
                        <button
                            type="button"
                            wire:click="previousPage"
                            @disabled($partners->onFirstPage())
                            class="inline-flex min-h-9 items-center rounded-md border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            Previous
                        </button>

                        @if ($startPage > 1)
                            <button type="button" wire:click="gotoPage(1)" class="inline-flex size-9 items-center justify-center rounded-md border border-slate-300 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50">1</button>
                            @if ($startPage > 2)
                                <span class="px-2 text-slate-400">...</span>
                            @endif
                        @endif

                        @for ($page = $startPage; $page <= $endPage; $page++)
                            <button
                                type="button"
                                wire:click="gotoPage({{ $page }})"
                                @class([
                                    'inline-flex size-9 items-center justify-center rounded-md border text-sm font-semibold',
                                    'border-slate-950 bg-slate-950 text-white' => $page === $currentPage,
                                    'border-slate-300 bg-white text-slate-700 hover:bg-slate-50' => $page !== $currentPage,
                                ])
                            >
                                {{ $page }}
                            </button>
                        @endfor

                        @if ($endPage < $lastPage)
                            @if ($endPage < $lastPage - 1)
                                <span class="px-2 text-slate-400">...</span>
                            @endif
                            <button type="button" wire:click="gotoPage({{ $lastPage }})" class="inline-flex size-9 items-center justify-center rounded-md border border-slate-300 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50">{{ $lastPage }}</button>
                        @endif

                        <button
                            type="button"
                            wire:click="nextPage"
                            @disabled(! $partners->hasMorePages())
                            class="inline-flex min-h-9 items-center rounded-md border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            Next
                        </button>
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
                    {{ $pendingDeleteMode === 'forceDelete' ? 'Delete forever?' : 'Delete '.str($partnerType)->headline().'?' }}
                </h3>
                <p class="mt-1 text-sm text-slate-500">
                    {{ $pendingDeleteMode === 'forceDelete' ? 'This action cannot be undone.' : 'The record will be moved to deleted records.' }}
                </p>
            </div>
            <div class="px-5 py-4">
                <p class="text-sm text-slate-600">Delete:</p>
                <p class="mt-1 text-sm font-semibold text-slate-950">{{ $pendingDeleteName }}</p>
            </div>
            <div class="flex items-center justify-end gap-2 border-t border-slate-200 px-5 py-4">
                <button type="button" class="rounded-md border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50" @click="open = false">Cancel</button>
                <button type="button" class="rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-700" wire:click="deleteConfirmedPartner">
                    {{ $pendingDeleteMode === 'forceDelete' ? 'Delete Forever' : 'Delete' }}
                </button>
            </div>
        </div>
    </div>
</div>
