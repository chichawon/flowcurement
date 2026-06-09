<?php

namespace App\Modules\Purchasing\Livewire\Orders;

use App\Modules\Purchasing\Helpers\PurchaseOrderOptions;
use App\Modules\Purchasing\Models\PurchaseOrder;
use App\Modules\Purchasing\Services\PurchaseOrderService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    public string $search = '';
    public string $status = '';
    public string $currency = '';
    public string $date_from = '';
    public string $date_to = '';
    public int $perPage = 10;

    protected $queryString = ['search' => ['except' => ''], 'status' => ['except' => ''], 'currency' => ['except' => '']];

    public function mount(): void
    {
        $this->authorize('viewAny', PurchaseOrder::class);
    }

    public function updating($property): void
    {
        if (in_array($property, ['search', 'status', 'currency', 'date_from', 'date_to', 'perPage'], true)) {
            $this->resetPage();
        }
    }

    public function cancel(int $id): void
    {
        $order = PurchaseOrder::query()->findOrFail($id);
        app(PurchaseOrderService::class)->cancel($order);
        session()->flash('toast', 'Purchase order cancelled successfully.');
    }

    public function render()
    {
        return view('modules.purchasing.orders.livewire.index', [
            'orders' => app(PurchaseOrderService::class)->paginate($this->filters(), $this->perPage),
            'statuses' => PurchaseOrderOptions::STATUSES,
            'currencies' => PurchaseOrderOptions::CURRENCIES,
        ]);
    }

    private function filters(): array
    {
        return [
            'search' => $this->search,
            'status' => $this->status,
            'currency' => $this->currency,
            'date_from' => $this->date_from,
            'date_to' => $this->date_to,
        ];
    }
}
