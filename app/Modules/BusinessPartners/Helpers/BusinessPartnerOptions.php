<?php

namespace App\Modules\BusinessPartners\Helpers;

final class BusinessPartnerOptions
{
    public const TYPES = ['client', 'supplier'];
    public const UNDER_PESA = ['yes', 'no'];
    public const VATABLE = ['non_vat', 'with_vat'];
    public const TERMS = [30, 60, 90];
    public const STATUSES = ['active', 'inactive'];
}
