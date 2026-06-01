<?php

namespace App\Modules\Quotations\Livewire;

use App\Modules\Quotations\Models\Quotation;
use App\Modules\Quotations\Services\QuotationService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithPagination;

class QuotationsIndex extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    public string $search = '';

    public string $currency = '';

    public string $date_from = '';

    public string $date_to = '';

    public int $perPage = 10;

    public bool $showDeleteConfirmation = false;

    public ?int $pendingDeleteQuotationId = null;

    public string $pendingDeleteNo = '';

    public function mount(): void
    {
        $this->authorize('viewAny', Quotation::class);
    }

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingCurrency(): void { $this->resetPage(); }
    public function updatingDateFrom(): void { $this->resetPage(); }
    public function updatingDateTo(): void { $this->resetPage(); }
    public function updatingPerPage(): void { $this->resetPage(); }

    public function promptDeleteQuotation(int $quotationId): void
    {
        $quotation = Quotation::query()->find($quotationId);

        if (! $quotation) {
            session()->flash('toast', 'Quotation record was already deleted or no longer exists.');
            $this->resetDeleteConfirmationState();

            return;
        }

        $this->authorize('delete', $quotation);

        $this->pendingDeleteQuotationId = $quotation->id;
        $this->pendingDeleteNo = $quotation->quotation_no;
        $this->showDeleteConfirmation = true;
    }

    public function deleteConfirmedQuotation(): void
    {
        if (! $this->pendingDeleteQuotationId) {
            $this->resetDeleteConfirmationState();

            return;
        }

        $quotation = Quotation::query()->find($this->pendingDeleteQuotationId);

        if (! $quotation) {
            session()->flash('toast', 'Quotation record was already deleted or no longer exists.');
            $this->resetDeleteConfirmationState();

            return;
        }

        $this->authorize('delete', $quotation);

        app(QuotationService::class)->delete($quotation);
        session()->flash('toast', 'Quotation moved to deleted records.');

        $this->resetDeleteConfirmationState();
    }

    public function cancelDeleteConfirmation(): void
    {
        $this->resetDeleteConfirmationState();
    }

    private function resetDeleteConfirmationState(): void
    {
        $this->showDeleteConfirmation = false;
        $this->pendingDeleteQuotationId = null;
        $this->pendingDeleteNo = '';
    }

    public function render()
    {
        return view('modules.quotations.livewire.index', [
            'quotations' => app(QuotationService::class)->paginate([
                'search' => $this->search,
                'currency' => $this->currency,
                'date_from' => $this->date_from,
                'date_to' => $this->date_to,
            ], $this->perPage),
        ]);
    }
}
