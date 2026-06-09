<?php

namespace App\Modules\Purchasing\Helpers;

class PurchaseOrderPermissions
{
    public const VIEW = 'purchase-orders.view';
    public const CREATE = 'purchase-orders.create';
    public const UPDATE = 'purchase-orders.update';
    public const DELETE = 'purchase-orders.delete';
    public const PRINT = 'purchase-orders.print';
    public const APPROVE = 'purchase-orders.approve';
    public const CANCEL = 'purchase-orders.cancel';

    public static function all(): array
    {
        return [self::VIEW, self::CREATE, self::UPDATE, self::DELETE, self::PRINT, self::APPROVE, self::CANCEL];
    }
}
