<?php

namespace App\Modules\Sales\Livewire\Orders;

use App\Modules\Sales\Models\SalesOrder;
use App\Modules\Sales\Services\SalesOrderService;
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
    public array $expandedRows = [];
    public bool $showDeleteConfirmation = false;
    public ?int $pendingDeleteSalesOrderId = null;
    public string $pendingDeleteNo = '';

    public function mount(): void
    {
        $this->authorize('viewAny', SalesOrder::class);
    }

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingStatus(): void { $this->resetPage(); }
    public function updatingCurrency(): void { $this->resetPage(); }
    public function updatingDateFrom(): void { $this->resetPage(); }
    public function updatingDateTo(): void { $this->resetPage(); }
    public function updatingPerPage(): void { $this->resetPage(); }

    public function setStatusFilter(string $status = ''): void
    {
        $this->status = $status;
        $this->resetPage();
    }

    public function toggleItemsDetails(int $salesOrderId): void
    {
        if (in_array($salesOrderId, $this->expandedRows, true)) {
            $this->expandedRows = array_values(array_filter(
                $this->expandedRows,
                fn (int $id): bool => $id !== $salesOrderId
            ));
            return;
        }

        $this->expandedRows[] = $salesOrderId;
    }

    public function promptDeleteSalesOrder(int $salesOrderId): void
    {
        $salesOrder = SalesOrder::query()->find($salesOrderId);
        if (! $salesOrder) {
            session()->flash('toast', 'Sales order no longer exists.');
            return;
        }
        $this->authorize('delete', $salesOrder);
        $this->pendingDeleteSalesOrderId = $salesOrder->id;
        $this->pendingDeleteNo = $salesOrder->sales_order_no;
        $this->showDeleteConfirmation = true;
    }

    public function deleteConfirmedSalesOrder(): void
    {
        if (! $this->pendingDeleteSalesOrderId) {
            $this->resetDeleteConfirmationState();
            return;
        }
        $salesOrder = SalesOrder::query()->find($this->pendingDeleteSalesOrderId);
        if (! $salesOrder) {
            session()->flash('toast', 'Sales order no longer exists.');
            $this->resetDeleteConfirmationState();
            return;
        }
        app(SalesOrderService::class)->delete($salesOrder);
        session()->flash('toast', 'Sales order moved to deleted records.');
        $this->resetDeleteConfirmationState();
    }

    public function cancelDeleteConfirmation(): void
    {
        $this->resetDeleteConfirmationState();
    }

    private function resetDeleteConfirmationState(): void
    {
        $this->showDeleteConfirmation = false;
        $this->pendingDeleteSalesOrderId = null;
        $this->pendingDeleteNo = '';
    }

    public function render()
    {
        $service = app(SalesOrderService::class);
        $filters = [
            'search' => $this->search,
            'status' => $this->status,
            'currency' => $this->currency,
            'date_from' => $this->date_from,
            'date_to' => $this->date_to,
        ];

        return view('modules.sales.orders.livewire.index', [
            'salesOrders' => $service->paginate($filters, $this->perPage, $this->expandedRows),
            'statusCounts' => $service->statusCounts($filters),
        ]);
    }
}
