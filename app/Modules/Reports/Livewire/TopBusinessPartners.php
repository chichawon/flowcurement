<?php

namespace App\Modules\Reports\Livewire;

use App\Modules\Reports\Services\ReportsService;
use Livewire\Component;
use Livewire\WithPagination;

class TopBusinessPartners extends Component
{
    use WithPagination;

    public string $search = '';
    public string $date_from = '';
    public string $date_to = '';
    public int $perPage = 10;

    public function mount(): void
    {
        abort_unless(auth()->user()?->can('reports.view'), 403);

        $this->date_from = now()->toDateString();
        $this->date_to = now()->toDateString();
    }

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingDateFrom(): void { $this->resetPage(); }
    public function updatingDateTo(): void { $this->resetPage(); }
    public function updatingPerPage(): void { $this->resetPage(); }

    public function searchReports(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->date_from = now()->toDateString();
        $this->date_to = now()->toDateString();
        $this->perPage = 10;
        $this->resetPage();
    }

    public function render(ReportsService $reports)
    {
        return view('modules.reports.livewire.top-business-partners', [
            'partners' => $reports->topBusinessPartners([
                'search' => $this->search,
                'date_from' => $this->date_from,
                'date_to' => $this->date_to,
            ], $this->perPage),
        ]);
    }
}
