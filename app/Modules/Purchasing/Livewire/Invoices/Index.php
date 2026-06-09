<?php

namespace App\Modules\Purchasing\Livewire\Invoices;

use App\Modules\Purchasing\Helpers\PurchaseInvoiceOptions;
use App\Modules\Purchasing\Models\PurchaseInvoice;
use App\Modules\Purchasing\Services\PurchaseInvoiceService;
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

    public function mount(): void
    {
        $this->authorize('viewAny', PurchaseInvoice::class);
    }

    public function updating($property): void
    {
        if (in_array($property, ['search', 'status', 'currency', 'date_from', 'date_to', 'perPage'], true)) {
            $this->resetPage();
        }
    }

    public function cancel(int $id): void
    {
        $invoice = PurchaseInvoice::query()->findOrFail($id);
        app(PurchaseInvoiceService::class)->cancel($invoice);
        session()->flash('toast', 'Purchase invoice cancelled successfully.');
    }

    public function render()
    {
        return view('modules.purchasing.invoices.livewire.index', [
            'invoices' => app(PurchaseInvoiceService::class)->paginate($this->filters(), $this->perPage),
            'statuses' => PurchaseInvoiceOptions::STATUSES,
            'currencies' => PurchaseInvoiceOptions::CURRENCIES,
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
