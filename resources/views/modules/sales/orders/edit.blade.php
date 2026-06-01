<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-cyan-700">Sales Orders</p>
            <h2 class="text-2xl font-semibold text-slate-950">Edit Sales Order</h2>
        </div>
    </x-slot>

    <livewire:sales.orders.edit :sales-order="$salesOrder" />
</x-app-layout>
