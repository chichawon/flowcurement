<?php

namespace App\Modules\BusinessPartners\Livewire;

use App\Modules\BusinessPartners\Models\BusinessPartner;
use App\Modules\BusinessPartners\Services\BusinessPartnerService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithPagination;

abstract class BusinessPartnerIndex extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    public string $search = '';

    public string $status = '';

    public string $vatable = '';

    public string $terms = '';

    public string $under_pesa = '';

    public bool $withTrashed = false;

    public int $perPage = 10;

    public bool $showDeleteConfirmation = false;

    public ?int $pendingDeletePartnerId = null;

    public string $pendingDeleteMode = 'delete';

    public string $pendingDeleteName = '';

    abstract protected function partnerType(): string;

    abstract protected function routePrefix(): string;

    abstract protected function title(): string;

    public function mount(): void
    {
        $this->authorize('viewAny', BusinessPartner::class);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updatingVatable(): void
    {
        $this->resetPage();
    }

    public function updatingTerms(): void
    {
        $this->resetPage();
    }

    public function updatingUnderPesa(): void
    {
        $this->resetPage();
    }

    public function updatingWithTrashed(): void
    {
        $this->resetPage();
    }

    public function promptDeletePartner(int $partnerId): void
    {
        $partner = BusinessPartner::query()
            ->where('type', $this->partnerType())
            ->find($partnerId);

        if (! $partner) {
            session()->flash('status', str($this->partnerType())->headline().' record was already deleted or no longer exists.');
            $this->resetDeleteConfirmationState();

            return;
        }

        $this->authorize('delete', $partner);
        $this->pendingDeletePartnerId = $partner->id;
        $this->pendingDeleteMode = 'delete';
        $this->pendingDeleteName = $partner->company_name;
        $this->showDeleteConfirmation = true;
    }

    public function promptForceDeletePartner(int $partnerId): void
    {
        $partner = BusinessPartner::onlyTrashed()
            ->where('type', $this->partnerType())
            ->find($partnerId);

        if (! $partner) {
            session()->flash('status', 'Deleted record no longer exists.');
            $this->resetDeleteConfirmationState();

            return;
        }

        $this->authorize('forceDelete', $partner);
        $this->pendingDeletePartnerId = $partner->id;
        $this->pendingDeleteMode = 'forceDelete';
        $this->pendingDeleteName = $partner->company_name;
        $this->showDeleteConfirmation = true;
    }

    public function deleteConfirmedPartner(): void
    {
        if (! $this->pendingDeletePartnerId) {
            $this->resetDeleteConfirmationState();

            return;
        }

        $partners = app(BusinessPartnerService::class);
        $partner = BusinessPartner::query()
            ->withTrashed()
            ->where('type', $this->partnerType())
            ->find($this->pendingDeletePartnerId);

        if (! $partner) {
            session()->flash('status', str($this->partnerType())->headline().' record was already deleted or no longer exists.');
            $this->resetDeleteConfirmationState();

            return;
        }

        if ($this->pendingDeleteMode === 'forceDelete') {
            $this->authorize('forceDelete', $partner);
            $partners->forceDelete($partner);
            session()->flash('status', str($this->partnerType())->headline().' permanently deleted.');
        } else {
            $this->authorize('delete', $partner);
            $partners->markDeleted($partner);
            session()->flash('status', str($this->partnerType())->headline().' moved to deleted records.');
        }

        $this->resetDeleteConfirmationState();
    }

    public function restorePartner(int $partnerId): void
    {
        $partner = BusinessPartner::onlyTrashed()
            ->where('type', $this->partnerType())
            ->findOrFail($partnerId);

        $this->authorize('restore', $partner);
        app(BusinessPartnerService::class)->restore($partner);
        session()->flash('status', str($this->partnerType())->headline().' restored successfully.');
    }

    public function cancelDeleteConfirmation(): void
    {
        $this->resetDeleteConfirmationState();
    }

    private function resetDeleteConfirmationState(): void
    {
        $this->showDeleteConfirmation = false;
        $this->pendingDeletePartnerId = null;
        $this->pendingDeleteMode = 'delete';
        $this->pendingDeleteName = '';
    }

    public function render()
    {
        return view('modules.business-partners.livewire.index', [
            'partners' => app(BusinessPartnerService::class)->paginate($this->partnerType(), [
                'search' => $this->search,
                'status' => $this->status,
                'vatable' => $this->vatable,
                'terms' => $this->terms,
                'under_pesa' => $this->under_pesa,
                'with_trashed' => $this->withTrashed,
            ], $this->perPage),
            'routePrefix' => $this->routePrefix(),
            'partnerType' => $this->partnerType(),
            'title' => $this->title(),
        ]);
    }
}
