<?php

namespace App\Modules\Sales\Requests;

use App\Modules\Sales\Helpers\SalesOrderOptions;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSalesOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('sales-orders.create') ?? false;
    }

    public function rules(): array
    {
        return self::rulesArray();
    }

    public static function rulesArray(?int $salesOrderId = null): array
    {
        return [
            'sales_order_no' => ['required', 'string', 'max:50', Rule::unique('sales_orders', 'sales_order_no')->ignore($salesOrderId)],
            'order_date' => ['required', 'date'],
            'no_of_days' => ['required', 'integer', 'min:0'],
            'delivery_date' => ['required', 'date', 'after_or_equal:order_date'],
            'customer_po' => ['nullable', 'string', 'max:255'],
            'agent_name' => ['required', 'string', 'max:255'],
            'remarks' => ['nullable', 'string', 'max:1000'],
            'business_partner_id' => ['required', Rule::exists('business_partners', 'id')->where('type', 'client')],
            'currency' => ['required', Rule::in(SalesOrderOptions::CURRENCIES)],
            'tax_rate' => ['required', Rule::in(SalesOrderOptions::TAX_RATES)],
            'po_attachment_upload' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:5120'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['nullable', 'integer', Rule::exists('sales_order_items', 'id')],
            'items.*.item_id' => ['required', Rule::exists('items', 'id')],
            'items.*.description' => ['nullable', 'string', 'max:1000'],
            'items.*.order_quantity' => ['required', 'numeric', 'min:1'],
            'items.*.unit_measure_id' => ['required', Rule::exists('unit_measures', 'id')->where('status', 'active')],
            'items.*.price' => ['required', 'numeric', 'min:0'],
            'items.*.available_stock' => ['required', 'numeric', 'min:0'],
            'items.*.remarks' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
