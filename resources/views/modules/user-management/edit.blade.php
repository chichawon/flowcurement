<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-medium text-cyan-700">User Management</p>
            <h2 class="text-2xl font-semibold text-slate-950">Edit User</h2>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('user-management.update', $user) }}">
        @csrf
        @method('PUT')

        @include('modules.user-management.partials.form', [
            'user' => $user,
            'roles' => $roles,
            'permissions' => $permissions,
            'selectedRoles' => $selectedRoles,
            'selectedPermissions' => $selectedPermissions,
            'submitLabel' => 'Update User',
        ])
    </form>
</x-app-layout>
