<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-cyan-700">Quotations</p>
            <h2 class="text-2xl font-semibold text-slate-950">Edit Quotation</h2>
        </div>
    </x-slot>

    @livewire('quotations.edit', ['quotation' => $quotation])
</x-app-layout>
