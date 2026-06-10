<?php

namespace App\Modules\Inventory\Livewire;

use App\Modules\Inventory\Services\InventoryService;
use Livewire\Component;
use Livewire\WithPagination;

class StockIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $item_type = '';
    public string $supplier_id = '';
    public string $stock_filter = 'all';
    public int $perPage = 10;

    public function mount(): void
    {
        abort_unless(auth()->user()?->can('inventory.view'), 403);
    }

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingItemType(): void { $this->resetPage(); }
    public function updatingSupplierId(): void { $this->resetPage(); }
    public function updatingPerPage(): void { $this->resetPage(); }

    public function setStockFilter(string $filter): void
    {
        $this->stock_filter = in_array($filter, ['all', 'low', 'out'], true) ? $filter : 'all';
        $this->resetPage();
    }

    public function render(InventoryService $inventory)
    {
        return view('modules.inventory.livewire.stock-index', [
            'items' => $inventory->stockList([
                'search' => $this->search,
                'item_type' => $this->item_type,
                'supplier_id' => $this->supplier_id,
                'stock_filter' => $this->stock_filter === 'all' ? null : $this->stock_filter,
            ], $this->perPage),
            'stockSummary' => $inventory->stockSummary(),
            'itemTypes' => $inventory->itemTypes(),
            'suppliers' => $inventory->suppliers(),
        ]);
    }
}
