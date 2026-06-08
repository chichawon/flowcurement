<?php

namespace App\Modules\Sales\Livewire\Collections;

use App\Modules\BusinessPartners\Models\BusinessPartner;
use App\Modules\Sales\Models\SalesInvoice;
use Illuminate\Support\Collection;
use Livewire\Component;

class Create extends Component
{
    public string $business_partner_id = '';
    public array $selected_invoice_ids = [];
    public bool $select_all_invoices = false;

    public function mount(): void
    {
        abort_unless(auth()->user()?->can('sales-collections.create'), 403);
    }

    public function updatedBusinessPartnerId(): void
    {
        $this->selected_invoice_ids = [];
        $this->select_all_invoices = false;
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
    }

    public function updatedSelectAllInvoices(bool $selected): void
    {
        $this->selected_invoice_ids = $selected
            ? $this->unpaidInvoices()->pluck('id')->map(fn (int $id): string => (string) $id)->all()
            : [];
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
            ->get(['id', 'company_name', 'company_code', 'contact_person', 'contact_no']);
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
                'total_amount',
                'balance_amount',
                'status',
            ]);
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
}
