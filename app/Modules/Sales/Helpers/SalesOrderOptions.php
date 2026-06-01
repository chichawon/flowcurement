<?php

namespace App\Modules\Sales\Helpers;

class SalesOrderOptions
{
    public const STATUSES = ['pending', 'partial', 'served'];
    public const CURRENCIES = ['php', 'dollar'];
    public const TAX_RATES = ['0', '12'];
}
