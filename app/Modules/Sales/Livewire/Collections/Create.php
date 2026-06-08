<?php

namespace App\Modules\Sales\Livewire\Collections;

use App\Modules\BusinessPartners\Models\BusinessPartner;
use App\Modules\Sales\Models\SalesInvoice;
use App\Modules\Sales\Services\SalesCollectionService;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Url;
use Livewire\Component;

class Create extends Component
{
    #[Url(as: 'company', except: '')]
    public string $business_partner_id = '';

    #[Url(as: 'invoices', except: '')]
    public string $invoice_ids = '';

    #[Url(as: 'step', except: 'select')]
    public string $collection_step = 'select';

    public array $selected_invoice_ids = [];
    public bool $select_all_invoices = false;
    public bool $show_collection_details = false;
    public string $bank_name = '';
    public string $check_number = '';
    public string $check_date = '';
    public string $check_amount = '';
    public string $collection_receipt_no = '';
    public string $collection_receipt_date = '';
    public string $collection_receipt_amount = '';

    public function mount(): void
    {
        abort_unless(auth()->user()?->can('sales-collections.create'), 403);
        $this->check_date = now()->toDateString();
        $this->collection_receipt_date = now()->toDateString();
        $this->restoreSelectionFromUrl();
    }

    public function updatedBusinessPartnerId(): void
    {
        $this->selected_invoice_ids = [];
        $this->select_all_invoices = false;
        $this->show_collection_details = false;
        $this->collection_step = 'select';
        $this->syncInvoiceSelectionToUrl();
    }

    public function updatedSelectedInvoiceIds(): void
    {
        $visibleIds = $this->unpaidInvoices()->pluck('id')->map(fn (int $id): string => (string) $id)->all();
        $this->selected_invoice_ids = collect($this->selected_invoice_ids)
            ->map(fn ($id): string => (string) $id)
            ->intersect($visibleIds)
            ->values()
            ->all();
        $this->select_all_invoices = $visibleIds !== [] && count($this->selected_invoice_ids) === count($visibleIds);
        $this->syncInvoiceSelectionToUrl();
    }

    public function updatedSelectAllInvoices(bool $selected): void
    {
        $this->selected_invoice_ids = $selected
            ? $this->unpaidInvoices()->pluck('id')->map(fn (int $id): string => (string) $id)->all()
            : [];
        $this->syncInvoiceSelectionToUrl();
    }

    public function clientsWithUnpaidInvoices(): Collection
    {
        return BusinessPartner::query()
            ->clients()
            ->whereIn('id', SalesInvoice::query()
                ->select('business_partner_id')
                ->where('status', 'unpaid')
                ->whereNotNull('business_partner_id'))
            ->orderBy('company_name')
            ->get(['id', 'company_name', 'company_code', 'contact_person', 'contact_no', 'agent_name']);
    }

    public function unpaidInvoices(): Collection
    {
        if ($this->business_partner_id === '') {
            return collect();
        }

        return SalesInvoice::query()
            ->where('business_partner_id', $this->business_partner_id)
            ->where('status', 'unpaid')
            ->latest('invoice_date')
            ->latest('id')
            ->get([
                'id',
                'sales_invoice_no',
                'invoice_date',
                'delivery_receipt_no',
                'sales_order_no',
                'customer_po',
                'company_name',
                'currency',
                'subtotal',
                'tax_amount',
                'withholding_tax_amount',
                'total_amount',
                'amount_paid',
                'balance_amount',
                'status',
            ]);
    }

    public function openCollectionDetails(): void
    {
        $this->resetErrorBag('selected_invoice_ids');

        if ($this->business_partner_id === '' || $this->selected_invoice_ids === []) {
            $this->addError('selected_invoice_ids', 'Select at least one unpaid sales invoice to collect.');

            return;
        }

        $this->updatedSelectedInvoiceIds();

        if ($this->selected_invoice_ids === []) {
            $this->addError('selected_invoice_ids', 'Selected invoices are no longer available for collection.');

            return;
        }

        $this->show_collection_details = true;
        $this->collection_step = 'details';
        $this->syncInvoiceSelectionToUrl();
    }

    public function backToInvoiceSelection(): void
    {
        $this->show_collection_details = false;
        $this->collection_step = 'select';
    }

