<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-cyan-700">Clients</p>
            <h2 class="text-2xl font-semibold text-slate-950">Edit Client</h2>
        </div>
    </x-slot>

    @livewire('business-partners.client-edit', ['businessPartner' => $businessPartner])
</x-app-layout>
