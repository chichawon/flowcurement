<div class="space-y-5">
    <form wire:submit.prevent="save" class="space-y-5">
        <section class="erp-panel">
            <div class="erp-panel-header">
                <h3 class="text-base font-semibold text-slate-950">Adjustment Details</h3>
                <p class="mt-1 text-sm text-slate-500">Use this for physical count corrections and approved manual stock changes.</p>
            </div>
            <div class="erp-panel-body space-y-4">
                <div class="grid gap-4 lg:grid-cols-3">
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Adjustment Date</span>
                        <input type="date" wire:model.blur="adjustment_date" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm erp-focus-ring">
                        @error('adjustment_date') <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
                    </label>

                    <label class="block lg:col-span-2">
                        <span class="text-sm font-medium text-slate-700">Item</span>
                        <select wire:model.live="item_id" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm erp-focus-ring">
                            <option value="">Select item</option>
                            @foreach ($items as $item)
                                <option value="{{ $item->id }}">{{ $item->item_code }} - {{ $item->item_name }} (Stock: {{ (int) $item->available_stock }})</option>
                            @endforeach
                        </select>
                        @error('item_id') <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
                    </label>
                </div>

                <div class="grid gap-4 lg:grid-cols-3">
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Adjustment Type</span>
                        <select wire:model.live="adjustment_type" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm erp-focus-ring">
                            <option value="add">Add Stock</option>
                            <option value="deduct">Deduct Stock</option>
                        </select>
                        @error('adjustment_type') <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
                    </label>

                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Quantity</span>
                        <input type="number" wire:model.live="quantity" min="1" step="1" class="mt-1 block w-full rounded-md border-slate-300 text-right text-sm shadow-sm erp-focus-ring">
                        @error('quantity') <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
                    </label>

                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Reason</span>
                        <input type="text" wire:model.blur="reason" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm erp-focus-ring" placeholder="Physical count, correction, etc.">
                        @error('reason') <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
                    </label>
                </div>

                <label class="block">
                    <span class="text-sm font-medium text-slate-700">Remarks</span>
                    <textarea wire:model.blur="remarks" rows="4" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm erp-focus-ring" placeholder="Optional details"></textarea>
                    @error('remarks') <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
                </label>
            </div>
        </section>

        <section class="erp-panel">
            <div class="erp-panel-header">
                <h3 class="text-base font-semibold text-slate-950">Stock Preview</h3>
            </div>
            <div class="erp-panel-body">
                <table class="w-full table-fixed border border-slate-300 text-sm">
                    <thead class="bg-slate-100 text-xs font-semibold uppercase text-slate-600">
                        <tr>
                            <th class="border border-slate-300 px-3 py-3 text-left">Summary</th>
                            <th class="border border-slate-300 px-3 py-3 text-right">Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="border border-slate-300 px-3 py-3 font-semibold">Current Stock</td>
                            <td class="border border-slate-300 px-3 py-3 text-right">{{ number_format($current_stock) }}</td>
                        </tr>
                        <tr>
                            <td class="border border-slate-300 px-3 py-3 font-semibold">{{ $adjustment_type === 'deduct' ? 'Deduct Quantity' : 'Add Quantity' }}</td>
                            <td class="border border-slate-300 px-3 py-3 text-right">{{ number_format((int) $quantity) }}</td>
                        </tr>
                    </tbody>
                    <tfoot class="bg-slate-950 text-white">
                        <tr>
                            <td class="border border-slate-950 px-3 py-3 font-bold">New Stock</td>
                            <td class="border border-slate-950 px-3 py-3 text-right font-bold">{{ number_format($new_stock) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </section>

        <div class="sticky bottom-0 flex items-center justify-end gap-2 border-t border-slate-200 bg-slate-100/95 py-4 backdrop-blur">
            <a href="{{ route('inventory.stock.index') }}" class="rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancel</a>
            <button type="submit" class="rounded-md bg-slate-950 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-slate-800">
                Save Adjustment
            </button>
        </div>
    </form>
</div>
