<?php

namespace App\Modules\BusinessPartners\Livewire;

class ClientIndex extends BusinessPartnerIndex
{
    protected function partnerType(): string
    {
        return 'client';
    }

    protected function routePrefix(): string
    {
        return 'clients';
    }

    protected function title(): string
    {
        return 'Clients';
    }
}
