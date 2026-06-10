<?php

namespace App\Modules\Inventory\Livewire;

use App\Modules\Inventory\Services\InventoryService;
use Livewire\Component;
use Livewire\WithPagination;

class MovementsIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $movement_type = '';
    public string $date_from = '';
    public string $date_to = '';
    public int $perPage = 10;

    public function mount(): void
    {
        abort_unless(auth()->user()?->can('inventory.view'), 403);

        $this->date_from = now()->startOfMonth()->toDateString();
        $this->date_to = now()->toDateString();
    }

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingMovementType(): void { $this->resetPage(); }
    public function updatingDateFrom(): void { $this->resetPage(); }
    public function updatingDateTo(): void { $this->resetPage(); }
    public function updatingPerPage(): void { $this->resetPage(); }

    public function render(InventoryService $inventory)
    {
        return view('modules.inventory.livewire.movements-index', [
            'movements' => $inventory->movements([
                'search' => $this->search,
                'movement_type' => $this->movement_type,
                'date_from' => $this->date_from,
                'date_to' => $this->date_to,
            ], $this->perPage),
        ]);
    }
}
