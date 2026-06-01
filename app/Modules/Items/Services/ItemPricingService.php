<?php

namespace App\Modules\Items\Services;

class ItemPricingService
{
    public function compute(float|int|string|null $supplierPrice, float|int|string|null $percentage): float
    {
        $basePrice = max(0, (float) ($supplierPrice ?? 0));
        $markup = max(0, (float) ($percentage ?? 0));

        return round($basePrice + ($basePrice * $markup / 100), 2);
    }
}
