<?php

namespace App\Modules\Sales\Livewire\Orders;

use App\Modules\Sales\Livewire\Orders\Concerns\ManagesSalesOrderForm;
use App\Modules\Sales\Models\SalesOrder;
use App\Modules\Sales\Services\SalesOrderService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use AuthorizesRequests;
    use ManagesSalesOrderForm;
    use WithFileUploads;

    public function mount(): void
    {
        $this->authorize('create', SalesOrder::class);
        $this->bootSalesOrderForm();
    }

    public function render()
    {
        $service = app(SalesOrderService::class);

        return view('modules.sales.orders.livewire.form', [
            'title' => 'Create Sales Order',
            'submitLabel' => 'Save Sales Order',
            'cancelRoute' => route('sales.orders.index'),
            'clients' => $service->clients(),
            'availableItems' => $service->activeItems(),
            'unitMeasures' => $service->unitMeasures(),
            'quotations' => $service->sourceQuotations(),
        ]);
    }
}
