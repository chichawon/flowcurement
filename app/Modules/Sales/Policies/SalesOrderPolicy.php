<?php

namespace App\Modules\Sales\Policies;

use App\Models\User;
use App\Modules\Sales\Helpers\SalesOrderPermissions;
use App\Modules\Sales\Models\SalesOrder;

class SalesOrderPolicy
{
    public function viewAny(User $actor): bool { return $actor->can(SalesOrderPermissions::VIEW); }
    public function view(User $actor, SalesOrder $salesOrder): bool { return $actor->can(SalesOrderPermissions::VIEW); }
    public function create(User $actor): bool { return $actor->can(SalesOrderPermissions::CREATE); }
    public function update(User $actor, SalesOrder $salesOrder): bool { return $actor->can(SalesOrderPermissions::UPDATE); }
    public function delete(User $actor, SalesOrder $salesOrder): bool
    {
        return $actor->can(SalesOrderPermissions::DELETE)
            && ! in_array($salesOrder->status, ['partial', 'served'], true);
    }
    public function approve(User $actor, SalesOrder $salesOrder): bool { return $actor->can(SalesOrderPermissions::APPROVE); }
    public function cancel(User $actor, SalesOrder $salesOrder): bool { return $actor->can(SalesOrderPermissions::CANCEL); }
    public function print(User $actor, SalesOrder $salesOrder): bool { return $actor->can(SalesOrderPermissions::PRINT); }
}
