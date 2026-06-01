<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-cyan-700">Delivery Receipts</p>
            <h2 class="text-2xl font-semibold text-slate-950">Edit Delivery Receipt</h2>
        </div>
    </x-slot>

    <livewire:sales.delivery-receipts.edit :delivery-receipt="$deliveryReceipt" />
</x-app-layout>

