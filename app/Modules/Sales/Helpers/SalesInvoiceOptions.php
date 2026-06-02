<?php

namespace App\Modules\Sales\Helpers;

class SalesInvoiceOptions
{
    public const STATUSES = ['pending', 'issued', 'partial_paid', 'paid', 'void'];
    public const CURRENCIES = ['php', 'dollar'];
    public const TAX_RATES = [0, 12];
}
