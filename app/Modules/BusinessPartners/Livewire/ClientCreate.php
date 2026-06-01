<?php

namespace App\Modules\BusinessPartners\Livewire;

use App\Modules\BusinessPartners\Livewire\Concerns\ManagesBusinessPartnerForm;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class ClientCreate extends Component
{
    use AuthorizesRequests;
    use ManagesBusinessPartnerForm;

    public function mount(): void
    {
        $this->authorize('create', \App\Modules\BusinessPartners\Models\BusinessPartner::class);
    }

    protected function partnerType(): string
    {
        return 'client';
    }

    protected function indexRoute(): string
    {
        return 'clients';
    }

    public function render()
    {
        return view('modules.business-partners.livewire.form', [
            'title' => 'Create Client',
            'submitLabel' => 'Create Client',
            'cancelRoute' => route('clients.index'),
        ]);
    }
}
