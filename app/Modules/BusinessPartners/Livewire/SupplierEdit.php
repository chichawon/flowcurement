<?php

namespace App\Modules\BusinessPartners\Livewire;

use App\Modules\BusinessPartners\Livewire\Concerns\ManagesBusinessPartnerForm;
use App\Modules\BusinessPartners\Models\BusinessPartner;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class SupplierEdit extends Component
{
    use AuthorizesRequests;
    use ManagesBusinessPartnerForm;

    public function mount(int $businessPartner): void
    {
        $partner = BusinessPartner::query()->suppliers()->find($businessPartner);

        if (! $partner) {
            $this->redirectRoute('suppliers.index', navigate: false);

            return;
        }

        $this->authorize('update', $partner);
        $this->fillFromPartner($partner);
    }

    protected function partnerType(): string
    {
        return 'supplier';
    }

    protected function indexRoute(): string
    {
        return 'suppliers';
    }

    public function render()
    {
        return view('modules.business-partners.livewire.form', [
            'title' => 'Edit Supplier',
            'submitLabel' => 'Save Supplier',
            'cancelRoute' => route('suppliers.index'),
        ]);
    }
}
