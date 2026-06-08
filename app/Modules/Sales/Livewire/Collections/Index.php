<?php

namespace App\Modules\Sales\Livewire\Collections;

use App\Modules\Sales\Services\SalesCollectionService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    public string $search = '';
    public string $status = '';
    public string $date_from = '';
    public string $date_to = '';
    public int $perPage = 10;

    public function mount(): void
    {
        abort_unless(auth()->user()?->can('sales-collections.view'), 403);
    }

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingStatus(): void { $this->resetPage(); }
    public function updatingDateFrom(): void { $this->resetPage(); }
    public function updatingDateTo(): void { $this->resetPage(); }
    public function updatingPerPage(): void { $this->resetPage(); }

    public function render()
    {
        $filters = [
            'search' => $this->search,
            'status' => $this->status,
            'date_from' => $this->date_from,
            'date_to' => $this->date_to,
        ];

        return view('modules.sales.collections.livewire.index', [
            'collections' => app(SalesCollectionService::class)->paginate($filters, $this->perPage),
        ]);
    }
}