    public function removeSelectedInvoice(string|int $invoiceId): void
    {
        $this->selected_invoice_ids = collect($this->selected_invoice_ids)
            ->map(fn ($id): string => (string) $id)
            ->reject(fn (string $id): bool => $id === (string) $invoiceId)
            ->values()
            ->all();

        $this->updatedSelectedInvoiceIds();

        if ($this->selected_invoice_ids === []) {
            $this->show_collection_details = false;
            $this->collection_step = 'select';
        }
    }

    public function save(): mixed
    {
        abort_unless(auth()->user()?->can('sales-collections.create'), 403);

        $payload = $this->validate([
            'business_partner_id' => ['required', 'integer', 'exists:business_partners,id'],
            'selected_invoice_ids' => ['required', 'array', 'min:1'],
            'selected_invoice_ids.*' => ['required', 'integer', 'exists:sales_invoices,id'],
            'bank_name' => ['required', 'string', 'max:255'],
            'check_number' => ['required', 'string', 'max:255'],
            'check_date' => ['required', 'date'],
            'check_amount' => ['required', 'numeric', 'min:0.01'],
            'collection_receipt_no' => ['required', 'string', 'max:255', Rule::unique('sales_collections', 'collection_receipt_no')],
            'collection_receipt_date' => ['required', 'date'],
            'collection_receipt_amount' => ['required', 'numeric', 'min:0.01'],
        ], [], [
            'business_partner_id' => 'company name',
            'selected_invoice_ids' => 'selected sales invoices',
            'bank_name' => 'bank name',
            'check_number' => 'check number',
            'check_date' => 'check date',
            'check_amount' => 'check amount',
            'collection_receipt_no' => 'collection receipt no.',
            'collection_receipt_date' => 'collection receipt date',
            'collection_receipt_amount' => 'collection receipt amount',
        ]);

        $payload['selected_invoice_ids'] = collect($payload['selected_invoice_ids'])
            ->map(fn ($id): int => (int) $id)
            ->unique()
            ->values()
            ->all();

        try {
            app(SalesCollectionService::class)->create($payload);
        } catch (\Throwable $exception) {
            $this->addError('collection_receipt_amount', $exception->getMessage());

            return null;
        }

        return redirect()->route('sales.collections.index')->with('toast', 'Sales collection saved successfully.');
    }

    public function selectedInvoices(): Collection
    {
        if ($this->selected_invoice_ids === []) {
            return collect();
        }

        return $this->unpaidInvoices()
            ->whereIn('id', collect($this->selected_invoice_ids)->map(fn ($id): int => (int) $id)->all())
            ->values();
    }

    public function render()
    {
        $clients = $this->clientsWithUnpaidInvoices();
        $invoices = $this->unpaidInvoices();
        $selectedIds = collect($this->selected_invoice_ids)->map(fn ($id): int => (int) $id)->all();
        $selectedInvoices = $selectedIds === []
            ? collect()
            : $invoices->whereIn('id', $selectedIds)->values();

        return view('modules.sales.collections.livewire.create', [
            'clients' => $clients,
            'invoices' => $invoices,
            'selectedInvoices' => $selectedInvoices,
            'selectedTotalBalance' => $selectedInvoices->sum(fn (SalesInvoice $invoice): float => (float) $invoice->balance_amount),
        ]);
    }

    private function restoreSelectionFromUrl(): void
    {
        $this->selected_invoice_ids = collect(explode(',', $this->invoice_ids))
            ->map(fn (string $id): string => trim($id))
            ->filter(fn (string $id): bool => ctype_digit($id))
            ->unique()
            ->values()
            ->all();

        if ($this->business_partner_id !== '' && $this->selected_invoice_ids !== []) {
            $visibleIds = $this->unpaidInvoices()->pluck('id')->map(fn (int $id): string => (string) $id)->all();
            $this->selected_invoice_ids = collect($this->selected_invoice_ids)
                ->intersect($visibleIds)
                ->values()
                ->all();
            $this->syncInvoiceSelectionToUrl();
        }

        $this->show_collection_details = $this->collection_step === 'details'
            && $this->business_partner_id !== ''
            && $this->selected_invoice_ids !== [];

        if (! $this->show_collection_details) {
            $this->collection_step = 'select';
        }
    }

    private function syncInvoiceSelectionToUrl(): void
    {
        $this->invoice_ids = collect($this->selected_invoice_ids)
            ->map(fn ($id): string => (string) $id)
            ->filter(fn (string $id): bool => $id !== '')
            ->unique()
            ->values()
            ->implode(',');
    }
}
