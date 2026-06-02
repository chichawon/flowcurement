<?php

namespace App\Modules\Sales\Livewire\Invoices;

use App\Modules\Sales\Models\SalesInvoice;
use App\Modules\Sales\Services\SalesInvoiceService;
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
    public bool $showVoidConfirmation = false;
    public ?int $pendingVoidInvoiceId = null;
    public string $pendingVoidInvoiceNo = '';

    public function mount(): void
    {
        $this->authorize('viewAny', SalesInvoice::class);
    }

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingStatus(): void { $this->resetPage(); }
    public function updatingCurrency(): void { $this->resetPage(); }
    public function updatingDateFrom(): void { $this->resetPage(); }
    public function updatingDateTo(): void { $this->resetPage(); }
    public function updatingPerPage(): void { $this->resetPage(); }

    public function promptVoidInvoice(int $invoiceId): void
    {
        $invoice = SalesInvoice::query()->find($invoiceId);
        if (! $invoice) {
            session()->flash('toast', 'Sales invoice no longer exists.');
            return;
        }

        $this->authorize('delete', $invoice);
        $this->pendingVoidInvoiceId = $invoice->id;
        $this->pendingVoidInvoiceNo = $invoice->sales_invoice_no;
        $this->showVoidConfirmation = true;
    }

    public function voidConfirmedInvoice(): void
    {
        if (! $this->pendingVoidInvoiceId) {
            $this->resetVoidConfirmationState();
            return;
        }

        $invoice = SalesInvoice::query()->find($this->pendingVoidInvoiceId);
        if (! $invoice) {
            session()->flash('toast', 'Sales invoice no longer exists.');
            $this->resetVoidConfirmationState();
            return;
        }

        app(SalesInvoiceService::class)->void($invoice);
        session()->flash('toast', 'Sales invoice cancelled successfully.');
        $this->dispatch('toast', message: 'Sales invoice cancelled successfully.', type: 'success');
        $this->resetVoidConfirmationState();
    }

    public function cancelVoidConfirmation(): void
    {
        $this->resetVoidConfirmationState();
    }

    private function resetVoidConfirmationState(): void
    {
        $this->showVoidConfirmation = false;
        $this->pendingVoidInvoiceId = null;
        $this->pendingVoidInvoiceNo = '';
    }

    public function render()
    {
        return view('modules.sales.invoices.livewire.index', [
            'salesInvoices' => app(SalesInvoiceService::class)->paginate([
                'search' => $this->search,
                'status' => $this->status,
                'currency' => $this->currency,
                'date_from' => $this->date_from,
                'date_to' => $this->date_to,
            ], $this->perPage),
        ]);
    }
}
