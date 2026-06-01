<?php

namespace App\Http\Controllers;

use App\Modules\Items\Models\Item;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ItemPageController extends Controller
{
    public function redirectIndex(): RedirectResponse
    {
        return redirect()->route('local-items.index');
    }

    public function localIndex(): View
    {
        return view('modules.items.local.index');
    }

    public function localCreate(): View
    {
        return view('modules.items.local.create');
    }

    public function localShow(int $item): View|RedirectResponse
    {
        $itemRecord = Item::query()
            ->local()
            ->with(['supplier:id,company_name,company_code,type', 'creator:id,name', 'updater:id,name'])
            ->find($item);

        if (! $itemRecord) {
            return redirect()->route('local-items.index')->with('toast', 'Local item record was already deleted or no longer exists.');
        }

        Gate::authorize('view', $itemRecord);

        return view('modules.items.show', [
            'item' => $itemRecord,
            'routePrefix' => 'local-items',
            'title' => 'Local Items',
        ]);
    }

    public function localEdit(int $item): View|RedirectResponse
    {
        $itemExists = Item::query()
            ->local()
            ->whereKey($item)
            ->exists();

        if (! $itemExists) {
            return redirect()->route('local-items.index')->with('toast', 'Local item record was already deleted or no longer exists.');
        }

        return view('modules.items.local.edit', ['item' => $item]);
    }

    public function importIndex(): View
    {
        return view('modules.items.import.index');
    }

    public function importCreate(): View
    {
        return view('modules.items.import.create');
    }

    public function importShow(int $item): View|RedirectResponse
    {
        $itemRecord = Item::query()
            ->import()
            ->with(['supplier:id,company_name,company_code,type', 'creator:id,name', 'updater:id,name'])
            ->find($item);

        if (! $itemRecord) {
            return redirect()->route('import-items.index')->with('toast', 'Import item record was already deleted or no longer exists.');
        }

        Gate::authorize('view', $itemRecord);

        return view('modules.items.show', [
            'item' => $itemRecord,
            'routePrefix' => 'import-items',
            'title' => 'Import Items',
        ]);
    }

    public function importEdit(int $item): View|RedirectResponse
    {
        $itemExists = Item::query()
            ->import()
            ->whereKey($item)
            ->exists();

        if (! $itemExists) {
            return redirect()->route('import-items.index')->with('toast', 'Import item record was already deleted or no longer exists.');
        }

        return view('modules.items.import.edit', ['item' => $item]);
    }
}
