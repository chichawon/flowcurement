<?php

namespace App\Modules\Sales\Helpers;

class SalesInvoiceOptions
{
    public const STATUSES = ['unpaid', 'paid', 'collected', 'cancelled'];
    public const CURRENCIES = ['php', 'dollar'];
    public const TAX_RATES = [0, 12];
    public const WITHHOLDING_TAX_RATES = [0, 1, 2, 5, 10];
}
