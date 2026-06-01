<?php

namespace App\Modules\BusinessPartners\Livewire;

class SupplierIndex extends BusinessPartnerIndex
{
    protected function partnerType(): string
    {
        return 'supplier';
    }

    protected function routePrefix(): string
    {
        return 'suppliers';
    }

    protected function title(): string
    {
        return 'Suppliers';
    }
}
