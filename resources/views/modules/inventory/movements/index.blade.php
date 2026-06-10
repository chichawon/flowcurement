<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-cyan-700">Inventory</p>
            <h2 class="text-2xl font-semibold text-slate-950">Stock Movements</h2>
        </div>
    </x-slot>

    <livewire:inventory.movements-index />
</x-app-layout>
