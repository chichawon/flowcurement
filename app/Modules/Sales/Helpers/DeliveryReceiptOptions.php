<?php

namespace App\Modules\Sales\Helpers;

class DeliveryReceiptOptions
{
    public const STATUSES = ['pending', 'billed', 'cancelled'];
    public const REMARKS = ['on_hold', 'completed'];
}
