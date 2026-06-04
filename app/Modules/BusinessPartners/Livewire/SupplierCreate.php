<?php

namespace App\Modules\BusinessPartners\Livewire;

use App\Modules\BusinessPartners\Livewire\Concerns\ManagesBusinessPartnerForm;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class SupplierCreate extends Component
{
    use AuthorizesRequests;
    use ManagesBusinessPartnerForm;

    public function mount(): void
    {
        $this->authorize('create', \App\Modules\BusinessPartners\Models\BusinessPartner::class);
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
            'title' => 'Create Supplier',
            'submitLabel' => 'Create Supplier',
            'cancelRoute' => route('suppliers.index'),
            'partnerType' => $this->partnerType(),
        ]);
    }
}
