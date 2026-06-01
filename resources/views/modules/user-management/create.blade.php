<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-cyan-700">User Management</p>
            <h2 class="text-2xl font-semibold text-slate-950">Create User</h2>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('user-management.store') }}">
        @csrf

        @include('modules.user-management.partials.form', [
            'roles' => $roles,
            'permissions' => $permissions,
            'selectedRoles' => $selectedRoles,
            'selectedPermissions' => $selectedPermissions,
            'submitLabel' => 'Create User',
        ])
    </form>
</x-app-layout>
