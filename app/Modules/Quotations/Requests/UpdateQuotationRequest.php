<?php

namespace App\Modules\Quotations\Requests;

class UpdateQuotationRequest extends StoreQuotationRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('quotations.update') ?? false;
    }
}
