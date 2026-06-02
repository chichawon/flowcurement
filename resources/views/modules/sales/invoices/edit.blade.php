<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-cyan-700">Sales Invoice</p>
            <h2 class="text-2xl font-semibold text-slate-950">Edit Sales Invoice</h2>
        </div>
    </x-slot>

    <livewire:sales.invoices.edit :sales-invoice="$salesInvoice" />
</x-app-layout>
