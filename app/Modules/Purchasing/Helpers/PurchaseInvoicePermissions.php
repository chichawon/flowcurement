<?php

namespace App\Modules\Purchasing\Helpers;

class PurchaseInvoicePermissions
{
    public const VIEW = 'purchase-invoices.view';
    public const CREATE = 'purchase-invoices.create';
    public const UPDATE = 'purchase-invoices.update';
    public const DELETE = 'purchase-invoices.delete';
    public const PRINT = 'purchase-invoices.print';
    public const CANCEL = 'purchase-invoices.cancel';

    public static function all(): array
    {
        return [self::VIEW, self::CREATE, self::UPDATE, self::DELETE, self::PRINT, self::CANCEL];
    }
}
