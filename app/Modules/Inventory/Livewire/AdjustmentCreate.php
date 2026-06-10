<?php

namespace App\Modules\Inventory\Livewire;

use App\Modules\Inventory\Services\InventoryService;
use App\Modules\Items\Models\Item;
use Illuminate\Validation\Rule;
use Livewire\Component;

class AdjustmentCreate extends Component
{
    public string $adjustment_date = '';
    public string $item_id = '';
    public string $adjustment_type = 'add';
    public int $quantity = 1;
    public string $reason = '';
    public string $remarks = '';
    public int $current_stock = 0;
    public int $new_stock = 0;

    public function mount(): void
    {
        abort_unless(auth()->user()?->can('inventory.update') || auth()->user()?->can('inventory.create'), 403);

        $this->adjustment_date = now()->toDateString();
        $this->recalculateStock();
    }

    public function updatedItemId(): void
    {
        $this->loadCurrentStock();
        $this->recalculateStock();
    }

    public function updatedAdjustmentType(): void
    {
        $this->recalculateStock();
    }

    public function updatedQuantity(): void
    {
        $this->quantity = max((int) $this->quantity, 0);
        $this->recalculateStock();
    }

    public function save(InventoryService $inventory): mixed
    {
        abort_unless(auth()->user()?->can('inventory.update') || auth()->user()?->can('inventory.create'), 403);

        $validated = $this->validate([
            'adjustment_date' => ['required', 'date'],
            'item_id' => ['required', 'integer', 'exists:items,id'],
            'adjustment_type' => ['required', Rule::in(['add', 'deduct'])],
            'quantity' => ['required', 'integer', 'min:1'],
            'reason' => ['nullable', 'max:255'],
            'remarks' => ['nullable', 'max:1000'],
        ]);

        $adjustment = $inventory->adjustStock($validated);

        session()->flash('toast', [
            'type' => 'success',
            'message' => 'Inventory adjustment saved: '.$adjustment->adjustment_no,
        ]);

        return redirect()->route('inventory.movements.index');
    }

    public function render(InventoryService $inventory)
    {
        return view('modules.inventory.livewire.adjustment-create', [
            'items' => $inventory->adjustmentItems(),
        ]);
    }

    private function loadCurrentStock(): void
    {
        if ($this->item_id === '') {
            $this->current_stock = 0;
            return;
        }

        $item = Item::query()->find((int) $this->item_id, ['id', 'available_stock']);

        $this->current_stock = (int) ($item?->available_stock ?? 0);
    }

    private function recalculateStock(): void
    {
        $quantity = max((int) $this->quantity, 0);
        $this->new_stock = $this->adjustment_type === 'deduct'
            ? max($this->current_stock - $quantity, 0)
            : $this->current_stock + $quantity;
    }
}
