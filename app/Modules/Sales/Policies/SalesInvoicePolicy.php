<?php

namespace App\Modules\Sales\Policies;

use App\Models\User;
use App\Modules\Sales\Helpers\SalesInvoicePermissions;
use App\Modules\Sales\Models\SalesInvoice;

class SalesInvoicePolicy
{
    public function viewAny(User $actor): bool { return $actor->can(SalesInvoicePermissions::VIEW); }
    public function view(User $actor, SalesInvoice $salesInvoice): bool { return $actor->can(SalesInvoicePermissions::VIEW); }
    public function create(User $actor): bool { return $actor->can(SalesInvoicePermissions::CREATE); }
    public function update(User $actor, SalesInvoice $salesInvoice): bool
    {
        return $actor->can(SalesInvoicePermissions::UPDATE)
            && $salesInvoice->status === 'pending';
    }
    public function delete(User $actor, SalesInvoice $salesInvoice): bool
    {
        return $actor->can(SalesInvoicePermissions::DELETE)
            && ! in_array($salesInvoice->status, ['paid', 'void'], true);
    }
    public function issue(User $actor, SalesInvoice $salesInvoice): bool
    {
        return $actor->can(SalesInvoicePermissions::ISSUE)
            && $salesInvoice->status === 'pending';
    }
    public function print(User $actor, SalesInvoice $salesInvoice): bool { return $actor->can(SalesInvoicePermissions::PRINT); }
}
