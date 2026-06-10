@php
    $money = fn ($amount) => number_format((float) $amount, 2);
@endphp

<div class="space-y-5">
    <section class="erp-panel">
        <div class="erp-panel-header flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h3 class="text-base font-semibold text-slate-950">Top 10 Business Partners</h3>
                <p class="mt-1 text-sm text-slate-500">Based on sales order amount and order count.</p>
            </div>
            <button type="button" wire:click="resetFilters" class="inline-flex items-center justify-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50">
                Refresh
            </button>
        </div>

        <div class="erp-panel-body space-y-4">
            <div class="overflow-x-auto">
                <div style="display: grid; min-width: 780px; grid-template-columns: minmax(280px, 1fr) 180px 180px 120px; gap: 0.75rem; align-items: end;">
                    <label class="block">
                        <span class="text-xs font-semibold uppercase text-slate-500">Search</span>
                        <input type="search" wire:model="search" wire:keydown.enter="searchReports" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm erp-focus-ring" placeholder="Company, agent, or contact person">
                    </label>
                    <label class="block">
                        <span class="text-xs font-semibold uppercase text-slate-500">Date From</span>
                        <input type="date" wire:model="date_from" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm erp-focus-ring">
                    </label>
                    <label class="block">
                        <span class="text-xs font-semibold uppercase text-slate-500">Date To</span>
                        <input type="date" wire:model="date_to" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm erp-focus-ring">
                    </label>
                    <button type="button" wire:click="searchReports" class="inline-flex items-center justify-center rounded-md px-4 py-2 text-sm font-semibold text-white shadow-sm" style="background-color: #0e7490;">
                        Search
                    </button>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 text-sm text-slate-700">
                <span>Rows</span>
                <select wire:model.live="perPage" class="rounded-md border-slate-300 text-sm shadow-sm erp-focus-ring">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[1180px] table-fixed border border-slate-300 text-sm">
                    <colgroup>
                        <col style="width: 70px;">
                        <col style="width: 240px;">
                        <col style="width: 170px;">
                        <col style="width: 180px;">
                        <col style="width: 140px;">
                        <col style="width: 150px;">
                        <col style="width: 140px;">
                        <col style="width: 90px;">
                    </colgroup>
                    <thead class="bg-slate-100 text-xs font-semibold uppercase text-slate-600">
                        <tr>
                            <th class="border border-slate-300 px-3 py-3 text-center">Rank</th>
                            <th class="border border-slate-300 px-3 py-3 text-left">Company Name</th>
                            <th class="border border-slate-300 px-3 py-3 text-left">Agent</th>
                            <th class="border border-slate-300 px-3 py-3 text-left">Contact Person</th>
                            <th class="border border-slate-300 px-3 py-3 text-left">Contact No</th>
                            <th class="border border-slate-300 px-3 py-3 text-right">Total Amount</th>
                            <th class="border border-slate-300 px-3 py-3 text-center">Last Order</th>
                            <th class="border border-slate-300 px-3 py-3 text-center">Orders</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($partners as $partner)
                            <tr>
                                <td class="border border-slate-300 px-3 py-3 text-center font-semibold">{{ $partners->firstItem() + $loop->index }}</td>
                                <td class="border border-slate-300 px-3 py-3 font-semibold text-slate-950">{{ $partner->company_name }}</td>
                                <td class="border border-slate-300 px-3 py-3">{{ $partner->agent_name ?: '-' }}</td>
                                <td class="border border-slate-300 px-3 py-3">{{ $partner->contact_person ?: '-' }}</td>
                                <td class="border border-slate-300 px-3 py-3">{{ $partner->contact_no ?: '-' }}</td>
                                <td class="border border-slate-300 px-3 py-3 text-right font-semibold">{{ $money($partner->total_order_amount) }}</td>
                                <td class="border border-slate-300 px-3 py-3 text-center">{{ $partner->last_order_date ? \Carbon\Carbon::parse($partner->last_order_date)->format('M d, Y') : '-' }}</td>
                                <td class="border border-slate-300 px-3 py-3 text-center">{{ number_format((int) $partner->order_count) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="border border-slate-300 px-3 py-10 text-center text-slate-500">No business partner report data found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="flex flex-col gap-3 border-t border-slate-200 pt-4 text-sm sm:flex-row sm:items-center sm:justify-between">
                <p class="text-slate-500">
                    Showing <span class="font-semibold text-slate-700">{{ $partners->firstItem() ?? 0 }}</span>
                    to <span class="font-semibold text-slate-700">{{ $partners->lastItem() ?? 0 }}</span>
                    of <span class="font-semibold text-slate-700">{{ $partners->total() }}</span> records
                </p>
                <div class="flex flex-wrap items-center gap-1">{{ $partners->links() }}</div>
            </div>
        </div>
    </section>
</div>
