<?php

namespace App\Modules\Quotations\Helpers;

final class QuotationPermissions
{
    public const VIEW = 'quotations.view';
    public const CREATE = 'quotations.create';
    public const UPDATE = 'quotations.update';
    public const DELETE = 'quotations.delete';
    public const PRINT = 'quotations.print';
    public const APPROVE = 'quotations.approve';
    public const CONVERT = 'quotations.convert';

    public static function all(): array
    {
        return [self::VIEW, self::CREATE, self::UPDATE, self::DELETE, self::PRINT, self::APPROVE, self::CONVERT];
    }
}
