<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-cyan-700">Business Partners</p>
            <h2 class="text-2xl font-semibold text-slate-950">Suppliers</h2>
        </div>
    </x-slot>

    @livewire('business-partners.supplier-index')
</x-app-layout>
