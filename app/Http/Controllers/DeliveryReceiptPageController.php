<?php

namespace App\Http\Controllers;

use App\Modules\Sales\Models\DeliveryReceipt;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class DeliveryReceiptPageController extends Controller
{
    public function index(): View
    {
        return view('modules.sales.delivery-receipts.index');
    }

    public function create(): View
    {
        return view('modules.sales.delivery-receipts.create');
    }

    public function show(int $deliveryReceipt): View|RedirectResponse
    {
        $deliveryReceipt = DeliveryReceipt::query()
            ->find($deliveryReceipt);

        if (! $deliveryReceipt) {
            return redirect()->route('sales.delivery-receipts.index')->with('toast', 'Delivery receipt no longer exists.');
        }

        $this->loadDeliveryReceipt($deliveryReceipt);
        Gate::authorize('view', $deliveryReceipt);

        return view('modules.sales.delivery-receipts.show', ['deliveryReceipt' => $deliveryReceipt]);
    }

    public function print(int $deliveryReceipt): Response|RedirectResponse
    {
        $deliveryReceipt = DeliveryReceipt::query()->find($deliveryReceipt);

        if (! $deliveryReceipt) {
            return redirect()->route('sales.delivery-receipts.index')->with('toast', 'Delivery receipt no longer exists.');
        }

        $this->loadDeliveryReceipt($deliveryReceipt);
        Gate::authorize('print', $deliveryReceipt);

        return Pdf::loadView('modules.sales.delivery-receipts.pdf', [
            'deliveryReceipt' => $deliveryReceipt,
        ])
            ->setPaper('a4')
            ->stream($deliveryReceipt->delivery_receipt_no.'.pdf');
    }

    public function uploadDetails(int $deliveryReceipt): View|RedirectResponse
    {
        $exists = DeliveryReceipt::query()->whereKey($deliveryReceipt)->exists();
        if (! $exists) {
            return redirect()->route('sales.delivery-receipts.index')->with('toast', 'Delivery receipt no longer exists.');
        }

        return view('modules.sales.delivery-receipts.upload-details', ['deliveryReceipt' => $deliveryReceipt]);
    }

    public function edit(int $deliveryReceipt): View|RedirectResponse
    {
        $exists = DeliveryReceipt::query()->whereKey($deliveryReceipt)->exists();
        if (! $exists) {
            return redirect()->route('sales.delivery-receipts.index')->with('toast', 'Delivery receipt no longer exists.');
        }

        return view('modules.sales.delivery-receipts.edit', ['deliveryReceipt' => $deliveryReceipt]);
    }

    private function loadDeliveryReceipt(DeliveryReceipt $deliveryReceipt): void
    {
        $deliveryReceipt->load([
            'salesOrder:id,sales_order_no',
            'businessPartner:id,company_name',
            'items.item:id,item_name',
            'items.unitMeasure:id,name',
            'items.salesOrderItem:id,description,price,total',
            'attachments',
            'salesInvoices:id,delivery_receipt_id,sales_invoice_no,invoice_date,total_amount,balance_amount,status',
            'creator:id,name',
        ]);
    }
}
