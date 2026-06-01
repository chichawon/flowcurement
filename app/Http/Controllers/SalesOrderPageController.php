<?php

namespace App\Http\Controllers;

use App\Modules\Sales\Models\SalesOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class SalesOrderPageController extends Controller
{
    public function index(): View
    {
        return view('modules.sales.orders.index');
    }

    public function create(): View
    {
        return view('modules.sales.orders.create');
    }

    public function show(int $salesOrder): View|RedirectResponse
    {
        $salesOrder = SalesOrder::query()->with([
            'businessPartner:id,company_name,type',
            'quotation:id,quotation_no',
            'creator:id,name',
            'items.item:id,item_name,item_code',
            'items.unitMeasure:id,name',
        ])->find($salesOrder);

        if (! $salesOrder) {
            return redirect()->route('sales.orders.index')->with('toast', 'Sales order no longer exists.');
        }

        Gate::authorize('view', $salesOrder);

        return view('modules.sales.orders.show', ['salesOrder' => $salesOrder]);
    }

    public function edit(int $salesOrder): View|RedirectResponse
    {
        $exists = SalesOrder::query()->whereKey($salesOrder)->exists();

        if (! $exists) {
            return redirect()->route('sales.orders.index')->with('toast', 'Sales order no longer exists.');
        }

        return view('modules.sales.orders.edit', ['salesOrder' => $salesOrder]);
    }
}
