<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-medium text-cyan-700">Administration</p>
                <h2 class="text-2xl font-semibold text-slate-950">User Management</h2>
            </div>
        </div>
    </x-slot>

    @livewire('user-management.users-index')
</x-app-layout>
