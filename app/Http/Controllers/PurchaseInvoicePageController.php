<?php

namespace App\Http\Controllers;

use App\Modules\Purchasing\Models\PurchaseInvoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PurchaseInvoicePageController extends Controller
{
    public function index(): View
    {
        return view('modules.purchasing.invoices.index');
    }

    public function create(): View
    {
        return view('modules.purchasing.invoices.create');
    }

    public function show(int $purchaseInvoice): View|RedirectResponse
    {
        $purchaseInvoice = PurchaseInvoice::query()
            ->with(['items.item:id,item_name,item_code,item_image', 'items.unitMeasure:id,name', 'purchaseOrder:id,purchase_order_no'])
            ->find($purchaseInvoice);

        if (! $purchaseInvoice) {
            return redirect()->route('purchasing.invoices.index')->with('toast', 'Purchase invoice no longer exists.');
        }

        Gate::authorize('view', $purchaseInvoice);

        return view('modules.purchasing.invoices.show', ['purchaseInvoice' => $purchaseInvoice]);
    }

    public function edit(int $purchaseInvoice): View|RedirectResponse
    {
        if (! PurchaseInvoice::query()->whereKey($purchaseInvoice)->exists()) {
            return redirect()->route('purchasing.invoices.index')->with('toast', 'Purchase invoice no longer exists.');
        }

        return view('modules.purchasing.invoices.edit', ['purchaseInvoice' => $purchaseInvoice]);
    }

    public function print(int $purchaseInvoice): Response|RedirectResponse
    {
        $purchaseInvoice = PurchaseInvoice::query()
            ->with(['items.item:id,item_name,item_code,item_image', 'items.unitMeasure:id,name', 'creator:id,name'])
            ->find($purchaseInvoice);

        if (! $purchaseInvoice) {
            return redirect()->route('purchasing.invoices.index')->with('toast', 'Purchase invoice no longer exists.');
        }

        Gate::authorize('print', $purchaseInvoice);

        return Pdf::loadView('modules.purchasing.invoices.pdf', ['purchaseInvoice' => $purchaseInvoice])
            ->setPaper('a4')
            ->stream($purchaseInvoice->purchase_invoice_no.'.pdf');
    }
}
