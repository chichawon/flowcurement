<?php

namespace App\Modules\Quotations\Livewire;

use App\Modules\Quotations\Livewire\Concerns\ManagesQuotationForm;
use App\Modules\Quotations\Models\Quotation;
use App\Modules\Quotations\Services\QuotationService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithFileUploads;

class QuotationEdit extends Component
{
    use AuthorizesRequests;
    use ManagesQuotationForm;
    use WithFileUploads;

    public function mount(int $quotation): void
    {
        $record = Quotation::query()->with(['items.item'])->find($quotation);

        if (! $record) {
            $this->redirectRoute('quotations.index', navigate: false);

            return;
        }

        $this->authorize('update', $record);
        $this->fillFromQuotation($record);
        $this->bootQuotationForm();
    }

    public function render()
    {
        $service = app(QuotationService::class);

        return view('modules.quotations.livewire.form', [
            'title' => 'Quotation Details',
            'submitLabel' => 'Update Quotation',
            'cancelRoute' => route('quotations.index'),
            'clients' => $service->clients(),
            'availableItems' => $service->activeItems(),
            'unitMeasures' => $service->unitMeasures(),
        ]);
    }
}
