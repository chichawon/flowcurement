<?php

namespace App\Modules\Items\Livewire;

use App\Modules\Items\Models\Item;
use App\Modules\Items\Services\ItemService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithPagination;

class ItemsIndex extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    public string $search = '';

    public string $item_type = '';

    public string $supplier_id = '';

    public string $taxable = '';

    public string $status = '';

    public string $stock_filter = 'all';

    public int $perPage = 10;

    public bool $showDeleteConfirmation = false;

    public ?int $pendingDeleteItemId = null;

    public string $pendingDeleteMode = 'delete';

    public string $pendingDeleteName = '';

    public function mount(): void
    {
        $this->authorize('viewAny', Item::class);
    }

    protected function itemSource(): ?string
    {
        return null;
    }

    protected function routePrefix(): string
    {
        return 'items';
    }

    protected function title(): string
    {
        return 'Items';
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingItemType(): void
    {
        $this->resetPage();
    }

    public function updatingSupplierId(): void
    {
        $this->resetPage();
    }

    public function updatingTaxable(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function setStockFilter(string $filter): void
    {
        if (! in_array($filter, ['all', 'low', 'out'], true)) {
            return;
        }

        $this->stock_filter = $filter;
        $this->resetPage();
    }

    public function promptDeleteItem(int $itemId): void
    {
        $item = Item::query()->find($itemId);

        if (! $item) {
            session()->flash('toast', 'Item record was already deleted or no longer exists.');
            $this->resetDeleteConfirmationState();

            return;
        }

        $this->authorize('delete', $item);

        $this->pendingDeleteItemId = $item->id;
        $this->pendingDeleteMode = 'delete';
        $this->pendingDeleteName = $item->item_name;
        $this->showDeleteConfirmation = true;
    }

    public function promptForceDeleteItem(int $itemId): void
    {
        $item = Item::onlyTrashed()->find($itemId);

        if (! $item) {
            session()->flash('toast', 'Deleted item record no longer exists.');
            $this->resetDeleteConfirmationState();

            return;
        }

        $this->authorize('forceDelete', $item);

        $this->pendingDeleteItemId = $item->id;
        $this->pendingDeleteMode = 'forceDelete';
        $this->pendingDeleteName = $item->item_name;
        $this->showDeleteConfirmation = true;
    }

    public function deleteConfirmedItem(): void
    {
        if (! $this->pendingDeleteItemId) {
            $this->resetDeleteConfirmationState();

            return;
        }

        $item = Item::query()->withTrashed()->find($this->pendingDeleteItemId);

        if (! $item) {
            session()->flash('toast', 'Item record was already deleted or no longer exists.');
            $this->resetDeleteConfirmationState();

            return;
        }

        $items = app(ItemService::class);

        if ($this->pendingDeleteMode === 'forceDelete') {
            $this->authorize('forceDelete', $item);
            $items->forceDelete($item);
            session()->flash('toast', 'Item permanently deleted.');
        } else {
            $this->authorize('delete', $item);
            $items->markDeleted($item);
            session()->flash('toast', 'Item moved to deleted records.');
        }

        $this->resetDeleteConfirmationState();
    }

    public function restoreItem(int $itemId): void
    {
        $item = Item::onlyTrashed()->findOrFail($itemId);
        $this->authorize('restore', $item);
        app(ItemService::class)->restore($item);
        session()->flash('toast', 'Item restored successfully.');
    }

    public function cancelDeleteConfirmation(): void
    {
        $this->resetDeleteConfirmationState();
    }

    private function resetDeleteConfirmationState(): void
    {
        $this->showDeleteConfirmation = false;
        $this->pendingDeleteItemId = null;
        $this->pendingDeleteMode = 'delete';
        $this->pendingDeleteName = '';
    }

    public function render()
    {
        $items = app(ItemService::class);

        return view('modules.items.livewire.items-index', [
            'items' => $items->paginate([
                'search' => $this->search,
                'item_source' => $this->itemSource(),
                'item_type' => $this->item_type,
                'supplier_id' => $this->supplier_id,
                'taxable' => $this->taxable,
                'status' => $this->status,
                'stock_filter' => $this->stock_filter === 'all' ? null : $this->stock_filter,
            ], $this->perPage),
            'suppliers' => $items->suppliers(),
            'itemTypes' => $items->itemTypes(),
            'stockSummary' => $items->stockSummary($this->itemSource()),
            'routePrefix' => $this->routePrefix(),
            'title' => $this->title(),
        ]);
    }
}
