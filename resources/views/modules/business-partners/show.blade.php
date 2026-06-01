<x-app-layout>
    <x-slot name="header">
        <div>
            <div>
                <p class="text-sm font-medium text-cyan-700">{{ $title }}</p>
                <h2 class="text-2xl font-semibold text-slate-950">{{ $businessPartner->company_name }}</h2>
            </div>
        </div>
    </x-slot>

    @if (session('status'))
        <div class="mb-5 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
            {{ session('status') }}
        </div>
    @endif

    <div class="grid gap-5 xl:grid-cols-[minmax(0,1fr)_22rem]">
        <section class="erp-panel">
            <div class="erp-panel-header">
                <h3 class="text-sm font-semibold text-slate-950">Company Information</h3>
            </div>
            <dl class="erp-panel-body grid gap-4 sm:grid-cols-2">
                <div>
                    <dt class="text-xs font-semibold uppercase text-slate-500">Company Code</dt>
                    <dd class="mt-1 text-sm font-semibold text-slate-950">{{ $businessPartner->company_code }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase text-slate-500">TIN Number</dt>
                    <dd class="mt-1 text-sm text-slate-700">{{ $businessPartner->tin_number }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase text-slate-500">Contact Person</dt>
                    <dd class="mt-1 text-sm text-slate-700">{{ $businessPartner->contact_person }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase text-slate-500">Contact No.</dt>
                    <dd class="mt-1 text-sm text-slate-700">{{ $businessPartner->contact_no }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-xs font-semibold uppercase text-slate-500">Company Address</dt>
                    <dd class="mt-1 whitespace-pre-line text-sm text-slate-700">{{ $businessPartner->company_address ?: 'No address provided.' }}</dd>
                </div>
            </dl>
        </section>

        <section class="erp-panel">
            <div class="erp-panel-header">
                <h3 class="text-sm font-semibold text-slate-950">Commercial Settings</h3>
            </div>
            <dl class="erp-panel-body space-y-4">
                <div class="flex items-center justify-between gap-3">
                    <dt class="text-sm text-slate-500">Status</dt>
                    <dd><x-business-partners.status-badge :status="$businessPartner->status" /></dd>
                </div>
                <div class="flex items-center justify-between gap-3">
                    <dt class="text-sm text-slate-500">VAT</dt>
                    <dd><x-business-partners.value-badge :value="$businessPartner->vatable" tone="cyan" /></dd>
                </div>
                <div class="flex items-center justify-between gap-3">
                    <dt class="text-sm text-slate-500">Under PESA</dt>
                    <dd><x-business-partners.value-badge :value="$businessPartner->under_pesa" tone="amber" /></dd>
                </div>
                <div class="flex items-center justify-between gap-3">
                    <dt class="text-sm text-slate-500">Terms</dt>
                    <dd class="text-sm font-semibold text-slate-950">{{ $businessPartner->terms }} days</dd>
                </div>
                <div class="flex items-center justify-between gap-3">
                    <dt class="text-sm text-slate-500">Credit Limit</dt>
                    <dd class="text-sm font-semibold text-slate-950">{{ number_format((float) $businessPartner->credit_limit, 2) }}</dd>
                </div>
                <div class="border-t border-slate-200 pt-4 text-xs text-slate-500">
                    <p>Created by {{ $businessPartner->creator?->name ?? 'System' }}</p>
                    <p class="mt-1">Updated by {{ $businessPartner->updater?->name ?? 'System' }}</p>
                </div>
            </dl>
        </section>
    </div>

    <div class="sticky bottom-0 mt-5 flex items-center justify-end gap-2 border-t border-slate-200 bg-slate-100/95 py-4 backdrop-blur">
        <a href="{{ route($routePrefix.'.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Back</a>
        @can('update', $businessPartner)
            <a href="{{ route($routePrefix.'.edit', $businessPartner) }}" class="rounded-md bg-slate-950 px-4 py-2 text-sm font-semibold text-white shadow-sm erp-focus-ring hover:bg-slate-800">Edit</a>
        @endcan
    </div>
</x-app-layout>
