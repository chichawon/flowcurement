<?php

namespace App\Modules\Sales\Policies;

use App\Models\User;
use App\Modules\Sales\Helpers\DeliveryReceiptPermissions;
use App\Modules\Sales\Models\DeliveryReceipt;

class DeliveryReceiptPolicy
{
    public function viewAny(User $actor): bool
    {
        return $actor->can(DeliveryReceiptPermissions::VIEW) || $actor->can('sales-orders.view');
    }

    public function view(User $actor, DeliveryReceipt $deliveryReceipt): bool
    {
        return $actor->can(DeliveryReceiptPermissions::VIEW) || $actor->can('sales-orders.view');
    }

    public function create(User $actor): bool
    {
        return $actor->can(DeliveryReceiptPermissions::CREATE) || $actor->can('sales-orders.create');
    }

    public function update(User $actor, DeliveryReceipt $deliveryReceipt): bool
    {
        return $actor->can(DeliveryReceiptPermissions::UPDATE) || $actor->can('sales-orders.update');
    }

    public function cancel(User $actor, DeliveryReceipt $deliveryReceipt): bool
    {
        return $actor->can(DeliveryReceiptPermissions::CANCEL) || $actor->can('sales-orders.cancel');
    }

    public function print(User $actor, DeliveryReceipt $deliveryReceipt): bool
    {
        return $actor->can(DeliveryReceiptPermissions::PRINT) || $actor->can('sales-orders.print');
    }
}
