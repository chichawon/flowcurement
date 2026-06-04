<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-cyan-700">Quotations</p>
            <h2 class="text-2xl font-semibold text-slate-950">{{ $quotation->quotation_no }}</h2>
        </div>
    </x-slot>

    <div class="space-y-5">
        <div class="grid gap-5 xl:grid-cols-[minmax(0,1fr)_22rem]">
            <section class="erp-panel">
                <div class="erp-panel-header">
                    <h3 class="text-sm font-semibold text-slate-950">Client Information</h3>
                </div>
                <dl class="erp-panel-body grid gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-xs font-semibold uppercase text-slate-500">Company</dt>
                        <dd class="mt-1 text-sm font-semibold text-slate-950">{{ $quotation->businessPartner?->company_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase text-slate-500">Agent</dt>
                        <dd class="mt-1 text-sm text-slate-700">{{ $quotation->agent_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase text-slate-500">Contact Person</dt>
                        <dd class="mt-1 text-sm text-slate-700">{{ $quotation->contact_person }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase text-slate-500">Contact No.</dt>
                        <dd class="mt-1 text-sm text-slate-700">{{ $quotation->contact_no }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-xs font-semibold uppercase text-slate-500">Address</dt>
                        <dd class="mt-1 text-sm text-slate-700">{{ $quotation->company_address ?: 'No address provided' }}</dd>
                    </div>
                </dl>
            </section>

            <section class="erp-panel">
                <div class="erp-panel-header">
                    <h3 class="text-sm font-semibold text-slate-950">Quotation Summary</h3>
                </div>
                <dl class="erp-panel-body space-y-3">
                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-sm text-slate-500">Date</dt>
                        <dd class="text-sm font-semibold text-slate-950">{{ $quotation->quotation_date?->format('M d, Y') }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-sm text-slate-500">Validity</dt>
                        <dd class="text-sm font-semibold text-slate-950">{{ $quotation->validity_date?->format('M d, Y') }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-sm text-slate-500">Prepared By</dt>
                        <dd class="truncate text-sm font-semibold text-slate-950">{{ $quotation->preparedBy?->name ?? 'System' }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-sm text-slate-500">Currency</dt>
                        <dd class="text-sm font-semibold uppercase text-slate-950">{{ $quotation->currency }}</dd>
                    </div>
                    <div class="border-t border-slate-200 pt-3">
                        <div class="flex items-center justify-between gap-3 text-sm">
                            <dt class="text-slate-500">Subtotal</dt>
                            <dd class="font-semibold text-slate-950">{{ number_format((float) $quotation->subtotal, 2) }}</dd>
                        </div>
                        <div class="mt-2 flex items-center justify-between gap-3 text-sm">
                            <dt class="text-slate-500">Tax {{ number_format((float) $quotation->tax_rate, 0) }}%</dt>
                            <dd class="font-semibold text-slate-950">{{ number_format((float) $quotation->tax_amount, 2) }}</dd>
                        </div>
                        <div class="mt-3 flex items-center justify-between gap-3 border-t border-slate-200 pt-3">
                            <dt class="text-sm font-semibold text-slate-700">Total</dt>
                            <dd class="text-xl font-bold text-slate-950">{{ number_format((float) $quotation->total_amount, 2) }}</dd>
                        </div>
                    </div>
                </dl>
            </section>
        </div>

        <section class="erp-panel" x-data="{ imagePreviewOpen: false, imagePreviewUrl: '', imagePreviewTitle: '' }">
            <div class="erp-panel-header">
                <h3 class="text-sm font-semibold text-slate-950">Items</h3>
            </div>
            <div class="erp-panel-body">
                <div class="overflow-hidden rounded-lg border border-slate-200">
                    <table class="w-full table-fixed divide-y divide-slate-200 text-sm">
                        <colgroup>
                            <col class="w-[22%]">
                            <col class="w-[26%]">
                            <col class="w-[12%]">
                            <col class="w-[12%]">
                            <col class="w-[12%]">
                            <col class="w-[10%]">
                            <col class="w-[12%]">
                        </colgroup>
                        <thead class="bg-slate-50 text-xs font-semibold uppercase text-slate-500">
                            <tr>
                                <th class="px-3 py-3 text-left">Item</th>
                                <th class="px-3 py-3 text-left">Description</th>
                                <th class="px-3 py-3 text-left">Lead Time</th>
                                <th class="px-3 py-3 text-left">Unit</th>
                                <th class="px-3 py-3 text-right">Price</th>
                                <th class="px-3 py-3 text-center">Qty</th>
                                <th class="px-3 py-3 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @foreach ($quotation->items as $row)
                                @php
                                    $itemImageUrl = $row->item?->item_image ? \Illuminate\Support\Facades\Storage::disk('public')->url($row->item->item_image) : null;
                                    $itemName = $row->item?->item_name ?? 'Item';
                                @endphp
                                <tr>
                                    <td class="px-3 py-3 align-middle">
                                        <div class="flex items-center gap-2">
                                            @if ($itemImageUrl)
                                                <button type="button" class="size-10 shrink-0 overflow-hidden rounded-md border border-slate-200 bg-white" @click="imagePreviewUrl = @js($itemImageUrl); imagePreviewTitle = @js($itemName); imagePreviewOpen = true">
                                                    <img src="{{ $itemImageUrl }}" alt="{{ $itemName }}" class="h-full w-full object-cover">
                                                </button>
                                            @else
                                                <span class="grid size-10 shrink-0 place-items-center rounded-md border border-slate-200 bg-slate-100 text-xs font-bold text-slate-500">{{ strtoupper(substr($itemName, 0, 1)) }}</span>
                                            @endif
                                            <p class="truncate font-semibold text-slate-950">{{ $itemName }}</p>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3 text-slate-700">{{ $row->description }}</td>
                                    <td class="px-3 py-3 text-slate-700">{{ $row->lead_time ?: '-' }}</td>
                                    <td class="px-3 py-3 text-slate-700">{{ str($row->unitMeasure?->name)->headline() }}</td>
                                    <td class="px-3 py-3 text-right font-semibold text-slate-950">{{ number_format((float) $row->item_price, 2) }}</td>
                                    <td class="px-3 py-3 text-center text-slate-700">{{ number_format((float) $row->quantity, 0) }}</td>
                                    <td class="px-3 py-3 text-right font-semibold text-slate-950">{{ number_format((float) $row->total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div x-show="imagePreviewOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 px-4">
                <div class="w-full max-w-2xl overflow-hidden rounded-lg bg-white shadow-2xl">
                    <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3">
                        <h4 class="text-sm font-semibold text-slate-950" x-text="imagePreviewTitle || 'Item Image'"></h4>
                        <button type="button" @click="imagePreviewOpen = false" class="rounded-md px-2 py-1 text-sm font-semibold text-slate-500 hover:bg-slate-100">Close</button>
                    </div>
                    <div class="bg-slate-50 p-4">
                        <img :src="imagePreviewUrl" alt="Item preview" class="mx-auto max-h-[32rem] max-w-full rounded-md object-contain">
                    </div>
                </div>
            </div>
        </section>

        @if ($quotation->remarks)
            <section class="erp-panel">
                <div class="erp-panel-body whitespace-pre-line text-sm text-slate-700">{{ $quotation->remarks }}</div>
            </section>
        @endif
    </div>

    <div class="sticky bottom-0 mt-5 flex items-center justify-end gap-2 border-t border-slate-200 bg-slate-100/95 py-4 backdrop-blur">
        <a href="{{ route('quotations.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Back</a>
        @can('print', $quotation)
            <a href="{{ route('quotations.print', $quotation) }}" target="_blank" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Print</a>
        @endcan
        @can('update', $quotation)
            <a href="{{ route('quotations.edit', $quotation) }}" class="rounded-md bg-slate-950 px-4 py-2 text-sm font-semibold text-white shadow-sm erp-focus-ring hover:bg-slate-800">Edit</a>
        @endcan
    </div>
</x-app-layout>
