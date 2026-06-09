<?php

namespace App\Modules\Purchasing\Policies;

use App\Models\User;
use App\Modules\Purchasing\Helpers\PurchaseInvoicePermissions;
use App\Modules\Purchasing\Models\PurchaseInvoice;

class PurchaseInvoicePolicy
{
    public function viewAny(User $actor): bool { return $actor->can(PurchaseInvoicePermissions::VIEW); }
    public function view(User $actor, PurchaseInvoice $purchaseInvoice): bool { return $actor->can(PurchaseInvoicePermissions::VIEW); }
    public function create(User $actor): bool { return $actor->can(PurchaseInvoicePermissions::CREATE); }
    public function update(User $actor, PurchaseInvoice $purchaseInvoice): bool { return $actor->can(PurchaseInvoicePermissions::UPDATE) && $purchaseInvoice->status !== 'cancelled'; }
    public function delete(User $actor, PurchaseInvoice $purchaseInvoice): bool { return $actor->can(PurchaseInvoicePermissions::DELETE) && $purchaseInvoice->status !== 'paid'; }
    public function print(User $actor, PurchaseInvoice $purchaseInvoice): bool { return $actor->can(PurchaseInvoicePermissions::PRINT); }
    public function cancel(User $actor, PurchaseInvoice $purchaseInvoice): bool { return $actor->can(PurchaseInvoicePermissions::CANCEL) && $purchaseInvoice->status !== 'cancelled'; }
}
