<?php

namespace App\Modules\Sales\Livewire\DeliveryReceipts;

use App\Modules\Sales\Models\DeliveryReceipt;
use App\Modules\Sales\Services\DeliveryReceiptService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    public string $search = '';
    public string $status = '';
    public string $date_from = '';
    public string $date_to = '';
    public int $perPage = 10;
    public bool $showVoidConfirmation = false;
    public ?int $pendingVoidReceiptId = null;
    public string $pendingVoidReceiptNo = '';

    public function mount(): void
    {
        $this->authorize('viewAny', DeliveryReceipt::class);
    }

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingStatus(): void { $this->resetPage(); }
    public function updatingDateFrom(): void { $this->resetPage(); }
    public function updatingDateTo(): void { $this->resetPage(); }
    public function updatingPerPage(): void { $this->resetPage(); }

    public function openUploadDetails(int $deliveryReceiptId): mixed
    {
        $receipt = DeliveryReceipt::query()->find($deliveryReceiptId);
        if (! $receipt) {
            session()->flash('toast', 'Delivery receipt no longer exists.');
            return null;
        }

        $this->authorize('update', $receipt);

        return redirect()->route('sales.delivery-receipts.upload-details', $receipt);
    }

    public function promptVoidReceipt(int $deliveryReceiptId): void
    {
        $receipt = DeliveryReceipt::query()->find($deliveryReceiptId);
        if (! $receipt) {
            session()->flash('toast', 'Delivery receipt no longer exists.');
            return;
        }

        $this->authorize('cancel', $receipt);
        $this->pendingVoidReceiptId = $receipt->id;
        $this->pendingVoidReceiptNo = $receipt->delivery_receipt_no;
        $this->showVoidConfirmation = true;
    }

    public function voidConfirmedReceipt(): void
    {
        if (! $this->pendingVoidReceiptId) {
            $this->resetVoidConfirmationState();
            return;
        }

        $receipt = DeliveryReceipt::query()->find($this->pendingVoidReceiptId);
        if (! $receipt) {
            session()->flash('toast', 'Delivery receipt no longer exists.');
            $this->resetVoidConfirmationState();
            return;
        }

        app(DeliveryReceiptService::class)->cancel($receipt);
        session()->flash('toast', 'Delivery receipt voided successfully.');
        $this->resetVoidConfirmationState();
    }

    public function cancelVoidConfirmation(): void
    {
        $this->resetVoidConfirmationState();
    }

    private function resetVoidConfirmationState(): void
    {
        $this->showVoidConfirmation = false;
        $this->pendingVoidReceiptId = null;
        $this->pendingVoidReceiptNo = '';
    }

    public function render()
    {
        return view('modules.sales.delivery-receipts.livewire.index', [
            'deliveryReceipts' => app(DeliveryReceiptService::class)->paginate([
                'search' => $this->search,
                'status' => $this->status,
                'date_from' => $this->date_from,
                'date_to' => $this->date_to,
            ], $this->perPage),
        ]);
    }
}
