<?php

namespace App\Modules\UserManagement\Services;

use App\Models\User;
use App\Modules\AuditTrail\Services\AuditTrailService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class UserService
{
    private const MODULE = 'user-management';

    /**
     * @param array{search?: string|null, status?: string|null, role?: string|null, with_trashed?: bool} $filters
     * @return LengthAwarePaginator<int, User>
     */
    public function paginate(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return User::query()
            ->with(['roles:id,name', 'permissions:id,name', 'creator:id,name', 'updater:id,name'])
            ->when($filters['with_trashed'] ?? false, fn (Builder $query) => $query->withTrashed())
            ->when($filters['search'] ?? null, function (Builder $query, string $search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status))
            ->when($filters['role'] ?? null, fn (Builder $query, string $role) => $query->role($role))
            ->latest('id')
            ->paginate($perPage);
    }

    /**
     * @param array<string, mixed> $data
     * @param array<int, string> $roles
     * @param array<int, string> $permissions
     */
    public function create(array $data, array $roles = [], array $permissions = []): User
    {
        return DB::transaction(function () use ($data, $roles, $permissions): User {
            $user = User::query()->create($this->payload($data));
            $user->syncRoles($roles);
            $user->syncPermissions($permissions);
            $user->refresh()->load(['roles:id,name', 'permissions:id,name']);

            app(AuditTrailService::class)->record(
                self::MODULE,
                'created',
                $user,
                null,
                array_merge($user->getAttributes(), [
                    'roles' => $user->roles->pluck('name')->all(),
                    'permissions' => $user->permissions->pluck('name')->all(),
                ]),
                'User created: '.$user->name
            );

            return $user;
        });
    }

    /**
     * @param array<string, mixed> $data
     * @param array<int, string> $roles
     * @param array<int, string> $permissions
     */
    public function update(User $user, array $data, array $roles = [], array $permissions = []): User
    {
        return DB::transaction(function () use ($user, $data, $roles, $permissions): User {
            $user->load(['roles:id,name', 'permissions:id,name']);
            $oldValues = array_merge($user->getOriginal(), [
                'roles' => $user->roles->pluck('name')->all(),
                'permissions' => $user->permissions->pluck('name')->all(),
            ]);

            $user->update($this->payload($data, false));
            $user->syncRoles($roles);
            $user->syncPermissions($permissions);
            $user->refresh()->load(['roles:id,name', 'permissions:id,name']);

            app(AuditTrailService::class)->record(
                self::MODULE,
                'updated',
                $user,
                $oldValues,
                array_merge($user->getAttributes(), [
                    'roles' => $user->roles->pluck('name')->all(),
                    'permissions' => $user->permissions->pluck('name')->all(),
                ]),
                'User updated: '.$user->name
            );

            return $user;
        });
    }

    public function markDeleted(User $user): void
    {
        DB::transaction(function () use ($user): void {
            $oldValues = $user->getOriginal();
            $user->delete();

            app(AuditTrailService::class)->record(
                self::MODULE,
                'deleted',
                $user,
                $oldValues,
                $user->fresh()?->getAttributes(),
                'User deleted: '.$user->name
            );
        });
    }

    public function restore(User $user): void
    {
        DB::transaction(function () use ($user): void {
            $oldValues = $user->getOriginal();
            $user->restore();
            $user->refresh();

            app(AuditTrailService::class)->record(
                self::MODULE,
                'restored',
                $user,
                $oldValues,
                $user->getAttributes(),
                'User restored: '.$user->name
            );
        });
    }

    public function forceDelete(User $user): void
    {
        DB::transaction(function () use ($user): void {
            $user->load(['roles:id,name', 'permissions:id,name']);
            $oldValues = array_merge($user->getOriginal(), [
                'roles' => $user->roles->pluck('name')->all(),
                'permissions' => $user->permissions->pluck('name')->all(),
            ]);

            app(AuditTrailService::class)->record(
                self::MODULE,
                'force_deleted',
                $user,
                $oldValues,
                null,
                'User permanently deleted: '.$user->name
            );

            $user->syncRoles([]);
            $user->syncPermissions([]);
            $user->forceDelete();
        });
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function payload(array $data, bool $creating = true): array
    {
        $payload = Arr::only($data, [
            'name',
            'username',
            'email',
            'password',
            'status',
            'email_verified_at',
            'created_by',
            'updated_by',
        ]);

        if (! $creating && blank($payload['password'] ?? null)) {
            unset($payload['password']);
        }

        return $payload;
    }
}
