<?php

namespace App\Modules\UserManagement\Livewire;

use App\Models\User;
use App\Modules\UserManagement\Services\RolePermissionService;
use App\Modules\UserManagement\Services\UserService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithPagination;

class UsersIndex extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    public string $search = '';

    public string $status = '';

    public string $role = '';

    public bool $withTrashed = false;

    public int $perPage = 10;

    public bool $showDeleteConfirmation = false;

    public ?int $pendingDeleteUserId = null;

    public string $pendingDeleteMode = 'delete';

    public string $pendingDeleteName = '';

    public function mount(): void
    {
        $this->authorize('viewAny', User::class);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updatingRole(): void
    {
        $this->resetPage();
    }

    public function updatingWithTrashed(): void
    {
        $this->resetPage();
    }

    public function promptDeleteUser(int $userId): void
    {
        $user = User::query()->find($userId);

        if (! $user) {
            session()->flash('status', 'User record was already deleted or no longer exists.');
            $this->resetDeleteConfirmationState();

            return;
        }

        $this->authorize('delete', $user);

        $this->pendingDeleteUserId = $user->id;
        $this->pendingDeleteMode = 'delete';
        $this->pendingDeleteName = $user->name;
        $this->showDeleteConfirmation = true;
    }

    public function promptForceDeleteUser(int $userId): void
    {
        $user = User::onlyTrashed()->find($userId);

        if (! $user) {
            session()->flash('status', 'Deleted user record no longer exists.');
            $this->resetDeleteConfirmationState();

            return;
        }

        $this->authorize('forceDelete', $user);

        $this->pendingDeleteUserId = $user->id;
        $this->pendingDeleteMode = 'forceDelete';
        $this->pendingDeleteName = $user->name;
        $this->showDeleteConfirmation = true;
    }

    public function deleteConfirmedUser(): void
    {
        if (! $this->pendingDeleteUserId) {
            $this->resetDeleteConfirmationState();

            return;
        }

        $users = app(UserService::class);
        $user = User::query()->withTrashed()->find($this->pendingDeleteUserId);

        if (! $user) {
            session()->flash('status', 'User record was already deleted or no longer exists.');
            $this->resetDeleteConfirmationState();

            return;
        }

        if ($this->pendingDeleteMode === 'forceDelete') {
            $this->authorize('forceDelete', $user);
            $users->forceDelete($user);
            session()->flash('status', 'User permanently deleted.');
        } else {
            $this->authorize('delete', $user);
            $users->markDeleted($user);
            session()->flash('status', 'User moved to deleted records.');
        }

        $this->resetDeleteConfirmationState();
    }

    public function cancelDeleteConfirmation(): void
    {
        $this->resetDeleteConfirmationState();
    }

    public function restoreUser(int $userId): void
    {
        $users = app(UserService::class);
        $user = User::onlyTrashed()->findOrFail($userId);
        $this->authorize('restore', $user);
        $users->restore($user);
        session()->flash('status', 'User restored successfully.');
    }

    private function resetDeleteConfirmationState(): void
    {
        $this->showDeleteConfirmation = false;
        $this->pendingDeleteUserId = null;
        $this->pendingDeleteMode = 'delete';
        $this->pendingDeleteName = '';
    }

    public function render()
    {
        $users = app(UserService::class);
        $access = app(RolePermissionService::class);

        return view('modules.user-management.livewire.users-index', [
            'users' => $users->paginate([
                'search' => $this->search,
                'status' => $this->status,
                'role' => $this->role,
                'with_trashed' => $this->withTrashed,
            ], $this->perPage),
            'roles' => $access->roles(),
        ]);
    }
}
