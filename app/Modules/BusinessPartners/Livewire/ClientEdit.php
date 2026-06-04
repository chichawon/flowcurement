<?php

namespace App\Modules\BusinessPartners\Livewire;

use App\Modules\BusinessPartners\Livewire\Concerns\ManagesBusinessPartnerForm;
use App\Modules\BusinessPartners\Models\BusinessPartner;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class ClientEdit extends Component
{
    use AuthorizesRequests;
    use ManagesBusinessPartnerForm;

    public function mount(int $businessPartner): void
    {
        $partner = BusinessPartner::query()->clients()->find($businessPartner);

        if (! $partner) {
            $this->redirectRoute('clients.index', navigate: false);

            return;
        }

        $this->authorize('update', $partner);
        $this->fillFromPartner($partner);
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
            'title' => 'Edit Client',
            'submitLabel' => 'Save Client',
            'cancelRoute' => route('clients.index'),
            'partnerType' => $this->partnerType(),
        ]);
    }
}
