<?php

namespace App\Modules\Quotations\Livewire;

use App\Modules\Quotations\Livewire\Concerns\ManagesQuotationForm;
use App\Modules\Quotations\Models\Quotation;
use App\Modules\Quotations\Services\QuotationService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class QuotationCreate extends Component
{
    use AuthorizesRequests;
    use ManagesQuotationForm;

    public function mount(): void
    {
        $this->authorize('create', Quotation::class);
        $this->quotation_no = app(QuotationService::class)->nextQuotationNo();
        $this->bootQuotationForm();
    }

    public function render()
    {
        $service = app(QuotationService::class);

        return view('modules.quotations.livewire.form', [
            'title' => 'Quotation Details',
            'submitLabel' => 'Save Quotation',
            'cancelRoute' => route('quotations.index'),
            'clients' => $service->clients(),
            'availableItems' => $service->activeItems(),
            'unitMeasures' => $service->unitMeasures(),
        ]);
    }
}
