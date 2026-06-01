<x-app-layout>
    <x-slot name="header">
        <div>
            <div>
                <p class="text-sm font-medium text-cyan-700">User Management</p>
                <h2 class="text-2xl font-semibold text-slate-950">View User</h2>
            </div>
        </div>
    </x-slot>

    @if (session('status'))
        <div class="mb-5 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
            {{ session('status') }}
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_minmax(320px,420px)]">
        <section class="erp-panel">
            <div class="erp-panel-header">
                <h3 class="text-base font-semibold text-slate-950">Account Details</h3>
            </div>
            <div class="erp-panel-body">
                <dl class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-xs font-semibold uppercase text-slate-500">Name</dt>
                        <dd class="mt-1 text-sm font-medium text-slate-950">{{ $user->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase text-slate-500">Username</dt>
                        <dd class="mt-1 text-sm font-medium text-slate-950">{{ $user->username }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase text-slate-500">Email</dt>
                        <dd class="mt-1 text-sm font-medium text-slate-950">{{ $user->email }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase text-slate-500">Status</dt>
                        <dd class="mt-1">
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $user->status === 'active' ? 'bg-emerald-600 text-white' : 'bg-slate-600 text-white' }}">
                                {{ str($user->status)->headline() }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase text-slate-500">Last Login</dt>
                        <dd class="mt-1 text-sm font-medium text-slate-950">{{ $user->last_login_at?->format('M d, Y H:i') ?? 'Never' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase text-slate-500">Created</dt>
                        <dd class="mt-1 text-sm font-medium text-slate-950">{{ $user->created_at?->format('M d, Y H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase text-slate-500">Created By</dt>
                        <dd class="mt-1 text-sm font-medium text-slate-950">{{ $user->creator?->name ?? 'System' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase text-slate-500">Updated By</dt>
                        <dd class="mt-1 text-sm font-medium text-slate-950">{{ $user->updater?->name ?? 'System' }}</dd>
                    </div>
                </dl>
            </div>
        </section>

        <div class="space-y-6">
            <section class="erp-panel">
                <div class="erp-panel-header">
                    <h3 class="text-base font-semibold text-slate-950">Roles</h3>
                </div>
                <div class="erp-panel-body flex flex-wrap gap-2">
                    @forelse ($user->roles as $role)
                        <span class="rounded-md bg-cyan-50 px-2.5 py-1.5 text-xs font-semibold text-cyan-800">{{ str($role->name)->headline() }}</span>
                    @empty
                        <span class="text-sm text-slate-500">No roles assigned.</span>
                    @endforelse
                </div>
            </section>

            <section class="erp-panel">
                <div class="erp-panel-header">
                    <h3 class="text-base font-semibold text-slate-950">Direct Permissions</h3>
                </div>
                <div class="erp-panel-body flex flex-wrap gap-2">
                    @forelse ($user->permissions as $permission)
                        <span class="rounded-md bg-slate-100 px-2.5 py-1.5 text-xs font-semibold text-slate-700">{{ $permission->name }}</span>
                    @empty
                        <span class="text-sm text-slate-500">No direct permissions assigned.</span>
                    @endforelse
                </div>
            </section>
        </div>
    </div>

    <div class="sticky bottom-0 mt-5 flex items-center justify-end gap-2 border-t border-slate-200 bg-slate-100/95 py-4 backdrop-blur">
        <a href="{{ route('user-management.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Back</a>
        @can('update', $user)
            <a href="{{ route('user-management.edit', $user) }}" class="rounded-md bg-slate-950 px-4 py-2 text-sm font-semibold text-white shadow-sm erp-focus-ring hover:bg-slate-800">Edit</a>
        @endcan
    </div>
</x-app-layout>
