<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-cyan-700">Inventory Master</p>
            <h2 class="text-2xl font-semibold text-slate-950">Items</h2>
        </div>
    </x-slot>

    @livewire('items.index')
</x-app-layout>
