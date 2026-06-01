<?php

namespace App\Modules\Sales\Helpers;

class SalesOrderPermissions
{
    public const VIEW = 'sales-orders.view';
    public const CREATE = 'sales-orders.create';
    public const UPDATE = 'sales-orders.update';
    public const DELETE = 'sales-orders.delete';
    public const APPROVE = 'sales-orders.approve';
    public const CANCEL = 'sales-orders.cancel';
    public const PRINT = 'sales-orders.print';

    public static function all(): array
    {
        return [
            self::VIEW,
            self::CREATE,
            self::UPDATE,
            self::DELETE,
            self::APPROVE,
            self::CANCEL,
            self::PRINT,
        ];
    }
}
