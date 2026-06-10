<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ReportPageController extends Controller
{
    public function index(): RedirectResponse
    {
        return redirect()->route('reports.top-business-partners');
    }

    public function topBusinessPartners(): View
    {
        return view('modules.reports.top-business-partners');
    }

    public function companyTopOrderedItems(): View
    {
        return view('modules.reports.company-top-ordered-items');
    }
}
