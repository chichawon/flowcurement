<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class InventoryPageController extends Controller
{
    public function stockIndex(): View
    {
        return view('modules.inventory.stock.index');
    }

    public function movementsIndex(): View
    {
        return view('modules.inventory.movements.index');
    }

    public function adjustmentCreate(): View
    {
        return view('modules.inventory.adjustments.create');
    }
}
