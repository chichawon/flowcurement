<?php

namespace App\Http\Controllers;

use App\Modules\Quotations\Models\Quotation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class QuotationPageController extends Controller
{
    public function index(): View
    {
        return view('modules.quotations.index');
    }

    public function create(): View
    {
        return view('modules.quotations.create');
    }

    public function show(int $quotation): View|RedirectResponse
    {
        $quotation = Quotation::query()->find($quotation);

        if (! $quotation) {
            return redirect()->route('quotations.index')->with('toast', 'Quotation record was already deleted or no longer exists.');
        }

        $this->loadQuotation($quotation);
        Gate::authorize('view', $quotation);

        return view('modules.quotations.show', ['quotation' => $quotation]);
    }

    public function edit(int $quotation): View|RedirectResponse
    {
        $quotationExists = Quotation::query()->whereKey($quotation)->exists();

        if (! $quotationExists) {
            return redirect()->route('quotations.index')->with('toast', 'Quotation record was already deleted or no longer exists.');
        }

        return view('modules.quotations.edit', ['quotation' => $quotation]);
    }

    public function print(int $quotation): Response|RedirectResponse
    {
        $quotation = Quotation::query()->find($quotation);

        if (! $quotation) {
            return redirect()->route('quotations.index')->with('toast', 'Quotation record was already deleted or no longer exists.');
        }

        $this->loadQuotation($quotation);
        Gate::authorize('print', $quotation);

        return Pdf::loadView('modules.quotations.pdf', [
            'quotation' => $quotation,
        ])
            ->setPaper('a4')
            ->stream($quotation->quotation_no.'.pdf');
    }

    private function loadQuotation(Quotation $quotation): void
    {
        $quotation->load([
            'businessPartner:id,company_name,company_code,type',
            'preparedBy:id,name',
            'items.item:id,item_name,item_code,item_image',
            'items.unitMeasure:id,name',
        ]);
    }
}
