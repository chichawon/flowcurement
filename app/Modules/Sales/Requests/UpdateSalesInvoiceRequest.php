<?php

namespace App\Modules\Sales\Requests;

class UpdateSalesInvoiceRequest extends StoreSalesInvoiceRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('sales-invoices.update') ?? false;
    }
}
