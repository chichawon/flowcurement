<?php

namespace App\Http\Controllers;

use App\Modules\Purchasing\Models\PurchaseOrder;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PurchaseOrderPageController extends Controller
{
    public function index(): View
    {
        return view('modules.purchasing.orders.index');
    }

    public function create(): View
    {
        return view('modules.purchasing.orders.create');
    }

    public function show(int $purchaseOrder): View|RedirectResponse
    {
        $purchaseOrder = PurchaseOrder::query()
            ->with(['items.item:id,item_name,item_code,item_image', 'items.unitMeasure:id,name', 'supplier:id,company_name'])
            ->find($purchaseOrder);

        if (! $purchaseOrder) {
            return redirect()->route('purchasing.orders.index')->with('toast', 'Purchase order no longer exists.');
        }

        Gate::authorize('view', $purchaseOrder);

        return view('modules.purchasing.orders.show', ['purchaseOrder' => $purchaseOrder]);
    }

    public function edit(int $purchaseOrder): View|RedirectResponse
    {
        if (! PurchaseOrder::query()->whereKey($purchaseOrder)->exists()) {
            return redirect()->route('purchasing.orders.index')->with('toast', 'Purchase order no longer exists.');
        }

        return view('modules.purchasing.orders.edit', ['purchaseOrder' => $purchaseOrder]);
    }

    public function print(int $purchaseOrder): Response|RedirectResponse
    {
        $purchaseOrder = PurchaseOrder::query()
            ->with(['items.item:id,item_name,item_code,item_image', 'items.unitMeasure:id,name', 'creator:id,name'])
            ->find($purchaseOrder);

        if (! $purchaseOrder) {
            return redirect()->route('purchasing.orders.index')->with('toast', 'Purchase order no longer exists.');
        }

        Gate::authorize('print', $purchaseOrder);

        return Pdf::loadView('modules.purchasing.orders.pdf', ['purchaseOrder' => $purchaseOrder])
            ->setPaper('a4')
            ->stream($purchaseOrder->purchase_order_no.'.pdf');
    }
}
