<?php

namespace App\Modules\UserManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\UserManagement\Requests\StoreUserRequest;
use App\Modules\UserManagement\Requests\UpdateUserRequest;
use App\Modules\UserManagement\Services\RolePermissionService;
use App\Modules\UserManagement\Services\UserService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class UserController extends Controller
{
    use AuthorizesRequests;

    public function index(): View
    {
        $this->authorize('viewAny', User::class);

        return view('modules.user-management.index');
    }

    public function create(RolePermissionService $access): View
    {
        $this->authorize('create', User::class);

        return view('modules.user-management.create', [
            'roles' => $access->roles(),
            'permissions' => $this->groupPermissions($access->permissions()),
            'selectedRoles' => old('roles', []),
            'selectedPermissions' => old('permissions', []),
        ]);
    }

    public function store(StoreUserRequest $request, UserService $users): RedirectResponse
    {
        $payload = $request->safe()->except(['roles', 'permissions', 'password_confirmation']);
        $payload['created_by'] = $request->user()->id;
        $payload['updated_by'] = $request->user()->id;

        $user = $users->create(
            $payload,
            $request->user()->can('user-management.assign-roles') ? $request->validated('roles', []) : [],
            $request->user()->can('user-management.assign-permissions') ? $request->validated('permissions', []) : []
        );

        return redirect()
            ->route('user-management.index')
            ->with('toast', 'User created successfully.');
    }

    public function show(int $user): View|RedirectResponse
    {
        $user = User::query()->find($user);

        if (! $user) {
            return redirect()->route('user-management.index')->with('toast', 'User record was already deleted or no longer exists.');
        }

        $this->authorize('view', $user);
        $user->load(['roles.permissions', 'permissions', 'creator:id,name', 'updater:id,name']);

        return view('modules.user-management.show', [
            'user' => $user,
        ]);
    }

    public function edit(int $user, RolePermissionService $access): View|RedirectResponse
    {
        $user = User::query()->find($user);

        if (! $user) {
            return redirect()->route('user-management.index')->with('toast', 'User record was already deleted or no longer exists.');
        }

        $this->authorize('update', $user);
        $user->load(['roles', 'permissions']);

        return view('modules.user-management.edit', [
            'user' => $user,
            'roles' => $access->roles(),
            'permissions' => $this->groupPermissions($access->permissions()),
            'selectedRoles' => old('roles', $user->roles->pluck('name')->all()),
            'selectedPermissions' => old('permissions', $user->permissions->pluck('name')->all()),
        ]);
    }

    public function update(UpdateUserRequest $request, int $user, UserService $users): RedirectResponse
    {
        $user = User::query()->find($user);

        if (! $user) {
            return redirect()->route('user-management.index')->with('toast', 'User record was already deleted or no longer exists.');
        }

        $payload = $request->safe()->except(['roles', 'permissions', 'password_confirmation']);
        $payload['updated_by'] = $request->user()->id;

        $user->load(['roles', 'permissions']);

        $users->update(
            $user,
            $payload,
            $request->user()->can('assignRoles', $user) ? $request->validated('roles', []) : $user->roles->pluck('name')->all(),
            $request->user()->can('assignPermissions', $user) ? $request->validated('permissions', []) : $user->permissions->pluck('name')->all()
        );

        return redirect()
            ->route('user-management.index')
            ->with('toast', 'User updated successfully.');
    }

    /**
     * @param Collection<int, mixed> $permissions
     * @return Collection<string, Collection<int, mixed>>
     */
    private function groupPermissions(Collection $permissions): Collection
    {
        $labelMap = [
            'business-partners' => 'Business Partners',
            'items' => 'Items',
            'quotations' => 'Quotations',
            'sales-orders' => 'Sales / Order',
            'delivery-receipts' => 'Sales / D.R',
            'sales-invoices' => 'Sales / Invoice',
            'sales-collections' => 'Sales / Collection',
        ];

        $orderedLabels = [
            'Sales',
            'Sales / Order',
            'Sales / D.R',
            'Sales / Invoice',
            'Sales / Collection',
            'Business Partners',
            'Items',
            'Quotations',
        ];

        $grouped = $permissions
            ->reject(fn ($permission) => str($permission->name)->startsWith('dashboard.'))
            ->groupBy(function ($permission) use ($labelMap): string {
                $module = (string) str($permission->name)->before('.');

                return $labelMap[$module] ?? str($module)->headline()->value();
            });

        $ordered = collect();

        foreach ($orderedLabels as $label) {
            if ($grouped->has($label)) {
                $ordered->put($label, $grouped->get($label));
            }
        }

        foreach ($grouped as $label => $groupPermissions) {
            if (! $ordered->has($label)) {
                $ordered->put($label, $groupPermissions);
            }
        }

        return $ordered;
    }
}
