<?php

namespace App\Modules\Purchasing\Helpers;

class PurchaseOrderOptions
{
    public const STATUSES = ['pending', 'partially_received', 'received', 'cancelled'];
    public const CURRENCIES = ['php', 'dollar'];
    public const TAX_RATES = ['0', '12'];
}
