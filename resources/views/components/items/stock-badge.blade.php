@props(['item'])

@if ($item->available_stock <= 0)
    <span class="inline-flex rounded-full bg-red-600 px-2.5 py-1 text-xs font-semibold text-white">Out Of Stock</span>
@elseif ($item->isLowStock())
    <span class="inline-flex rounded-full bg-amber-500 px-2.5 py-1 text-xs font-semibold text-white">Low Stock</span>
@else
    <span class="inline-flex rounded-full bg-emerald-600 px-2.5 py-1 text-xs font-semibold text-white">In Stock</span>
@endif
