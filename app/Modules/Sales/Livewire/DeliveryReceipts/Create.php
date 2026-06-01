<?php

namespace App\Modules\Sales\Livewire\DeliveryReceipts;

use App\Modules\Sales\Livewire\DeliveryReceipts\Concerns\ManagesDeliveryReceiptForm;
use App\Modules\Sales\Models\DeliveryReceipt;
use App\Modules\Sales\Services\DeliveryReceiptService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class Create extends Component
{
    use AuthorizesRequests;
    use ManagesDeliveryReceiptForm;

    public function mount(): void
    {
        $this->authorize('create', DeliveryReceipt::class);
        $this->bootDeliveryReceiptForm();
    }

    public function render()
    {
        return view('modules.sales.delivery-receipts.livewire.form', [
            'title' => 'Create Delivery Receipt',
            'submitLabel' => 'Save Delivery Receipt',
            'cancelRoute' => route('sales.delivery-receipts.index'),
            'salesOrders' => app(DeliveryReceiptService::class)->eligibleSalesOrders(),
        ]);
    }
}

