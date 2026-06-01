<?php

namespace App\Modules\Items\Helpers;

final class ItemImage
{
    public static function url(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        return asset('storage/'.$path);
    }
}
