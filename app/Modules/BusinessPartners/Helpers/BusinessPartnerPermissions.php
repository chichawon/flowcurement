<?php

namespace App\Modules\BusinessPartners\Helpers;

final class BusinessPartnerPermissions
{
    public const VIEW = 'business-partners.view';
    public const CREATE = 'business-partners.create';
    public const UPDATE = 'business-partners.update';
    public const DELETE = 'business-partners.delete';
    public const RESTORE = 'business-partners.restore';
    public const FORCE_DELETE = 'business-partners.force-delete';

    /**
     * @return array<int, string>
     */
    public static function all(): array
    {
        return [
            self::VIEW,
            self::CREATE,
            self::UPDATE,
            self::DELETE,
            self::RESTORE,
            self::FORCE_DELETE,
        ];
    }
}
