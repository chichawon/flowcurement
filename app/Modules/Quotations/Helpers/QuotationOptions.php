<?php

namespace App\Modules\Quotations\Helpers;

final class QuotationOptions
{
    public const CURRENCIES = ['php', 'dollar'];
    public const TAX_RATES = [0, 12];
    public const STATUSES = ['draft', 'sent', 'approved', 'rejected', 'expired', 'converted'];
}
