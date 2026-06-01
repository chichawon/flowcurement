<?php

namespace App\Modules\Sales\Requests;

class UpdateSalesOrderRequest extends StoreSalesOrderRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('sales-orders.update') ?? false;
    }
}
