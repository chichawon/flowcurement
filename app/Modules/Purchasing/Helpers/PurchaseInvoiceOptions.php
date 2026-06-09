<?php

namespace App\Modules\Purchasing\Helpers;

class PurchaseInvoiceOptions
{
    public const STATUSES = ['unpaid', 'partial', 'paid', 'cancelled'];
    public const CURRENCIES = ['php', 'dollar'];
    public const TAX_RATES = ['0', '12'];
}
