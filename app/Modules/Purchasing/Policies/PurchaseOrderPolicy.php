<?php

namespace App\Modules\Purchasing\Policies;

use App\Models\User;
use App\Modules\Purchasing\Helpers\PurchaseOrderPermissions;
use App\Modules\Purchasing\Models\PurchaseOrder;

class PurchaseOrderPolicy
{
    public function viewAny(User $actor): bool { return $actor->can(PurchaseOrderPermissions::VIEW); }
    public function view(User $actor, PurchaseOrder $purchaseOrder): bool { return $actor->can(PurchaseOrderPermissions::VIEW); }
    public function create(User $actor): bool { return $actor->can(PurchaseOrderPermissions::CREATE); }
    public function update(User $actor, PurchaseOrder $purchaseOrder): bool { return $actor->can(PurchaseOrderPermissions::UPDATE) && $purchaseOrder->status !== 'cancelled'; }
    public function delete(User $actor, PurchaseOrder $purchaseOrder): bool { return $actor->can(PurchaseOrderPermissions::DELETE) && ! in_array($purchaseOrder->status, ['received', 'cancelled'], true); }
    public function print(User $actor, PurchaseOrder $purchaseOrder): bool { return $actor->can(PurchaseOrderPermissions::PRINT); }
    public function cancel(User $actor, PurchaseOrder $purchaseOrder): bool { return $actor->can(PurchaseOrderPermissions::CANCEL) && $purchaseOrder->status !== 'cancelled'; }
}
