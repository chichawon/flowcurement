@props([
    'user' => null,
    'roles',
    'permissions',
    'selectedRoles' => [],
    'selectedPermissions' => [],
    'submitLabel' => 'Save User',
])

@php
    $editing = filled($user);
@endphp

<div class="space-y-4">
    <div class="grid gap-4 lg:grid-cols-2">
        <section class="erp-panel h-full">
            <div class="erp-panel-header">
                <h3 class="text-sm font-semibold text-slate-950">Account Information</h3>
            </div>
            <div class="erp-panel-body space-y-3">
                <div class="grid gap-3 sm:grid-cols-2">
                    <x-admin.form-field label="Full Name" name="name" value="{{ old('name', $user?->name) }}" required />
                    <x-admin.form-field label="Username" name="username" value="{{ old('username', $user?->username) }}" required />
                </div>

                <x-admin.form-field label="Email" name="email" type="email" value="{{ old('email', $user?->email) }}" required />

                <div class="grid gap-3 sm:grid-cols-2">
                    <x-admin.form-field label="{{ $editing ? 'New Password' : 'Password' }}" name="password" type="password" :required="! $editing" />
                    <x-admin.form-field label="Confirm Password" name="password_confirmation" type="password" :required="! $editing" />
                </div>

                <label class="block">
                    <span class="text-sm font-medium text-slate-700">Account Status</span>
                    <select name="status" class="mt-1 block h-10 w-full rounded-md border-slate-300 px-3 text-sm shadow-sm erp-focus-ring" required>
                        <option value="active" @selected(old('status', $user?->status ?? 'active') === 'active')>Active</option>
                        <option value="inactive" @selected(old('status', $user?->status ?? 'active') === 'inactive')>Inactive</option>
                    </select>
                    @error('status')
                        <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span>
                    @enderror
                </label>
            </div>
        </section>

        @can('user-management.assign-roles')
            <section class="erp-panel h-full">
                <div class="erp-panel-header">
                    <h3 class="text-sm font-semibold text-slate-950">Roles</h3>
                </div>
                <div class="erp-panel-body">
                    <div class="grid gap-2 sm:grid-cols-2">
                        @foreach ($roles as $roleOption)
                            <label class="flex items-center gap-2 rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-700">
                                <input type="checkbox" name="roles[]" value="{{ $roleOption->name }}" @checked(in_array($roleOption->name, $selectedRoles, true)) class="rounded border-slate-300 text-cyan-600 erp-focus-ring">
                                <span>{{ str($roleOption->name)->headline() }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('roles')
                        <span class="text-xs font-medium text-red-600">{{ $message }}</span>
                    @enderror
                </div>
            </section>
        @endcan
    </div>

    @can('user-management.assign-permissions')
        @php
            $tabGroups = collect();
            foreach ($permissions as $group => $groupPermissions) {
                $tab = str_starts_with($group, 'Sales /') ? 'Sales' : $group;
                if (! $tabGroups->has($tab)) {
                    $tabGroups->put($tab, collect());
                }
                $tabGroups->get($tab)->put($group, collect($groupPermissions));
            }
            $tabOrder = [
                'Business Partners',
                'Items',
                'Quotations',
                'Sales',
                'Purchasing',
                'Inventory',
                'Reports',
                'User Management',
            ];
            $orderedTabKeys = collect($tabOrder)->filter(fn ($tab) => $tabGroups->has($tab))
                ->merge($tabGroups->keys()->reject(fn ($tab) => in_array($tab, $tabOrder, true)))
                ->values();
            $initialTab = $orderedTabKeys->first();
        @endphp
        <section class="erp-panel w-full" x-data="{ permTab: '{{ $initialTab }}' }">
            <div class="erp-panel-header">
                <h3 class="text-sm font-semibold text-slate-950">Direct Permissions</h3>
            </div>
            <div class="erp-panel-body">
                <div class="mb-4 overflow-x-auto">
                    <div class="inline-flex min-w-full items-end gap-1 border-b border-slate-300 px-1 pt-1">
                        @foreach ($orderedTabKeys as $tabName)
                            <button
                                type="button"
                                @click="permTab = '{{ $tabName }}'"
                                :class="permTab === '{{ $tabName }}' ? 'z-10 bg-cyan-600 text-white border-cyan-600 shadow-sm' : 'bg-slate-100 text-slate-600 border-slate-300 hover:bg-slate-200 hover:text-slate-900'"
                                class="relative -mb-px inline-flex h-9 items-center rounded-t-lg border px-4 text-xs font-semibold whitespace-nowrap transition"
                            >
                                {{ $tabName }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="max-h-[38rem] space-y-2 overflow-y-auto pr-1">
                    @foreach ($orderedTabKeys as $tabName)
                        @php($groupSet = $tabGroups->get($tabName))
                        <div x-show="permTab === '{{ $tabName }}'" x-cloak class="space-y-2">
                            @foreach ($groupSet as $group => $groupPermissions)
                                <div class="rounded-md border border-slate-200 bg-white px-3 py-2.5">
                                    <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-500">
                                        {{ str_starts_with($group, 'Sales /') ? str($group)->after('Sales /')->replace('Order', 'Sales Order')->replace('Invoice', 'Sales Invoice')->replace('Collection', 'Sales Collection') : $group }}
                                    </p>
                                    <div class="mt-2 grid gap-x-3 gap-y-2 sm:grid-cols-2 xl:grid-cols-4">
                                        @foreach ($groupPermissions as $permission)
                                            <label class="flex items-center gap-2 px-1 py-1 text-xs text-slate-700">
                                                <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" @checked(in_array($permission->name, $selectedPermissions, true)) class="size-3.5 rounded border-slate-300 text-cyan-600 erp-focus-ring">
                                                <span class="leading-4">{{ str($permission->name)->after('.')->headline() }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endcan
</div>

<div class="sticky bottom-0 mt-5 flex items-center justify-end gap-2 border-t border-slate-200 bg-slate-100/95 py-4 backdrop-blur">
    <a href="{{ route('user-management.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancel</a>
    <button type="submit" class="rounded-md bg-slate-950 px-4 py-2 text-sm font-semibold text-white shadow-sm erp-focus-ring hover:bg-slate-800">
        {{ $submitLabel }}
    </button>
</div>
