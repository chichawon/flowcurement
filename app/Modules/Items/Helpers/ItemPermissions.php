<?php

namespace App\Modules\Items\Helpers;

final class ItemPermissions
{
    public const VIEW = 'items.view';
    public const CREATE = 'items.create';
    public const UPDATE = 'items.update';
    public const DELETE = 'items.delete';
    public const RESTORE = 'items.restore';
    public const FORCE_DELETE = 'items.force-delete';

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
