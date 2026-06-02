<?php

namespace App\Modules\Sales\Helpers;

class SalesInvoicePermissions
{
    public const VIEW = 'sales-invoices.view';
    public const CREATE = 'sales-invoices.create';
    public const UPDATE = 'sales-invoices.update';
    public const DELETE = 'sales-invoices.delete';
    public const PRINT = 'sales-invoices.print';

    public static function all(): array
    {
        return [
            self::VIEW,
            self::CREATE,
            self::UPDATE,
            self::DELETE,
            self::PRINT,
        ];
    }
}
