<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class SalesCollectionPageController extends Controller
{
    public function index(): View
    {
        return view('modules.sales.collections.index');
    }

    public function create(): View
    {
        return view('modules.sales.collections.create');
    }
}
