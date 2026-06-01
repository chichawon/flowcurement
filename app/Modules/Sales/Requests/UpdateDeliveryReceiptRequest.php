<?php

namespace App\Modules\Sales\Requests;

class UpdateDeliveryReceiptRequest extends StoreDeliveryReceiptRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('delivery-receipts.update') ?? false;
    }
}

