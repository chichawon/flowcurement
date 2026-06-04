<?php

namespace App\Http\Controllers;

use App\Modules\Sales\Models\SalesInvoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class SalesInvoicePageController extends Controller
{
    public function index(): View
    {
        return view('modules.sales.invoices.index');
    }

    public function create(): View
    {
        return view('modules.sales.invoices.create');
    }

    public function show(int $salesInvoice): View|RedirectResponse
    {
        $salesInvoice = SalesInvoice::query()
            ->with(['items.unitMeasure', 'items.item:id,item_name,item_code,item_image', 'businessPartner:id,company_name', 'salesOrder:id,sales_order_no', 'deliveryReceipt:id,delivery_receipt_no'])
            ->find($salesInvoice);

        if (! $salesInvoice) {
            return redirect()->route('sales.invoices.index')->with('toast', 'Sales invoice no longer exists.');
        }

        Gate::authorize('view', $salesInvoice);

        return view('modules.sales.invoices.show', ['salesInvoice' => $salesInvoice]);
    }

    public function print(int $salesInvoice): Response|RedirectResponse
    {
        $salesInvoice = SalesInvoice::query()->find($salesInvoice);

        if (! $salesInvoice) {
            return redirect()->route('sales.invoices.index')->with('toast', 'Sales invoice no longer exists.');
        }

        $this->loadSalesInvoice($salesInvoice);
        Gate::authorize('print', $salesInvoice);

        return Pdf::loadView('modules.sales.invoices.pdf', [
            'salesInvoice' => $salesInvoice,
        ])
            ->setPaper('a4')
            ->stream($salesInvoice->sales_invoice_no.'.pdf');
    }

    public function edit(int $salesInvoice): View|RedirectResponse
    {
        $exists = SalesInvoice::query()->whereKey($salesInvoice)->exists();
        if (! $exists) {
            return redirect()->route('sales.invoices.index')->with('toast', 'Sales invoice no longer exists.');
        }

        return view('modules.sales.invoices.edit', ['salesInvoice' => $salesInvoice]);
    }

    private function loadSalesInvoice(SalesInvoice $salesInvoice): void
    {
        $salesInvoice->load([
            'businessPartner:id,company_name,company_code',
            'salesOrder:id,sales_order_no',
            'deliveryReceipt:id,delivery_receipt_no',
            'items.item:id,item_name,item_code,item_image',
            'items.unitMeasure:id,name',
            'creator:id,name',
        ]);
    }
}
