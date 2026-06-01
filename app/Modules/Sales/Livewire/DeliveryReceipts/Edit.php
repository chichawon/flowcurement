<?php

namespace App\Modules\Sales\Livewire\DeliveryReceipts;

use App\Modules\Sales\Livewire\DeliveryReceipts\Concerns\ManagesDeliveryReceiptForm;
use App\Modules\Sales\Models\DeliveryReceipt;
use App\Modules\Sales\Services\DeliveryReceiptService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class Edit extends Component
{
    use AuthorizesRequests;
    use ManagesDeliveryReceiptForm;

    public function mount(int $deliveryReceipt): void
    {
        $record = DeliveryReceipt::query()->find($deliveryReceipt);
        if (! $record) {
            $this->redirectRoute('sales.delivery-receipts.index', navigate: false);
            return;
        }
        $this->authorize('update', $record);
        $this->fillFromDeliveryReceipt($record);
        $this->bootDeliveryReceiptForm();
    }

    public function render()
    {
        return view('modules.sales.delivery-receipts.livewire.form', [
            'title' => 'Edit Delivery Receipt',
            'submitLabel' => 'Update Delivery Receipt',
            'cancelRoute' => route('sales.delivery-receipts.index'),
            'salesOrders' => app(DeliveryReceiptService::class)->eligibleSalesOrders(),
        ]);
    }
}

