<?php

namespace App\Http\Controllers;

use App\Modules\Sales\Models\DeliveryReceipt;
use Illuminate\Http\RedirectResponse;
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
            ->with([
                'salesOrder:id,sales_order_no',
                'businessPartner:id,company_name',
                'items.item:id,item_name',
                'items.unitMeasure:id,name',
                'items.salesOrderItem:id,description',
                'attachments',
                'creator:id,name',
            ])
            ->find($deliveryReceipt);

        if (! $deliveryReceipt) {
            return redirect()->route('sales.delivery-receipts.index')->with('toast', 'Delivery receipt no longer exists.');
        }

        Gate::authorize('view', $deliveryReceipt);

        return view('modules.sales.delivery-receipts.show', ['deliveryReceipt' => $deliveryReceipt]);
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
}
