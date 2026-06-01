<form wire:submit.prevent="save" class="space-y-5">
    <section class="erp-panel">
        <div class="erp-panel-header">
            <h3 class="text-sm font-semibold text-slate-950">{{ $title }}</h3>
        </div>
        <div class="erp-panel-body space-y-4">
            <div class="grid gap-3 lg:grid-cols-4">
                <label class="block">
                    <span class="text-sm font-medium text-slate-700">Delivery Receipt No.</span>
                    <input type="text" value="{{ $delivery_receipt_no }}" readonly class="mt-1 block h-10 w-full rounded-md border-slate-200 bg-slate-100 px-3 text-sm font-semibold text-slate-950 shadow-sm">
                </label>
                <label class="block">
                    <span class="text-sm font-medium text-slate-700">DR Date</span>
                    <input type="date" wire:model.live="dr_date" class="mt-1 block h-10 w-full rounded-md border-slate-300 px-3 text-sm shadow-sm erp-focus-ring">
                    @error('dr_date') <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
                </label>
                <label class="block lg:col-span-2">
                    <span class="text-sm font-medium text-slate-700">Sales Order</span>
                    <select wire:model.live="sales_order_id" class="mt-1 block h-10 w-full rounded-md border-slate-300 px-3 text-sm shadow-sm erp-focus-ring" @disabled($deliveryReceiptRecord !== null)>
                        <option value="">Select sales order</option>
                        @foreach ($salesOrders as $so)
                            <option value="{{ $so->id }}">{{ $so->sales_order_no }} - {{ $so->businessPartner?->company_name }}</option>
                        @endforeach
                    </select>
                    @error('sales_order_id') <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
                </label>
            </div>

            <div class="grid gap-3 lg:grid-cols-4">
                <label class="block"><span class="text-sm font-medium text-slate-700">Sales Order No.</span><input type="text" value="{{ $sales_order_no }}" readonly class="mt-1 block h-10 w-full rounded-md border-slate-200 bg-slate-100 px-3 text-sm text-slate-700 shadow-sm"></label>
                <label class="block"><span class="text-sm font-medium text-slate-700">Customer PO</span><input type="text" value="{{ $customer_po }}" readonly class="mt-1 block h-10 w-full rounded-md border-slate-200 bg-slate-100 px-3 text-sm text-slate-700 shadow-sm"></label>
                <label class="block"><span class="text-sm font-medium text-slate-700">Agent Name</span><input type="text" value="{{ $agent_name }}" readonly class="mt-1 block h-10 w-full rounded-md border-slate-200 bg-slate-100 px-3 text-sm text-slate-700 shadow-sm"></label>
                <label class="block"><span class="text-sm font-medium text-slate-700">Company</span><input type="text" value="{{ $company_name }}" readonly class="mt-1 block h-10 w-full rounded-md border-slate-200 bg-slate-100 px-3 text-sm text-slate-700 shadow-sm"></label>
            </div>
            <div class="grid gap-3 lg:grid-cols-4">
                <label class="block"><span class="text-sm font-medium text-slate-700">Terms</span><input type="text" value="{{ $terms }} days" readonly class="mt-1 block h-10 w-full rounded-md border-slate-200 bg-slate-100 px-3 text-sm text-slate-700 shadow-sm"></label>
                <label class="block lg:col-span-2"><span class="text-sm font-medium text-slate-700">Company Address</span><input type="text" value="{{ $company_address }}" readonly class="mt-1 block h-10 w-full rounded-md border-slate-200 bg-slate-100 px-3 text-sm text-slate-700 shadow-sm"></label>
                <label class="block"><span class="text-sm font-medium text-slate-700">Contact</span><input type="text" value="{{ $contact_person }}{{ $contact_no ? ' | '.$contact_no : '' }}" readonly class="mt-1 block h-10 w-full rounded-md border-slate-200 bg-slate-100 px-3 text-sm text-slate-700 shadow-sm"></label>
            </div>
        </div>
    </section>

    <section class="erp-panel">
        <div class="erp-panel-header">
            <h3 class="text-sm font-semibold text-slate-950">Items</h3>
        </div>
        <div class="erp-panel-body space-y-3">
            @if ($notice)
                <div class="rounded-md border border-red-300 bg-red-50 px-3 py-2 text-sm text-red-800">
                    <span class="font-extrabold text-red-700">*Notice:</span>
                    <span class="font-semibold">{{ $notice }}</span>
                </div>
            @endif
            <div class="overflow-x-auto border border-slate-400 bg-white">
                <table class="min-w-[760px] w-full table-fixed border-collapse text-xs">
                    <thead class="bg-slate-200 uppercase text-slate-700">
                        <tr>
                            <th class="border border-slate-400 px-2 py-2 text-right">Quantity</th>
                            <th class="border border-slate-400 px-2 py-2 text-left">Unit</th>
                            <th class="border border-slate-400 px-2 py-2 text-left">Item Name</th>
                            <th class="border border-slate-400 px-2 py-2 text-left">Stock Availability</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @forelse ($items as $index => $row)
                            <tr>
                                <td class="border border-slate-300 px-2 py-2 text-right align-top">
                                    <input type="number" min="0" step="1" wire:model.live.debounce.250ms="items.{{ $index }}.delivered_quantity" class="h-8 w-24 border border-slate-400 bg-white px-2 text-right text-xs shadow-sm erp-focus-ring" @disabled($deliveryReceiptRecord !== null)>
                                </td>
                                <td class="border border-slate-300 px-2 py-2 align-top">{{ str($row['unit_measure_name'])->headline() }}</td>
                                <td class="border border-slate-300 px-2 py-2 align-top font-medium text-slate-900">{{ $row['item_name'] }}</td>
                                <td class="border border-slate-300 px-2 py-2 align-top">
                                    @if ($row['stock_status'] === 'no_stock')
                                        <span class="rounded-full bg-red-600 px-2 py-0.5 text-[11px] font-semibold text-white">No stock available</span>
                                    @elseif ($row['stock_status'] === 'partial_stock')
                                        <span class="rounded-full bg-amber-500 px-2 py-0.5 text-[11px] font-semibold text-white">Partial stock available</span>
                                    @else
                                        <span class="rounded-full bg-emerald-600 px-2 py-0.5 text-[11px] font-semibold text-white">Available</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="border border-slate-300 px-3 py-5 text-center text-sm text-slate-500">Select a sales order to load items.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @error('items') <span class="block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
        </div>
    </section>

    <div class="sticky bottom-0 flex items-center justify-end gap-2 border-t border-slate-200 bg-slate-100/95 py-4 backdrop-blur">
        <a href="{{ $cancelRoute }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancel</a>
        <button type="submit" class="rounded-md bg-slate-950 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-slate-800">{{ $submitLabel }}</button>
    </div>
</form>
