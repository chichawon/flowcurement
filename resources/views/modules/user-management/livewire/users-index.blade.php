<div class="space-y-5">
    @if (session('status'))
        <div class="rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
            {{ session('status') }}
        </div>
    @endif

    <section class="erp-panel">
        <div class="erp-panel-header flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h3 class="text-base font-semibold text-slate-950">Users</h3>
                <p class="mt-1 text-sm text-slate-500">Manage accounts, roles, direct permissions, status, and deleted records.</p>
            </div>
            @can('create', \App\Models\User::class)
                <a href="{{ route('user-management.create') }}" class="inline-flex items-center justify-center rounded-md bg-slate-950 px-4 py-2 text-sm font-semibold text-white shadow-sm erp-focus-ring hover:bg-slate-800">
                    Create User
                </a>
            @endcan
        </div>

        <div class="erp-panel-body space-y-4">
            <div class="grid gap-3 md:grid-cols-5">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700" for="user-search">Search</label>
                    <input id="user-search" type="search" wire:model.live.debounce.350ms="search" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm erp-focus-ring" placeholder="Name, username, or email">
                </div>

                <label class="block">
                    <span class="text-sm font-medium text-slate-700">Status</span>
                    <select wire:model.live="status" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm erp-focus-ring">
                        <option value="">All statuses</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </label>

                <label class="block">
                    <span class="text-sm font-medium text-slate-700">Role</span>
                    <select wire:model.live="role" class="mt-1 block w-full rounded-md border-slate-300 text-sm shadow-sm erp-focus-ring">
                        <option value="">All roles</option>
                        @foreach ($roles as $roleOption)
                            <option value="{{ $roleOption->name }}">{{ str($roleOption->name)->headline() }}</option>
                        @endforeach
                    </select>
                </label>
            </div>

            <div class="flex items-center justify-end gap-3">
                <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                    <span>Rows</span>
                    <select wire:model.live="perPage" class="rounded-md border-slate-300 text-sm shadow-sm erp-focus-ring">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                </label>
            </div>

            <div class="relative overflow-visible rounded-lg border border-slate-200">
                <table class="w-full table-fixed divide-y divide-slate-200 text-sm">
                    <colgroup>
                        <col class="w-20">
                        <col class="w-[46%]">
                        <col class="w-[16%]">
                        <col class="w-[30%]">
                    </colgroup>
                    <thead class="bg-slate-50 text-xs font-semibold uppercase text-slate-500">
                        <tr>
                            <th class="px-4 py-3 text-center">Action</th>
                            <th class="px-4 py-3 text-left">User</th>
                            <th class="px-3 py-3 text-center">Status</th>
                            <th class="px-3 py-3 text-center">Roles</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @forelse ($users as $user)
                            <tr class="{{ $user->trashed() ? 'bg-slate-50 text-slate-500' : '' }}">
                                <td class="whitespace-nowrap px-4 py-3 text-center align-middle">
                                    <x-dropdown align="left" width="48">
                                        <x-slot name="trigger">
                                            <button type="button" class="mx-auto inline-flex size-8 items-center justify-center rounded-md border border-slate-300 bg-white text-slate-600 hover:bg-slate-50 hover:text-slate-900" aria-label="Open actions">
                                                <svg class="size-4 text-slate-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path d="M10 3a1.75 1.75 0 1 0 0 3.5A1.75 1.75 0 0 0 10 3Zm0 5.75A1.75 1.75 0 1 0 10 12.25 1.75 1.75 0 0 0 10 8.75Zm0 5.75a1.75 1.75 0 1 0 0 3.5 1.75 1.75 0 0 0 0-3.5Z" />
                                                </svg>
                                            </button>
                                        </x-slot>

                                        <x-slot name="content">
                                            @if ($user->trashed())
                                                @can('restore', $user)
                                                    <button type="button" wire:click="restoreUser({{ $user->id }})" class="flex w-full items-center gap-2 px-4 py-2 text-start text-sm text-slate-700 hover:bg-slate-100"><x-action-icon name="restore" /> Restore</button>
                                                @endcan
                                                @can('forceDelete', $user)
                                                    <button type="button" wire:click="promptForceDeleteUser({{ $user->id }})" class="flex w-full items-center gap-2 px-4 py-2 text-start text-sm text-red-700 hover:bg-red-50"><x-action-icon name="delete" /> Delete Forever</button>
                                                @endcan
                                            @else
                                                @can('view', $user)
                                                    <a href="{{ route('user-management.show', $user) }}" class="flex w-full items-center gap-2 px-4 py-2 text-start text-sm text-slate-700 hover:bg-slate-100"><x-action-icon name="view" /> View</a>
                                                @endcan
                                                @can('update', $user)
                                                    <a href="{{ route('user-management.edit', $user) }}" class="flex w-full items-center gap-2 px-4 py-2 text-start text-sm text-slate-700 hover:bg-slate-100"><x-action-icon name="edit" /> Edit</a>
                                                @endcan
                                                @can('delete', $user)
                                                    <button type="button" wire:click="promptDeleteUser({{ $user->id }})" class="flex w-full items-center gap-2 px-4 py-2 text-start text-sm text-red-700 hover:bg-red-50"><x-action-icon name="delete" /> Delete</button>
                                                @endcan
                                            @endif
                                        </x-slot>
                                    </x-dropdown>
                                </td>
                                <td class="px-4 py-3 align-middle">
                                    <div class="grid w-full grid-cols-[2.25rem_minmax(0,1fr)] items-start gap-3 text-left">
                                        <span class="mt-0.5 grid size-9 place-items-center rounded-md bg-slate-900 text-xs font-bold text-white">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                        <span class="min-w-0 text-justify leading-5">
                                            <span class="block truncate font-semibold text-slate-950">{{ $user->name }}</span>
                                            <span class="block break-words text-xs text-slate-500">{{ $user->username }} - {{ $user->email }}</span>
                                            <span class="mt-0.5 block break-words text-[11px] text-slate-400">By: {{ $user->creator?->name ?? 'System' }} . Updated: {{ $user->updater?->name ?? 'System' }}</span>
                                        </span>
                                    </div>
                                </td>
                                <td class="px-3 py-3 text-center align-middle">
                                    <div class="flex flex-col items-center gap-1">
                                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $user->status === 'active' ? 'bg-emerald-600 text-white' : 'bg-slate-600 text-white' }}">
                                            {{ str($user->status)->headline() }}
                                        </span>
                                        @if ($user->trashed())
                                            <span class="inline-flex rounded-full bg-red-600 px-2.5 py-1 text-xs font-semibold text-white">Deleted</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-3 py-3 text-center align-middle">
                                    <div class="flex flex-wrap justify-center gap-1">
                                        @forelse ($user->roles as $roleBadge)
                                            <span class="rounded-md bg-cyan-50 px-2 py-1 text-xs font-medium text-cyan-800">{{ str($roleBadge->name)->headline() }}</span>
                                        @empty
                                            <span class="text-xs text-slate-400">No roles</span>
                                        @endforelse
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-10 text-center text-sm text-slate-500">No users found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($users->total() > 0)
                @php
                    $currentPage = $users->currentPage();
                    $lastPage = $users->lastPage();
                    $startPage = max(1, $currentPage - 2);
                    $endPage = min($lastPage, $currentPage + 2);
                @endphp

                <div class="flex flex-col gap-3 border-t border-slate-200 pt-4 text-sm sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-slate-500">
                        Showing <span class="font-semibold text-slate-700">{{ $users->firstItem() }}</span>
                        to <span class="font-semibold text-slate-700">{{ $users->lastItem() }}</span>
                        of <span class="font-semibold text-slate-700">{{ $users->total() }}</span> records
                    </p>

                    <div class="flex flex-wrap items-center gap-1">
                        <button
                            type="button"
                            wire:click="previousPage"
                            @disabled($users->onFirstPage())
                            class="inline-flex min-h-9 items-center rounded-md border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            Previous
                        </button>

                        @if ($startPage > 1)
                            <button type="button" wire:click="gotoPage(1)" class="inline-flex size-9 items-center justify-center rounded-md border border-slate-300 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50">1</button>
                            @if ($startPage > 2)
                                <span class="px-2 text-slate-400">...</span>
                            @endif
                        @endif

                        @for ($page = $startPage; $page <= $endPage; $page++)
                            <button
                                type="button"
                                wire:click="gotoPage({{ $page }})"
                                @class([
                                    'inline-flex size-9 items-center justify-center rounded-md border text-sm font-semibold',
                                    'border-slate-950 bg-slate-950 text-white' => $page === $currentPage,
                                    'border-slate-300 bg-white text-slate-700 hover:bg-slate-50' => $page !== $currentPage,
                                ])
                            >
                                {{ $page }}
                            </button>
                        @endfor

                        @if ($endPage < $lastPage)
                            @if ($endPage < $lastPage - 1)
                                <span class="px-2 text-slate-400">...</span>
                            @endif
                            <button type="button" wire:click="gotoPage({{ $lastPage }})" class="inline-flex size-9 items-center justify-center rounded-md border border-slate-300 bg-white text-sm font-semibold text-slate-700 hover:bg-slate-50">{{ $lastPage }}</button>
                        @endif

                        <button
                            type="button"
                            wire:click="nextPage"
                            @disabled(! $users->hasMorePages())
                            class="inline-flex min-h-9 items-center rounded-md border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            Next
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </section>

    <div
        x-data="{ open: @entangle('showDeleteConfirmation').live }"
        x-show="open"
        x-transition.opacity
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center px-4 py-6 sm:px-0"
        role="dialog"
        aria-modal="true"
    >
        <div class="absolute inset-0 bg-slate-950/60" @click="open = false"></div>

        <div
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95 translate-y-2"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-2"
            class="relative w-full max-w-sm rounded-xl bg-white shadow-2xl"
        >
            <div class="border-b border-slate-200 px-5 py-4">
                <div class="flex items-center gap-3">
                    <div class="grid size-10 shrink-0 place-items-center rounded-full bg-red-100 text-red-700">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.72 3h16.92a2 2 0 0 0 1.72-3L13.71 3.86a2 2 0 0 0-3.42 0Z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-base font-semibold text-slate-950">
                            {{ $pendingDeleteMode === 'forceDelete' ? 'Delete forever?' : 'Delete user?' }}
                        </h3>
                        <p class="mt-0.5 text-sm text-slate-500">
                            {{ $pendingDeleteMode === 'forceDelete' ? 'This action cannot be undone.' : 'The user will be moved to deleted records.' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="px-5 py-4">
                <p class="text-sm text-slate-600">
                    Delete:
                </p>
                <p class="mt-1 text-sm font-semibold text-slate-950">
                    {{ $pendingDeleteName }}
                </p>
            </div>

            <div class="flex items-center justify-end gap-2 border-t border-slate-200 px-5 py-4">
                <button
                    type="button"
                    class="inline-flex items-center justify-center rounded-md border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                    @click="open = false"
                >
                    Cancel
                </button>

                <button
                    type="button"
                    class="inline-flex items-center justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white hover:bg-red-700"
                    wire:click="deleteConfirmedUser"
                >
                    {{ $pendingDeleteMode === 'forceDelete' ? 'Delete Forever' : 'Delete User' }}
                </button>
            </div>
        </div>
    </div>
</div>
