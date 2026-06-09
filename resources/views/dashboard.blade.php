<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-medium text-cyan-700">Enterprise Resource Planning</p>
                <h2 class="text-2xl font-semibold text-slate-950">Operations Dashboard</h2>
            </div>
            <div class="text-sm text-slate-500">
                <p id="dashboard-current-date" class="text-xs font-semibold uppercase tracking-wide text-cyan-700">
                    {{ now()->timezone(config('app.timezone'))->format('l, M d, Y') }}
                </p>
                <p id="dashboard-current-time" class="mt-1 text-xl font-bold text-slate-950">
                    {{ now()->timezone(config('app.timezone'))->format('h:i:s A') }}
                </p>
                <!-- <p class="mt-1 text-xs text-slate-500">{{ auth()->user()->name }}</p> -->
            </div>
        </div>
    </x-slot>

    @php
    $money = fn ($value): string => number_format((float) $value, 2);
    $compactMoney = fn ($value): string => (float) $value >= 1000000
    ? number_format((float) $value / 1000000, 2).'M'
    : ((float) $value >= 1000 ? number_format((float) $value / 1000, 1).'K' : number_format((float) $value, 0));

    $flowSteps = [
    ['label' => 'Quotation', 'value' => $operations['quotations']['total'], 'meta' => $operations['quotations']['this_month'].' this month', 'route' => 'quotations.index', 'bg' => '#e0f2f1', 'border' => '#99d8d3', 'accent' => '#0f766e'],
    ['label' => 'Sales Order', 'value' => $operations['sales_orders']['pending'] + $operations['sales_orders']['partial'] + $operations['sales_orders']['served'], 'meta' => $operations['sales_orders']['pending'].' pending', 'route' => 'sales.orders.index', 'bg' => '#e8eef5', 'border' => '#b7c4d6', 'accent' => '#475569'],
    ['label' => 'D.R', 'value' => $operations['delivery_receipts']['pending'] + $operations['delivery_receipts']['billed'] + $operations['delivery_receipts']['cancelled'], 'meta' => $operations['delivery_receipts']['billed'].' billed', 'route' => 'sales.delivery-receipts.index', 'bg' => '#f8ead8', 'border' => '#e3bd8b', 'accent' => '#b45309'],
    ['label' => 'Invoice', 'value' => $operations['invoices']['unpaid'] + $operations['invoices']['paid'] + $operations['invoices']['cancelled'], 'meta' => $operations['invoices']['unpaid'].' unpaid', 'route' => 'sales.invoices.index', 'bg' => '#e6edf5', 'border' => '#adc0d4', 'accent' => '#1e3a5f'],
    ['label' => 'Collection', 'value' => $summary['pending_collections'], 'meta' => 'pending review', 'route' => 'sales.collections.index', 'bg' => '#eaf3df', 'border' => '#bfd89f', 'accent' => '#4d7c0f'],
    ];
    @endphp

    <div class="space-y-6">
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase text-slate-500">Total Collections</p>
                        <p class="mt-2 text-2xl font-bold text-slate-950">{{ $money($summary['total_collections']) }}</p>
                    </div>
                    <span class="grid size-11 place-items-center rounded-md bg-emerald-600 text-white">
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.25h5.25a2.25 2.25 0 0 0 0-4.5h-4.5a2.25 2.25 0 0 1 0-4.5H15M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </span>
                </div>
                <p class="mt-3 text-sm text-slate-500">Posted payment applications excluding cancelled collections.</p>
            </section>

            <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase text-slate-500">This Month</p>
                        <p class="mt-2 text-2xl font-bold text-slate-950">{{ $money($summary['monthly_collections']) }}</p>
                    </div>
                    <span class="grid size-11 place-items-center rounded-md bg-cyan-600 text-white">
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75Zm6.75-4.5c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625Zm6.75-4.5c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                        </svg>
                    </span>
                </div>
                <p class="mt-3 text-sm text-slate-500">Current month collection performance.</p>
            </section>

            <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase text-slate-500">Unpaid Balance</p>
                        <p class="mt-2 text-2xl font-bold text-slate-950">{{ $money($summary['unpaid_balance']) }}</p>
                    </div>
                    <span class="grid size-11 place-items-center rounded-md bg-amber-500 text-white">
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                        </svg>
                    </span>
                </div>
                <p class="mt-3 text-sm text-slate-500">{{ $operations['invoices']['unpaid'] }} unpaid invoice(s) still open.</p>
            </section>

            <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase text-slate-500">Inventory Alerts</p>
                        <p class="mt-2 text-2xl font-bold text-slate-950">{{ $operations['items']['low'] + $operations['items']['out'] }}</p>
                    </div>
                    <span class="grid size-11 place-items-center rounded-md bg-red-600 text-white">
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 7.5-9-4.5-9 4.5m18 0-9 4.5m9-4.5v9l-9 4.5m0-9L3 7.5m9 4.5v9M3 7.5v9l9 4.5" />
                        </svg>
                    </span>
                </div>
                <p class="mt-3 text-sm text-slate-500">{{ $operations['items']['low'] }} low stock, {{ $operations['items']['out'] }} out of stock.</p>
            </section>
        </div>

        <section class="rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4">
                <h3 class="text-base font-semibold text-slate-950">System Flow</h3>
            </div>
            <div class="grid gap-3 p-5 lg:grid-cols-5">
                @foreach ($flowSteps as $index => $step)
                <a href="{{ route($step['route']) }}" class="group relative rounded-lg border px-4 py-4 shadow-sm transition hover:brightness-[.98]" style="background-color: {{ $step['bg'] }}; border-color: {{ $step['border'] }};">
                    <div class="flex items-center justify-between gap-3">
                        <span class="grid size-9 place-items-center rounded-md text-sm font-bold text-white shadow-sm" style="background-color: {{ $step['accent'] }};">{{ $index + 1 }}</span>
                        <span class="text-xs font-semibold uppercase" style="color: {{ $step['accent'] }};">{{ $step['meta'] }}</span>
                    </div>
                    <p class="mt-4 text-sm font-semibold text-slate-950">{{ $step['label'] }}</p>
                    <p class="mt-1 text-2xl font-bold text-slate-950">{{ $step['value'] }}</p>
                </a>
                @endforeach
            </div>
        </section>

        <div class="grid gap-6 xl:grid-cols-3">
            <section class="rounded-lg border border-slate-200 bg-white shadow-sm xl:col-span-2">
                <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                    <div>
                        <h3 class="text-base font-semibold text-slate-950">Monthly Collections</h3>
                        <p class="mt-1 text-sm text-slate-500">{{ $currentYear }} collection trend</p>
                    </div>
                    <span class="text-sm font-semibold text-slate-700">Max {{ $compactMoney($maxMonthlyCollection) }}</span>
                </div>
                <div class="px-5 py-5">
                    <div style="width: 100%; min-width: 0;">
                        <div class="border-b border-l border-slate-200" style="position: relative; height: 280px;">
                            <div style="position: absolute; inset: 0; background-image: linear-gradient(to bottom, rgba(203, 213, 225, .65) 1px, transparent 1px); background-size: 100% 25%;"></div>

                            <div style="position: absolute; inset: 0; display: grid; grid-template-columns: repeat(12, minmax(0, 1fr)); column-gap: 10px; align-items: end; padding: 32px 12px 0;">
                                @foreach ($monthlyCollections as $index => $total)
                                @php
                                $height = (float) $total > 0 ? max(5, ((float) $total / $maxMonthlyCollection) * 100) : 0;
                                @endphp
                                <div style="position: relative; z-index: 1; display: flex; align-items: end; justify-content: center; height: 100%; min-width: 0;">
                                    <div
                                        class="w-full rounded-t-md shadow-sm"
                                        style="height: {{ $height }}%; max-width: 42px; background: linear-gradient(180deg, #14b8a6 0%, #0f766e 100%);"
                                        title="{{ $monthLabels[$index] }}: {{ $money($total) }}"></div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="px-3 pt-3" style="display: grid; grid-template-columns: repeat(12, minmax(0, 1fr)); column-gap: 10px;">
                            @foreach ($monthLabels as $monthLabel)
                            <span class="text-center text-[11px] font-medium text-slate-500">{{ $monthLabel }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>

            <section class="rounded-lg border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-5 py-4">
                    <h3 class="text-base font-semibold text-slate-950">Yearly Collections</h3>
                </div>
                <div class="space-y-4 p-5">
                    @foreach ($yearlyCollections as $row)
                    @php $width = max(3, ((float) $row['total'] / $maxYearlyCollection) * 100); @endphp
                    <div>
                        <div class="mb-1 flex items-center justify-between gap-3 text-sm">
                            <span class="font-semibold text-slate-700">{{ $row['year'] }}</span>
                            <span class="text-slate-500">{{ $money($row['total']) }}</span>
                        </div>
                        <div class="h-3 overflow-hidden rounded-full bg-slate-100">
                            <div class="h-full rounded-full bg-emerald-600" style="width: {{ $width }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </section>
        </div>

        <div class="grid gap-6 xl:grid-cols-3">
            <section class="rounded-lg border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-5 py-4">
                    <h3 class="text-base font-semibold text-slate-950">Sales Health</h3>
                </div>
                <div class="space-y-3 p-5">
                    <div class="grid grid-cols-3 gap-2 text-center">
                        <div class="rounded-md bg-amber-50 p-3">
                            <p class="text-xl font-bold text-amber-700">{{ $operations['sales_orders']['pending'] }}</p>
                            <p class="text-xs font-semibold uppercase text-amber-700">Pending SO</p>
                        </div>
                        <div class="rounded-md bg-cyan-50 p-3">
                            <p class="text-xl font-bold text-cyan-700">{{ $operations['sales_orders']['partial'] }}</p>
                            <p class="text-xs font-semibold uppercase text-cyan-700">Partial SO</p>
                        </div>
                        <div class="rounded-md bg-emerald-50 p-3">
                            <p class="text-xl font-bold text-emerald-700">{{ $operations['sales_orders']['served'] }}</p>
                            <p class="text-xs font-semibold uppercase text-emerald-700">Served SO</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-2 text-center">
                        <div class="rounded-md bg-amber-50 p-3">
                            <p class="text-xl font-bold text-amber-700">{{ $operations['delivery_receipts']['pending'] }}</p>
                            <p class="text-xs font-semibold uppercase text-amber-700">Pending D.R</p>
                        </div>
                        <div class="rounded-md bg-slate-100 p-3">
                            <p class="text-xl font-bold text-slate-700">{{ $operations['delivery_receipts']['billed'] }}</p>
                            <p class="text-xs font-semibold uppercase text-slate-600">Billed D.R</p>
                        </div>
                        <div class="rounded-md bg-red-50 p-3">
                            <p class="text-xl font-bold text-red-700">{{ $operations['delivery_receipts']['cancelled'] }}</p>
                            <p class="text-xs font-semibold uppercase text-red-700">Cancelled</p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="rounded-lg border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-5 py-4">
                    <h3 class="text-base font-semibold text-slate-950">Master Data</h3>
                </div>
                <div class="grid grid-cols-2 gap-3 p-5">
                    <div class="rounded-md border border-slate-200 p-4">
                        <p class="text-xs font-semibold uppercase text-slate-500">Clients</p>
                        <p class="mt-2 text-2xl font-bold text-slate-950">{{ $operations['partners']['clients'] }}</p>
                    </div>
                    <div class="rounded-md border border-slate-200 p-4">
                        <p class="text-xs font-semibold uppercase text-slate-500">Suppliers</p>
                        <p class="mt-2 text-2xl font-bold text-slate-950">{{ $operations['partners']['suppliers'] }}</p>
                    </div>
                    <div class="rounded-md border border-slate-200 p-4">
                        <p class="text-xs font-semibold uppercase text-slate-500">Items</p>
                        <p class="mt-2 text-2xl font-bold text-slate-950">{{ $operations['items']['all'] }}</p>
                    </div>
                    <div class="rounded-md border border-red-200 bg-red-50 p-4">
                        <p class="text-xs font-semibold uppercase text-red-700">Out of Stock</p>
                        <p class="mt-2 text-2xl font-bold text-red-700">{{ $operations['items']['out'] }}</p>
                    </div>
                </div>
            </section>

            <section class="rounded-lg border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-5 py-4">
                    <h3 class="text-base font-semibold text-slate-950">Recent Collections</h3>
                </div>
                <div class="divide-y divide-slate-200">
                    @forelse ($recentCollections as $collection)
                    <div class="px-5 py-3">
                        <div class="flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-slate-950">{{ $collection->collection_no }}</p>
                                <p class="truncate text-xs text-slate-500">{{ $collection->company_name }} - {{ $collection->collection_receipt_no }}</p>
                            </div>
                            <p class="shrink-0 text-sm font-semibold text-slate-950">{{ $money($collection->applied_amount) }}</p>
                        </div>
                    </div>
                    @empty
                    <p class="px-5 py-8 text-center text-sm text-slate-500">No collections yet.</p>
                    @endforelse
                </div>
            </section>
        </div>

        <section class="rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4">
                <h3 class="text-base font-semibold text-slate-950">Transaction Logs</h3>
                <form id="dashboard-log-filter-form" method="GET" action="{{ route('dashboard.transaction-logs') }}" class="mt-4">
                    <div style="display: flex; flex-wrap: wrap; align-items: flex-end; gap: 16px;">
                        <div style="flex: 1 1 360px; min-width: 260px;">
                            <label for="log_search" class="mb-1 block text-sm font-semibold text-slate-950">Search</label>
                            <input
                                id="log_search"
                                name="log_search"
                                type="search"
                                value="{{ $logFilters['search'] }}"
                                placeholder="Module, action, description, or user"
                                class="w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-cyan-600 focus:ring-cyan-600">
                        </div>

                        <div style="flex: 0 0 190px;">
                            <label for="log_from" class="mb-1 block text-sm font-semibold text-slate-950">Date From</label>
                            <input
                                id="log_from"
                                name="log_from"
                                type="date"
                                value="{{ $logFilters['from'] }}"
                                class="w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-cyan-600 focus:ring-cyan-600">
                        </div>

                        <div style="flex: 0 0 190px;">
                            <label for="log_to" class="mb-1 block text-sm font-semibold text-slate-950">Date To</label>
                            <input
                                id="log_to"
                                name="log_to"
                                type="date"
                                value="{{ $logFilters['to'] }}"
                                class="w-full rounded-md border-slate-300 text-sm shadow-sm focus:border-cyan-600 focus:ring-cyan-600">
                        </div>

                        <button type="submit" class="inline-flex items-center justify-center rounded-md bg-slate-950 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-slate-800" style="flex: 0 0 auto;">
                            Search
                        </button>

                    </div>

                    <div class="mt-4 flex items-center justify-end gap-2 text-sm text-slate-950">
                        <label for="log_limit">Rows</label>
                        <select
                            id="log_limit"
                            name="log_limit"
                            class="w-20 rounded-md border-slate-300 text-sm shadow-sm focus:border-cyan-600 focus:ring-cyan-600">
                            @foreach ([10, 25, 50, 100] as $limit)
                            <option value="{{ $limit }}" @selected((int) $logFilters['limit']===$limit)>{{ $limit }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
            <div id="dashboard-log-table" class="p-5">
                @include('dashboard.partials.transaction-logs-table', ['recentLogs' => $recentLogs])
            </div>
        </section>
    </div>

    <script>
        (() => {
            const dateEl = document.getElementById('dashboard-current-date');
            const timeEl = document.getElementById('dashboard-current-time');
            const timezone = @json(config('app.timezone'));

            const updateClock = () => {
                if (!dateEl || !timeEl) return;

                const now = new Date();

                dateEl.textContent = new Intl.DateTimeFormat('en-US', {
                    weekday: 'long',
                    month: 'short',
                    day: '2-digit',
                    year: 'numeric',
                    timeZone: timezone,
                }).format(now);

                timeEl.textContent = new Intl.DateTimeFormat('en-US', {
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    hour12: true,
                    timeZone: timezone,
                }).format(now);
            };

            updateClock();
            window.setInterval(updateClock, 1000);

            const form = document.getElementById('dashboard-log-filter-form');
            const table = document.getElementById('dashboard-log-table');

            if (!form || !table) return;

            const loadLogs = async (requestedUrl = null) => {
                const params = new URLSearchParams(new FormData(form));
                const url = requestedUrl || `${form.action}?${params.toString()}`;

                table.style.opacity = '0.55';

                try {
                    const response = await fetch(url, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        credentials: 'same-origin',
                    });

                    if (!response.ok) throw new Error('Unable to load transaction logs.');

                    const payload = await response.json();
                    table.innerHTML = payload.html;
                } finally {
                    table.style.opacity = '1';
                }
            };

            form.addEventListener('submit', (event) => {
                event.preventDefault();
                loadLogs();
            });

            form.querySelector('[name="log_limit"]')?.addEventListener('change', loadLogs);

            table.addEventListener('click', (event) => {
                const link = event.target.closest('.dashboard-log-page');

                if (!link) return;

                event.preventDefault();
                loadLogs(link.href);
            });
        })();
    </script>
</x-app-layout>
