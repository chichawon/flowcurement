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

<div class="grid gap-4 xl:grid-cols-[minmax(0,1.1fr)_minmax(0,0.9fr)]">
    <section class="erp-panel">
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

    <div class="space-y-6">
        @can('user-management.assign-roles')
            <section class="erp-panel">
                <div class="erp-panel-header">
                    <h3 class="text-sm font-semibold text-slate-950">Roles</h3>
                </div>
                <div class="erp-panel-body">
                    <div class="grid gap-2 sm:grid-cols-2 xl:grid-cols-1">
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

        @can('user-management.assign-permissions')
            <section class="erp-panel">
                <div class="erp-panel-header">
                    <h3 class="text-sm font-semibold text-slate-950">Direct Permissions</h3>
                </div>
                <div class="erp-panel-body">
                    <div class="max-h-[36rem] space-y-2 overflow-y-auto pr-1">
                        @foreach ($permissions as $group => $groupPermissions)
                            <div class="rounded-md border border-slate-200 p-3">
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-500">{{ $group }}</p>
                                <div class="mt-2 grid gap-2 sm:grid-cols-2 xl:grid-cols-3">
                                    @foreach ($groupPermissions as $permission)
                                        <label class="flex items-center gap-2 text-sm text-slate-700">
                                            <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" @checked(in_array($permission->name, $selectedPermissions, true)) class="rounded border-slate-300 text-cyan-600 erp-focus-ring">
                                            <span>{{ str($permission->name)->after('.')->headline() }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endcan
    </div>
</div>

<div class="sticky bottom-0 mt-5 flex items-center justify-end gap-2 border-t border-slate-200 bg-slate-100/95 py-4 backdrop-blur">
    <a href="{{ route('user-management.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancel</a>
    <button type="submit" class="rounded-md bg-slate-950 px-4 py-2 text-sm font-semibold text-white shadow-sm erp-focus-ring hover:bg-slate-800">
        {{ $submitLabel }}
    </button>
</div>
