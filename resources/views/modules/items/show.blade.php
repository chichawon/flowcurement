<x-app-layout>
    <x-slot name="header">
        <div>
            <div>
                <p class="text-sm font-medium text-cyan-700">{{ $title ?? 'Items' }}</p>
                <h2 class="text-2xl font-semibold text-slate-950">{{ $item->item_name }}</h2>
            </div>
        </div>
    </x-slot>

    <div class="grid gap-5 xl:grid-cols-[minmax(0,1fr)_24rem]">
        <section class="erp-panel">
            <div class="erp-panel-header">
                <h3 class="text-sm font-semibold text-slate-950">Item Information</h3>
            </div>
            <dl class="erp-panel-body grid gap-4 sm:grid-cols-2">
                <div>
                    <dt class="text-xs font-semibold uppercase text-slate-500">Item Code</dt>
                    <dd class="mt-1 text-sm font-semibold text-slate-950">{{ $item->item_code }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase text-slate-500">Item Type</dt>
                    <dd class="mt-1 text-sm text-slate-700">{{ $item->item_type }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase text-slate-500">Supplier</dt>
                    <dd class="mt-1 text-sm text-slate-700">{{ $item->supplier?->company_name ?? 'No supplier' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase text-slate-500">Taxable</dt>
                    <dd class="mt-1 text-sm text-slate-700">{{ str($item->taxable)->headline() }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase text-slate-500">Supplier Price</dt>
                    <dd class="mt-1 text-sm font-semibold text-slate-950">{{ number_format((float) $item->supplier_price, 2) }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase text-slate-500">Markup</dt>
                    <dd class="mt-1 text-sm font-semibold text-slate-950">{{ number_format((float) $item->percentage, 2) }}%</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase text-slate-500">Computed Item Price</dt>
                    <dd class="mt-1 text-base font-semibold text-slate-950">{{ number_format((float) $item->item_price, 2) }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase text-slate-500">Status</dt>
                    <dd class="mt-1"><x-items.status-badge :status="$item->status" /></dd>
                </div>
            </dl>
        </section>

        <section class="erp-panel">
            <div class="erp-panel-header">
                <h3 class="text-sm font-semibold text-slate-950">Stock Monitoring</h3>
            </div>
            <div class="erp-panel-body space-y-4">
                <div class="overflow-hidden rounded-lg border border-slate-200 bg-slate-50">
                    @if ($item->item_image)
                        <img src="{{ \App\Modules\Items\Helpers\ItemImage::url($item->item_image) }}" alt="{{ $item->item_name }}" class="h-64 w-full bg-white object-contain">
                    @else
                        <div class="grid h-48 place-items-center text-sm text-slate-400">No image</div>
                    @endif
                </div>
                <div class="flex items-center justify-between gap-3">
                    <span class="text-sm text-slate-500">Stock</span>
                    <span class="text-sm font-semibold text-slate-950">{{ $item->available_stock }}</span>
                </div>
                <div class="flex items-center justify-between gap-3">
                    <span class="text-sm text-slate-500">Reorder Point</span>
                    <span class="text-sm font-semibold text-slate-950">{{ $item->reorder_point }}</span>
                </div>
                <x-items.stock-badge :item="$item" />
            </div>
        </section>
    </div>

    <div class="sticky bottom-0 mt-5 flex items-center justify-end gap-2 border-t border-slate-200 bg-slate-100/95 py-4 backdrop-blur">
        <a href="{{ route($routePrefix.'.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Back</a>
        @can('update', $item)
            <a href="{{ route($routePrefix.'.edit', $item) }}" class="rounded-md bg-slate-950 px-4 py-2 text-sm font-semibold text-white shadow-sm erp-focus-ring hover:bg-slate-800">Edit</a>
        @endcan
    </div>
</x-app-layout>
