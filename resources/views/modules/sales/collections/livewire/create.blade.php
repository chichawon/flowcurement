<div class="space-y-5">
    @if (! $show_collection_details)
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
                    <div>
                        <p class="text-xs font-semibold uppercase text-slate-500">Agent</p>
                        <p class="mt-1 text-slate-700">{{ $selectedClient->agent_name ?: '-' }}</p>
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

            <div class="relative overflow-x-auto rounded-lg border border-slate-200">
                <table class="min-w-[1320px] w-full table-fixed divide-y divide-slate-200 text-sm">
                    <colgroup>
                        <col class="w-14">
                        <col class="w-40">
                        <col class="w-40">
                        <col class="w-36">
                        <col class="w-32">
                        <col class="w-32">
                        <col class="w-44">
                        <col class="w-36">
                        <col class="w-36">
                        <col class="w-36">
                    </colgroup>
                    <thead class="bg-slate-50 text-xs font-semibold uppercase text-slate-500">
                        <tr>
                            <th class="px-3 py-3 text-center"></th>
                            <th class="px-3 py-3 text-left">Invoice No</th>
                            <th class="px-3 py-3 text-left">P.O No</th>
                            <th class="px-3 py-3 text-center">Invoice Date</th>
                            <th class="px-3 py-3 text-right">Subtotal</th>
                            <th class="px-3 py-3 text-right">Tax Amount</th>
                            <th class="px-3 py-3 text-right">Total Invoice Amount</th>
                            <th class="px-3 py-3 text-right">Withholding Tax</th>
                            <th class="px-3 py-3 text-right">Balance</th>
                            <th class="px-3 py-3 text-right">Total Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @forelse ($invoices as $invoice)
                            @php
                                $netTotal = max((float) $invoice->total_amount - (float) $invoice->withholding_tax_amount, 0);
                            @endphp
                            <tr>
                                <td class="px-3 py-3 text-center align-middle">
                                    <input type="checkbox" wire:model.live="selected_invoice_ids" value="{{ $invoice->id }}" class="rounded border-slate-300 text-cyan-700 shadow-sm erp-focus-ring">
                                </td>
                                <td class="px-3 py-3 align-middle">
                                    <p class="font-semibold text-slate-950">{{ $invoice->sales_invoice_no }}</p>
                                </td>
                                <td class="px-3 py-3 align-middle text-slate-700">{{ $invoice->customer_po ?: 'None' }}</td>
                                <td class="px-3 py-3 text-center align-middle text-slate-700">{{ $invoice->invoice_date?->format('M d, Y') }}</td>
                                <td class="px-3 py-3 text-right align-middle text-slate-700">{{ number_format((float) $invoice->subtotal, 2) }}</td>
                                <td class="px-3 py-3 text-right align-middle text-slate-700">{{ number_format((float) $invoice->tax_amount, 2) }}</td>
                                <td class="px-3 py-3 text-right align-middle font-semibold text-slate-950">{{ number_format((float) $invoice->total_amount, 2) }}</td>
                                <td class="px-3 py-3 text-right align-middle text-slate-700">{{ number_format((float) $invoice->withholding_tax_amount, 2) }}</td>
                                <td class="px-3 py-3 text-right align-middle font-semibold text-slate-950">{{ number_format((float) $invoice->balance_amount, 2) }}</td>
                                <td class="px-3 py-3 text-right align-middle font-semibold text-slate-950">{{ number_format($netTotal, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-4 py-10 text-center text-sm text-slate-500">
                                    {{ $business_partner_id ? 'No unpaid sales invoices found for this company.' : 'Select a company to show unpaid sales invoices.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @error('selected_invoice_ids') <p class="text-sm font-semibold text-red-600">{{ $message }}</p> @enderror
        </div>
    </section>

    <div class="sticky bottom-0 flex items-center justify-end gap-2 border-t border-slate-200 bg-slate-100/95 py-4 backdrop-blur">
        <a href="{{ route('sales.collections.index') }}" class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancel</a>
        <button
            type="button"
            wire:click="openCollectionDetails"
            @disabled($selectedInvoices->isEmpty())
            class="inline-flex items-center justify-center rounded-md bg-slate-950 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-slate-800"
        >
            Collect
        </button>
    </div>
    @else
        @php
            $selectedClient = $clients->firstWhere('id', (int) $business_partner_id);
            $selectedTotalAmount = $selectedInvoices->sum(fn ($invoice): float => (float) $invoice->balance_amount);
        @endphp

        <div class="grid gap-5 xl:grid-cols-3">
            <section class="erp-panel">
                <div class="erp-panel-header">
                    <h3 class="text-base font-semibold text-slate-950">Company Details</h3>
                </div>
                <div class="erp-panel-body space-y-4">
                    <div>
                        <label class="text-sm font-medium text-slate-700">Company Name</label>
                        <input type="text" value="{{ $selectedClient?->company_name ?: '-' }}" readonly class="mt-1 block h-10 w-full rounded-md border-slate-200 bg-slate-100 px-3 text-sm text-slate-700 shadow-sm">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-700">Agent Name</label>
                        <input type="text" value="{{ $selectedClient?->agent_name ?: '-' }}" readonly class="mt-1 block h-10 w-full rounded-md border-slate-200 bg-slate-100 px-3 text-sm text-slate-700 shadow-sm">
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-700">Contact Person</label>
                        <input type="text" value="{{ $selectedClient?->contact_person ?: '-' }}" readonly class="mt-1 block h-10 w-full rounded-md border-slate-200 bg-slate-100 px-3 text-sm text-slate-700 shadow-sm">
                    </div>
                </div>
            </section>

            <section class="erp-panel">
                <div class="erp-panel-header">
                    <h3 class="text-base font-semibold text-slate-950">Bank Details</h3>
                </div>
                <div class="erp-panel-body space-y-4">
                    <div>
                        <label class="text-sm font-medium text-slate-700">Bank Name</label>
                        <input type="text" wire:model="bank_name" class="mt-1 block h-10 w-full rounded-md border-slate-300 px-3 text-sm shadow-sm erp-focus-ring">
                        @error('bank_name') <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-700">Check Number</label>
                        <input type="text" wire:model="check_number" class="mt-1 block h-10 w-full rounded-md border-slate-300 px-3 text-sm shadow-sm erp-focus-ring">
                        @error('check_number') <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-700">Check Date</label>
                        <input type="date" wire:model="check_date" class="mt-1 block h-10 w-full rounded-md border-slate-300 px-3 text-sm shadow-sm erp-focus-ring">
                        @error('check_date') <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-700">Check Amount</label>
                        <input type="number" step="0.01" min="0" wire:model="check_amount" class="mt-1 block h-10 w-full rounded-md border-slate-300 px-3 text-right text-sm shadow-sm erp-focus-ring">
                        @error('check_amount') <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
                    </div>
                </div>
            </section>

            <section class="erp-panel">
                <div class="erp-panel-header">
                    <h3 class="text-base font-semibold text-slate-950">Payment Details</h3>
                </div>
                <div class="erp-panel-body space-y-4">
                    <div>
                        <label class="text-sm font-medium text-slate-700">Collection Receipt No.</label>
                        <input type="text" wire:model="collection_receipt_no" class="mt-1 block h-10 w-full rounded-md border-slate-300 px-3 text-sm shadow-sm erp-focus-ring">
                        @error('collection_receipt_no') <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-700">Collection Receipt Date</label>
                        <input type="date" wire:model="collection_receipt_date" class="mt-1 block h-10 w-full rounded-md border-slate-300 px-3 text-sm shadow-sm erp-focus-ring">
                        @error('collection_receipt_date') <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-700">Collection Receipt Amount</label>
                        <input type="number" step="0.01" min="0" wire:model="collection_receipt_amount" class="mt-1 block h-10 w-full rounded-md border-slate-300 px-3 text-right text-sm shadow-sm erp-focus-ring">
                        @error('collection_receipt_amount') <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
                    </div>
                </div>
            </section>
        </div>

        <section class="erp-panel">
            <div class="erp-panel-header flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h3 class="text-base font-semibold text-slate-950">Selected Sales Invoices</h3>
                    <p class="mt-1 text-sm text-slate-500">Remove any invoice that was selected by mistake before saving the collection.</p>
                </div>
                <div class="rounded-md bg-slate-950 px-4 py-2 text-sm font-semibold text-white">
                    Total Amount: {{ number_format((float) $selectedTotalAmount, 2) }}
                </div>
            </div>
            <div class="erp-panel-body">
                <div class="overflow-x-auto rounded-lg border border-slate-200">
                    <table class="min-w-[980px] w-full table-fixed divide-y divide-slate-200 text-sm">
                        <colgroup>
                            <col class="w-16">
                            <col class="w-44">
                            <col class="w-36">
                            <col class="w-36">
                            <col class="w-44">
                            <col class="w-40">
                            <col class="w-36">
                        </colgroup>
                        <thead class="bg-slate-50 text-xs font-semibold uppercase text-slate-500">
                            <tr>
                                <th class="px-3 py-3 text-center">Action</th>
                                <th class="px-3 py-3 text-left">Sales Invoice No</th>
                                <th class="px-3 py-3 text-right">Subtotal</th>
                                <th class="px-3 py-3 text-right">Tax Amount</th>
                                <th class="px-3 py-3 text-right">Total Invoice Amount</th>
                                <th class="px-3 py-3 text-right">Withholding Tax</th>
                                <th class="px-3 py-3 text-right">Balance</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @foreach ($selectedInvoices as $invoice)
                                <tr>
                                    <td class="px-3 py-3 text-center align-middle">
                                        <button type="button" wire:click="removeSelectedInvoice({{ $invoice->id }})" class="inline-flex h-8 w-8 items-center justify-center rounded-md bg-red-600 text-white hover:bg-red-700" title="Remove invoice">
                                            <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 0 1 1.414 0L10 8.586l4.293-4.293a1 1 0 1 1 1.414 1.414L11.414 10l4.293 4.293a1 1 0 0 1-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 0 1-1.414-1.414L8.586 10 4.293 5.707a1 1 0 0 1 0-1.414Z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </td>
                                    <td class="px-3 py-3 align-middle">
                                        <p class="font-semibold text-slate-950">{{ $invoice->sales_invoice_no }}</p>
                                        <p class="mt-0.5 text-xs text-slate-500">{{ $invoice->customer_po ?: 'No customer P.O' }}</p>
                                    </td>
                                    <td class="px-3 py-3 text-right align-middle text-slate-700">{{ number_format((float) $invoice->subtotal, 2) }}</td>
                                    <td class="px-3 py-3 text-right align-middle text-slate-700">{{ number_format((float) $invoice->tax_amount, 2) }}</td>
                                    <td class="px-3 py-3 text-right align-middle font-semibold text-slate-950">{{ number_format((float) $invoice->total_amount, 2) }}</td>
                                    <td class="px-3 py-3 text-right align-middle text-slate-700">{{ number_format((float) $invoice->withholding_tax_amount, 2) }}</td>
                                    <td class="px-3 py-3 text-right align-middle font-semibold text-slate-950">{{ number_format((float) $invoice->balance_amount, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="border-t border-slate-300 bg-slate-950 text-sm font-semibold text-white">
                            <tr>
                                <td colspan="6" class="px-3 py-4 text-left uppercase">Total Amount</td>
                                <td class="px-3 py-4 text-right">{{ number_format((float) $selectedTotalAmount, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </section>

        <div class="sticky bottom-0 flex items-center justify-end gap-2 border-t border-slate-200 bg-slate-100/95 py-4 backdrop-blur">
            <button type="button" wire:click="backToInvoiceSelection" class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Back</button>
            <button type="button" wire:click="save" wire:loading.attr="disabled" wire:target="save" class="inline-flex items-center justify-center rounded-md bg-slate-950 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-slate-800">
                <span wire:loading.remove wire:target="save">Save Collection</span>
                <span wire:loading wire:target="save">Saving...</span>
            </button>
        </div>
    @endif
</div>
