<div class="space-y-5">
    <section class="erp-panel">
        <div class="erp-panel-header">
            <h3 class="text-base font-semibold text-slate-950">Customer Selection</h3>
        </div>
        <div class="erp-panel-body">
            @php
                $clientOptions = $clients->map(fn ($client): array => [
                    'id' => (string) $client->id,
                    'company_name' => (string) $client->company_name,
                    'company_code' => (string) ($client->company_code ?: 'No code'),
                    'contact' => trim(($client->contact_person ?: '').($client->contact_no ? ' | '.$client->contact_no : '')) ?: 'No contact details',
                    'search' => strtolower(trim($client->company_name.' '.$client->company_code.' '.$client->contact_person.' '.$client->contact_no)),
                ])->values();
                $selectedClient = $clients->firstWhere('id', (int) $business_partner_id);
            @endphp

            <div
                class="max-w-3xl"
                wire:ignore.self
                x-data="{
                    open: false,
                    search: '',
                    selected: @js((string) $business_partner_id),
                    options: @js($clientOptions),
                    get selectedOption() {
                        return this.options.find((option) => String(option.id) === String(this.selected));
                    },
                    get filteredOptions() {
                        const term = this.search.trim().toLowerCase();
                        if (! term) return this.options;
                        return this.options.filter((option) => option.search.includes(term));
                    },
                    choose(option) {
                        this.selected = String(option.id);
                        this.open = false;
                        this.search = '';
                    },
                }"
                @click.away="open = false"
            >
                <span class="text-sm font-medium text-slate-700">Company Name</span>
                <div class="relative mt-1">
                    <button
                        type="button"
                        class="flex min-h-11 w-full items-center justify-between gap-3 rounded-md border border-slate-300 bg-white px-3 py-2 text-left text-sm shadow-sm erp-focus-ring"
                        @click="open = ! open; $nextTick(() => $refs.companySearch?.focus());"
                    >
                        <span class="min-w-0">
                            <span class="block truncate font-semibold" :class="selectedOption ? 'text-slate-950' : 'text-slate-500'" x-text="selectedOption ? selectedOption.company_name : 'Select company'"></span>
                            <span x-show="selectedOption" class="block truncate text-xs text-slate-500" x-text="selectedOption?.contact"></span>
                        </span>
                        <svg class="size-4 shrink-0 text-slate-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.22 7.22a.75.75 0 0 1 1.06 0L10 10.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 8.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                        </svg>
                    </button>

                    <div x-show="open" x-cloak class="absolute z-40 mt-1 w-full overflow-hidden rounded-md border border-slate-300 bg-white shadow-lg">
                        <div class="border-b border-slate-200 p-2">
                            <input
                                x-ref="companySearch"
                                type="search"
                                x-model="search"
                                class="block h-9 w-full rounded-md border-slate-300 px-3 text-sm shadow-sm erp-focus-ring"
                                placeholder="Search company name"
                            >
                        </div>
                        <div class="max-h-72 overflow-y-auto py-1">
                            <template x-for="option in filteredOptions" :key="option.id">
                                <button
                                    type="button"
                                    class="flex w-full flex-col gap-0.5 px-3 py-2 text-left text-sm hover:bg-cyan-50"
                                    :class="String(option.id) === String(selected) ? 'bg-cyan-50 text-cyan-900' : 'text-slate-800'"
                                    @click="choose(option); $wire.set('business_partner_id', String(option.id))"
                                >
                                    <span class="font-semibold" x-text="option.company_name"></span>
                                    <span class="text-xs text-slate-500" x-text="`${option.company_code} - ${option.contact}`"></span>
                                </button>
                            </template>

                            <div x-show="filteredOptions.length === 0" class="px-3 py-4 text-center text-sm text-slate-500">
                                No companies with unpaid invoices found.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if ($selectedClient)
                <div class="mt-4 grid gap-3 rounded-md border border-slate-200 bg-slate-50 p-4 text-sm sm:grid-cols-3">
                    <div>
                        <p class="text-xs font-semibold uppercase text-slate-500">Company</p>
                        <p class="mt-1 font-semibold text-slate-950">{{ $selectedClient->company_name }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase text-slate-500">Company Code</p>
                        <p class="mt-1 text-slate-700">{{ $selectedClient->company_code ?: '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase text-slate-500">Contact</p>
                        <p class="mt-1 text-slate-700">{{ $selectedClient->contact_person ?: '-' }}{{ $selectedClient->contact_no ? ' | '.$selectedClient->contact_no : '' }}</p>
                    </div>
                </div>
            @endif
        </div>
    </section>

    <section class="erp-panel">
        <div class="erp-panel-header flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h3 class="text-base font-semibold text-slate-950">Unpaid Sales Invoices</h3>
                <p class="mt-1 text-sm text-slate-500">Only unpaid sales invoices for the selected company are shown.</p>
            </div>
            <div class="rounded-md bg-slate-950 px-4 py-2 text-sm font-semibold text-white">
                Selected Balance: {{ number_format((float) $selectedTotalBalance, 2) }}
            </div>
        </div>
        <div class="erp-panel-body space-y-3">
            <div class="flex items-center justify-between gap-3">
                <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700">
                    <input type="checkbox" wire:model.live="select_all_invoices" class="rounded border-slate-300 text-cyan-700 shadow-sm erp-focus-ring" @disabled($invoices->isEmpty())>
                    <span>Select all invoices</span>
                </label>
                <p class="text-sm text-slate-500">{{ count($selected_invoice_ids) }} selected of {{ $invoices->count() }} invoice(s)</p>
            </div>

            <div class="relative overflow-visible rounded-lg border border-slate-200">
                <table class="w-full table-fixed divide-y divide-slate-200 text-sm">
                    <colgroup>
                        <col class="w-14">
                        <col class="w-[15%]">
                        <col class="w-[11%]">
                        <col class="w-[14%]">
                        <col class="w-[14%]">
                        <col class="w-[14%]">
                        <col class="w-[12%]">
                        <col class="w-[12%]">
                    </colgroup>
                    <thead class="bg-slate-50 text-xs font-semibold uppercase text-slate-500">
                        <tr>
                            <th class="px-3 py-3 text-center"></th>
                            <th class="px-3 py-3 text-left">Invoice No</th>
                            <th class="px-3 py-3 text-center">Date</th>
                            <th class="px-3 py-3 text-left">D.R No</th>
                            <th class="px-3 py-3 text-left">Sales Order</th>
                            <th class="px-3 py-3 text-left">Customer P.O</th>
                            <th class="px-3 py-3 text-right">Invoice Amount</th>
                            <th class="px-3 py-3 text-right">Balance</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @forelse ($invoices as $invoice)
                            <tr>
                                <td class="px-3 py-3 text-center align-middle">
                                    <input type="checkbox" wire:model.live="selected_invoice_ids" value="{{ $invoice->id }}" class="rounded border-slate-300 text-cyan-700 shadow-sm erp-focus-ring">
                                </td>
                                <td class="px-3 py-3 align-middle">
                                    <p class="font-semibold text-slate-950">{{ $invoice->sales_invoice_no }}</p>
                                    <p class="mt-0.5 text-xs text-slate-500">{{ strtoupper($invoice->currency) }}</p>
                                </td>
                                <td class="px-3 py-3 text-center align-middle text-slate-700">{{ $invoice->invoice_date?->format('M d, Y') }}</td>
                                <td class="px-3 py-3 align-middle text-slate-700">{{ $invoice->delivery_receipt_no ?: '-' }}</td>
                                <td class="px-3 py-3 align-middle text-slate-700">{{ $invoice->sales_order_no ?: '-' }}</td>
                                <td class="px-3 py-3 align-middle text-slate-700">{{ $invoice->customer_po ?: 'None' }}</td>
                                <td class="px-3 py-3 text-right align-middle font-semibold text-slate-950">{{ number_format((float) $invoice->total_amount, 2) }}</td>
                                <td class="px-3 py-3 text-right align-middle font-semibold text-slate-950">{{ number_format((float) $invoice->balance_amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-10 text-center text-sm text-slate-500">
                                    {{ $business_partner_id ? 'No unpaid sales invoices found for this company.' : 'Select a company to show unpaid sales invoices.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <div class="sticky bottom-0 flex items-center justify-end gap-2 border-t border-slate-200 bg-slate-100/95 py-4 backdrop-blur">
        <a href="{{ route('sales.collections.index') }}" class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancel</a>
        <button type="button" disabled class="rounded-md bg-slate-300 px-4 py-2 text-sm font-semibold text-slate-500">
            Continue
        </button>
    </div>
</div>
