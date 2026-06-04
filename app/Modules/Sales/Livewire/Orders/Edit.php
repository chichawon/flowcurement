<?php

namespace App\Modules\Sales\Livewire\Orders;

use App\Modules\Sales\Livewire\Orders\Concerns\ManagesSalesOrderForm;
use App\Modules\Sales\Models\SalesOrder;
use App\Modules\Sales\Services\SalesOrderService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithFileUploads;

class Edit extends Component
{
    use AuthorizesRequests;
    use ManagesSalesOrderForm;
    use WithFileUploads;

    public function mount(int $salesOrder): void
    {
        $record = SalesOrder::query()->with('items.item')->find($salesOrder);
        if (! $record) {
            $this->redirectRoute('sales.orders.index', navigate: false);
            return;
        }
        $this->authorize('update', $record);
        $this->fillFromSalesOrder($record);
        $this->bootSalesOrderForm();
    }

    public function render()
    {
        $service = app(SalesOrderService::class);

        return view('modules.sales.orders.livewire.form', [
            'title' => 'Edit Sales Order',
            'submitLabel' => 'Update Sales Order',
            'cancelRoute' => route('sales.orders.index'),
            'clients' => $service->clients(),
            'availableItems' => $service->activeItems(),
            'unitMeasures' => $service->unitMeasures(),
            'quotations' => $service->sourceQuotations($this->salesOrderRecord?->quotation_id),
        ]);
    }
}
