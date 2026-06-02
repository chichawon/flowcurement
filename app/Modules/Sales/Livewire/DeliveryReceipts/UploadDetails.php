<?php

namespace App\Modules\Sales\Livewire\DeliveryReceipts;

use App\Modules\Sales\Models\DeliveryReceipt;
use App\Modules\Sales\Models\DeliveryReceiptAttachment;
use App\Modules\Sales\Services\DeliveryReceiptService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class UploadDetails extends Component
{
    use AuthorizesRequests;
    use WithFileUploads;

    public ?DeliveryReceipt $deliveryReceipt = null;

    public string $delivery_receipt_no = '';

    public string $received_date = '';

    public string $received_by = '';

    public string $delivered_by = '';

    public array $attachments = [];

    public function mount(int $deliveryReceiptId): void
    {
        $record = DeliveryReceipt::query()
            ->with('attachments')
            ->find($deliveryReceiptId);

        if (! $record) {
            $this->redirectRoute('sales.delivery-receipts.index', navigate: false);
            return;
        }

        $this->authorize('update', $record);
        $this->deliveryReceipt = $record;
        $this->delivery_receipt_no = $record->delivery_receipt_no;
        $this->received_date = $record->received_date?->toDateString() ?? now()->toDateString();
        $this->received_by = (string) $record->received_by;
        $this->delivered_by = (string) $record->delivered_by;
    }

    public function updatedAttachments(): void
    {
        $this->validateOnly('attachments.*', $this->rules(), [], $this->validationAttributes());
    }

    public function removeSelectedAttachment(int $index): void
    {
        if (! isset($this->attachments[$index])) {
            return;
        }

        unset($this->attachments[$index]);
        $this->attachments = array_values($this->attachments);
    }

    public function save(): mixed
    {
        if (! $this->deliveryReceipt) {
            return redirect()->route('sales.delivery-receipts.index');
        }

        $payload = $this->validate($this->rules(), [], $this->validationAttributes());

        app(DeliveryReceiptService::class)->updateUploadDetails(
            $this->deliveryReceipt,
            $payload,
            $this->attachments
        );

        return redirect()
            ->route('sales.delivery-receipts.index')
            ->with('toast', 'Delivery receipt upload details saved successfully.');
    }

    public function deleteAttachment(int $attachmentId): void
    {
        if (! $this->deliveryReceipt) {
            session()->flash('toast', 'Delivery receipt no longer exists.');
            return;
        }

        $attachment = DeliveryReceiptAttachment::query()
            ->where('delivery_receipt_id', $this->deliveryReceipt->id)
            ->find($attachmentId);

        if (! $attachment) {
            session()->flash('toast', 'Attachment no longer exists.');
            return;
        }

        app(DeliveryReceiptService::class)->deleteAttachment($attachment);
        $this->deliveryReceipt->load('attachments');
        session()->flash('toast', 'Attachment deleted.');
    }

    public function attachmentUrl(string $path): string
    {
        return Storage::disk('public')->url($path);
    }

    public function isImage(string $name): bool
    {
        return in_array(strtolower(pathinfo($name, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp'], true);
    }

    private function rules(): array
    {
        return [
            'received_date' => ['required', 'date'],
            'received_by' => ['required', 'string', 'max:255'],
            'delivered_by' => ['required', 'string', 'max:255'],
            'attachments' => ['required', 'array', 'min:1'],
            'attachments.*' => ['file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
        ];
    }

    private function validationAttributes(): array
    {
        return [
            'received_date' => 'received date',
            'received_by' => 'received by',
            'delivered_by' => 'delivered by',
            'attachments' => 'attachments',
            'attachments.*' => 'attachment',
        ];
    }

    public function render()
    {
        if (! $this->deliveryReceipt) {
            return view('modules.sales.delivery-receipts.livewire.upload-details', [
                'existingAttachments' => collect(),
            ]);
        }

        return view('modules.sales.delivery-receipts.livewire.upload-details', [
            'existingAttachments' => $this->deliveryReceipt->attachments()->latest('id')->get(),
        ]);
    }
}
