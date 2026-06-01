@php
    $navigation = [
        ['label' => 'Dashboard', 'permission' => 'dashboard.view', 'url' => route('dashboard'), 'active' => request()->routeIs('dashboard'), 'icon' => 'M3 13.5V6a1.5 1.5 0 0 1 1.5-1.5h2.25A1.5 1.5 0 0 1 8.25 6v7.5A1.5 1.5 0 0 1 6.75 15H4.5A1.5 1.5 0 0 1 3 13.5Zm7.5 0V10.5A1.5 1.5 0 0 1 12 9h2.25a1.5 1.5 0 0 1 1.5 1.5v3a1.5 1.5 0 0 1-1.5 1.5H12a1.5 1.5 0 0 1-1.5-1.5Zm7.5 0V4.5A1.5 1.5 0 0 1 19.5 3h2.25A1.5 1.5 0 0 1 23.25 4.5v9A1.5 1.5 0 0 1 21.75 15H19.5A1.5 1.5 0 0 1 18 13.5Z'],
        [
            'label' => 'Business Partners',
            'permission' => 'business-partners.view',
            'active' => request()->routeIs('clients.*') || request()->routeIs('suppliers.*'),
            'icon' => 'M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21',
            'children' => [
                ['label' => 'Clients', 'url' => route('clients.index'), 'active' => request()->routeIs('clients.*')],
                ['label' => 'Suppliers', 'url' => route('suppliers.index'), 'active' => request()->routeIs('suppliers.*')],
            ],
        ],
        
           [
            'label' => 'Items',
            'permission' => 'items.view',
            'active' => request()->routeIs('items.*') || request()->routeIs('local-items.*') || request()->routeIs('import-items.*'),
            'icon' => 'm20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z',
            'children' => [
                ['label' => 'Local Items', 'url' => route('local-items.index'), 'active' => request()->routeIs('local-items.*')],
                ['label' => 'Import Items', 'url' => route('import-items.index'), 'active' => request()->routeIs('import-items.*')],
            ],
        ],
        
        ['label' => 'Quotations', 'permission' => 'quotations.view', 'url' => route('quotations.index'), 'active' => request()->routeIs('quotations.*'), 'icon' => 'M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5A3.375 3.375 0 0 0 10.125 2.25H8.25m.75 12 2.25 2.25L15.75 12m-7.5 9h7.5A2.25 2.25 0 0 0 18 18.75V9.621a2.25 2.25 0 0 0-.659-1.591l-4.371-4.371A2.25 2.25 0 0 0 11.379 3H8.25A2.25 2.25 0 0 0 6 5.25v13.5A2.25 2.25 0 0 0 8.25 21Z'],

        [
            'label' => 'Sales',
            'permission' => 'sales-orders.view',
            'active' => request()->routeIs('sales.orders.*') || request()->routeIs('sales.delivery-receipts.*'),
            'icon' => 'M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.63-1.163m0 0-5.25-.875m5.25.875-.875 5.25',
            'children' => [
                ['label' => 'Order', 'url' => route('sales.orders.index'), 'active' => request()->routeIs('sales.orders.*')],
                ['label' => 'Delivery Receipt', 'url' => route('sales.delivery-receipts.index'), 'active' => request()->routeIs('sales.delivery-receipts.*')],
                ['label' => 'Invoice', 'url' => '#', 'active' => false],
                ['label' => 'Collection', 'url' => '#', 'active' => false],
            ],
        ],
     
        ['label' => 'Purchasing', 'permission' => 'purchasing.view', 'url' => '#', 'active' => false, 'icon' => 'M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007Z'],
        
        ['label' => 'Inventory', 'permission' => 'inventory.view', 'url' => '#', 'active' => false, 'icon' => 'M21 7.5 12 2.25 3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25m0-9L3 7.5m9 5.25v9m-9-14.25v9l9 5.25'],

        ['label' => 'Reports', 'permission' => 'reports.view', 'url' => '#', 'active' => false, 'icon' => 'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z'],
        ['label' => 'User Management', 'permission' => 'user-management.view', 'url' => route('user-management.index'), 'active' => request()->routeIs('user-management.*'), 'icon' => 'M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 1 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 1 1 5.25 0Z'],
    ];
