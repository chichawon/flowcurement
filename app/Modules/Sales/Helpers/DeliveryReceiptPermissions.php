<?php

namespace App\Modules\Sales\Helpers;

class DeliveryReceiptPermissions
{
    public const VIEW = 'delivery-receipts.view';
    public const CREATE = 'delivery-receipts.create';
    public const UPDATE = 'delivery-receipts.update';
    public const CANCEL = 'delivery-receipts.cancel';
    public const PRINT = 'delivery-receipts.print';

    public static function all(): array
    {
        return [
            self::VIEW,
            self::CREATE,
            self::UPDATE,
            self::CANCEL,
            self::PRINT,
        ];
    }
}

