<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-cyan-700">Items</p>
            <h2 class="text-2xl font-semibold text-slate-950">Edit Item</h2>
        </div>
    </x-slot>

    @livewire('items.edit', ['item' => $item])
</x-app-layout>
