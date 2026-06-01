<?php

namespace App\Http\Controllers;

use App\Modules\BusinessPartners\Models\BusinessPartner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class BusinessPartnerPageController extends Controller
{
    public function clientsIndex(): View
    {
        return view('modules.business-partners.clients.index');
    }

    public function clientsCreate(): View
    {
        return view('modules.business-partners.clients.create');
    }

    public function clientsShow(int $businessPartner): View|RedirectResponse
    {
        $partner = BusinessPartner::query()
            ->clients()
            ->with(['creator:id,name', 'updater:id,name'])
            ->find($businessPartner);

        if (! $partner) {
            return redirect()->route('clients.index')->with('toast', 'Client record was already deleted or no longer exists.');
        }

        Gate::authorize('view', $partner);

        return view('modules.business-partners.show', [
            'businessPartner' => $partner,
            'routePrefix' => 'clients',
            'title' => 'Clients',
        ]);
    }

    public function clientsEdit(int $businessPartner): View|RedirectResponse
    {
        $partnerExists = BusinessPartner::query()
            ->clients()
            ->whereKey($businessPartner)
            ->exists();

        if (! $partnerExists) {
            return redirect()->route('clients.index')->with('toast', 'Client record was already deleted or no longer exists.');
        }

        return view('modules.business-partners.clients.edit', [
            'businessPartner' => $businessPartner,
        ]);
    }

    public function suppliersIndex(): View
    {
        return view('modules.business-partners.suppliers.index');
    }

    public function suppliersCreate(): View
    {
        return view('modules.business-partners.suppliers.create');
    }

    public function suppliersShow(int $businessPartner): View|RedirectResponse
    {
        $partner = BusinessPartner::query()
            ->suppliers()
            ->with(['creator:id,name', 'updater:id,name'])
            ->find($businessPartner);

        if (! $partner) {
            return redirect()->route('suppliers.index')->with('toast', 'Supplier record was already deleted or no longer exists.');
        }

        Gate::authorize('view', $partner);

        return view('modules.business-partners.show', [
            'businessPartner' => $partner,
            'routePrefix' => 'suppliers',
            'title' => 'Suppliers',
        ]);
    }

    public function suppliersEdit(int $businessPartner): View|RedirectResponse
    {
        $partnerExists = BusinessPartner::query()
            ->suppliers()
            ->whereKey($businessPartner)
            ->exists();

        if (! $partnerExists) {
            return redirect()->route('suppliers.index')->with('toast', 'Supplier record was already deleted or no longer exists.');
        }

        return view('modules.business-partners.suppliers.edit', [
            'businessPartner' => $businessPartner,
        ]);
    }
}