@endphp

<div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-40 bg-slate-900/60 lg:hidden" @click="sidebarOpen = false"></div>

<aside
    class="fixed inset-y-0 left-0 z-50 flex w-72 flex-col bg-slate-950 text-slate-100 shadow-xl transition-all duration-200 lg:translate-x-0"
    :class="[sidebarOpen ? 'translate-x-0' : '-translate-x-full', sidebarCollapsed ? 'lg:w-24' : 'lg:w-72']"
>
    <div class="flex h-16 items-center justify-between border-b border-white/10 px-4">
        <a href="{{ route('dashboard') }}" class="flex min-w-0 items-center gap-3">
            <span class="grid size-10 shrink-0 place-items-center rounded-lg bg-cyan-400 text-sm font-bold text-slate-950">FC</span>
            <span class="truncate text-lg font-semibold tracking-wide" x-show="! sidebarCollapsed">Flowcurement</span>
        </a>
        <button type="button" class="hidden rounded-md p-2 text-slate-300 hover:bg-white/10 hover:text-white lg:inline-flex" x-show="! sidebarCollapsed" @click="sidebarCollapsed = true" aria-label="Collapse sidebar">
            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
        </button>
        <button type="button" class="hidden rounded-md p-2 text-slate-300 hover:bg-white/10 hover:text-white lg:inline-flex" x-show="sidebarCollapsed" @click="sidebarCollapsed = false" aria-label="Expand sidebar">
            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg>
        </button>
    </div>

    <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
        @foreach ($navigation as $item)
            @can($item['permission'])
                @if (isset($item['children']))
                    <div x-data="{ open: {{ ($item['active'] ?? false) ? 'true' : 'false' }} }" class="space-y-1">
                        <button
                            type="button"
                            @click="sidebarCollapsed ? sidebarCollapsed = false : open = ! open"
                            @class([
                                'group flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-left text-sm font-medium transition',
                                'bg-cyan-400/15 text-cyan-300 ring-1 ring-cyan-400/20' => $item['active'] ?? false,
                                'text-slate-300 hover:bg-white/10 hover:text-white' => ! ($item['active'] ?? false),
                            ])
                            aria-expanded="{{ ($item['active'] ?? false) ? 'true' : 'false' }}"
                        >
                            <svg class="size-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" /></svg>
                            <span class="min-w-0 flex-1 truncate" x-show="! sidebarCollapsed">{{ $item['label'] }}</span>
                            <svg class="size-4 shrink-0 transition-transform" :class="{ 'rotate-90': open }" x-show="! sidebarCollapsed" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                            </svg>
                        </button>

                        <div x-show="open && ! sidebarCollapsed" x-transition class="ml-8 space-y-1 border-l border-white/10 pl-3">
                            @foreach ($item['children'] as $child)
                                <a href="{{ $child['url'] }}" @class([
                                    'block rounded-md px-3 py-2 text-sm font-medium transition',
                                    'bg-cyan-400/10 text-cyan-200' => $child['active'] ?? false,
                                    'text-slate-400 hover:bg-white/10 hover:text-white' => ! ($child['active'] ?? false),
                                ]) aria-current="{{ ($child['active'] ?? false) ? 'page' : 'false' }}">
                                    {{ $child['label'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @else
                    <a href="{{ $item['url'] }}" @class([
                        'group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition',
                        'bg-cyan-400/15 text-cyan-300 ring-1 ring-cyan-400/20' => $item['active'] ?? false,
                        'text-slate-300 hover:bg-white/10 hover:text-white' => ! ($item['active'] ?? false),
                    ]) aria-current="{{ ($item['active'] ?? false) ? 'page' : 'false' }}">
                        <svg class="size-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" /></svg>
                        <span class="truncate" x-show="! sidebarCollapsed">{{ $item['label'] }}</span>
                    </a>
                @endif
            @endcan
        @endforeach
    </nav>

    <div class="border-t border-white/10 p-4" x-show="! sidebarCollapsed">
        <p class="text-xs uppercase tracking-wider text-slate-500">ERP Foundation</p>
        <p class="mt-1 text-sm text-slate-300">Phase 1 setup</p>
    </div>
</aside>
