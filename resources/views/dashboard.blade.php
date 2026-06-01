<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-medium text-cyan-700">Operations overview</p>
                <h2 class="text-2xl font-semibold text-slate-950">Dashboard</h2>
            </div>
            <p class="text-sm text-slate-500">Signed in as {{ auth()->user()->email }}</p>
        </div>
    </x-slot>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <x-admin.stat-card label="Open Requests" value="0" meta="Setup" tone="cyan" />
        <x-admin.stat-card label="Pending Approvals" value="0" meta="Ready" tone="amber" />
        <x-admin.stat-card label="Active Suppliers" value="0" meta="Phase 2" tone="emerald" />
        <x-admin.stat-card label="Inventory Alerts" value="0" meta="Phase 2" tone="rose" />
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-3">
        <section class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm xl:col-span-2">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h3 class="text-lg font-semibold text-slate-950">Foundation Status</h3>
                    <p class="mt-1 text-sm text-slate-500">Core ERP architecture is ready for module implementation.</p>
                </div>
            </div>

            <div class="mt-6 grid gap-3 sm:grid-cols-2">
                @foreach (['Authentication', 'Livewire', 'Tailwind CSS', 'Role Permissions', 'Module Routes', 'Admin Layout'] as $item)
                    <div class="flex items-center gap-3 rounded-lg border border-slate-200 px-4 py-3">
                        <span class="grid size-8 place-items-center rounded-md bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100">
                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                        </span>
                        <span class="text-sm font-medium text-slate-700">{{ $item }}</span>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="text-lg font-semibold text-slate-950">Module Shells</h3>
            <div class="mt-4 space-y-3">
                @foreach (['User Management', 'Business Partners', 'Items', 'Quotations', 'Sales', 'Purchasing', 'Inventory', 'Reports'] as $module)
                    <div class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2">
                        <span class="text-sm font-medium text-slate-700">{{ $module }}</span>
                        <span class="text-xs font-semibold text-slate-400">Ready</span>
                    </div>
                @endforeach
            </div>
        </section>
    </div>
</x-app-layout>
