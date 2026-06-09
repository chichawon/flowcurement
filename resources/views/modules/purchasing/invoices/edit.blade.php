<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-cyan-700">Purchase Invoices</p>
            <h2 class="text-2xl font-semibold text-slate-950">Edit Purchase Invoice</h2>
        </div>
    </x-slot>

    <livewire:purchasing.invoices.edit :purchase-invoice="$purchaseInvoice" />
</x-app-layout>
