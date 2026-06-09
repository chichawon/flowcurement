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
                $tab = str_starts_with($group, 'Sales /') ? 'Sales' : (str_starts_with($group, 'Purchasing /') ? 'Purchasing' : $group);
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
                                x-on:click="permTab = '{{ $tabName }}'"
                                :class="permTab === '{{ $tabName }}' ? 'z-10 bg-cyan-600 text-white border-cyan-600 shadow-sm' : 'bg-slate-100 text-slate-600 border-slate-300 hover:bg-slate-200 hover:text-slate-900'"
                                class="relative -mb-px inline-flex h-9 shrink-0 items-center justify-center rounded-t-lg border px-2 text-xs font-semibold whitespace-nowrap transition"
                                style="width: 7.50rem; min-width: 7.50rem; max-width: 7.50rem;"
                            >
                                <span class="truncate">{{ $tabName }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="max-h-[38rem] overflow-y-auto pr-1">
                    @foreach ($orderedTabKeys as $tabName)
                        @php
                            $groupSet = $tabGroups->get($tabName);
                        @endphp
                        <div x-show="permTab === '{{ $tabName }}'" x-cloak class="grid gap-3 xl:grid-cols-2">
                            @foreach ($groupSet as $group => $groupPermissions)
                                @php
                                    $groupTitle = $group;
                                    if (str_starts_with($group, 'Sales /')) {
                                        $groupTitle = str($group)->after('Sales /')->replace('Order', 'Sales Order')->replace('Invoice', 'Sales Invoice')->replace('Collection', 'Sales Collection')->value();
                                    } elseif (str_starts_with($group, 'Purchasing /')) {
                                        $groupTitle = str($group)->after('Purchasing /')->value();
                                    }
                                @endphp
                                <div
                                    class="rounded-lg border border-slate-200 bg-white shadow-sm"
                                    x-data="{
                                        allChecked: false,
                                        sync() {
                                            const boxes = Array.from(this.$refs.permissions.querySelectorAll('input[type=checkbox]'));
                                            this.allChecked = boxes.length > 0 && boxes.every((box) => box.checked);
                                        },
                                        toggleAll() {
                                            Array.from(this.$refs.permissions.querySelectorAll('input[type=checkbox]')).forEach((box) => box.checked = this.allChecked);
                                        }
                                    }"
                                    x-init="sync()"
                                >
                                    <div class="flex items-center justify-between gap-3 border-b border-slate-200 bg-slate-50 px-3 py-2">
                                        <p class="text-xs font-semibold uppercase text-slate-600">
                                            {{ $groupTitle }}
                                        </p>
                                        <label class="inline-flex shrink-0 items-center gap-1.5 text-xs font-semibold text-slate-600">
                                            <input
                                                type="checkbox"
                                                x-model="allChecked"
                                                x-on:change="toggleAll()"
                                                class="size-3.5 rounded border-slate-300 text-cyan-600 erp-focus-ring"
                                            >
                                            <span>Check all</span>
                                        </label>
                                    </div>
                                    <div x-ref="permissions" x-on:change="sync()" class="grid gap-x-8 gap-y-4 px-4 py-4 sm:grid-cols-2">
                                        @foreach ($groupPermissions as $permission)
                                            <label class="flex min-h-8 items-center gap-3 text-sm leading-5 text-slate-700">
                                                <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" @checked(in_array($permission->name, $selectedPermissions, true)) class="size-4 shrink-0 rounded border-slate-300 text-cyan-600 erp-focus-ring">
                                                <span>{{ str($permission->name)->after('.')->headline() }}</span>
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
