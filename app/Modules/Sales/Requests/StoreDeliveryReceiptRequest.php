<?php

namespace App\Modules\Sales\Requests;

use App\Modules\Sales\Helpers\DeliveryReceiptOptions;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDeliveryReceiptRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('delivery-receipts.create') ?? false;
    }

    public function rules(): array
    {
        return self::rulesArray();
    }

    public static function rulesArray(?int $deliveryReceiptId = null): array
    {
        return [
            'delivery_receipt_no' => ['required', 'string', 'max:50', Rule::unique('delivery_receipts', 'delivery_receipt_no')->ignore($deliveryReceiptId)],
            'dr_date' => ['required', 'date'],
            'sales_order_id' => ['required', Rule::exists('sales_orders', 'id')],
            'status' => ['nullable', Rule::in(DeliveryReceiptOptions::STATUSES)],
            'items' => ['required', 'array', 'min:1'],
            'items.*.sales_order_item_id' => ['required', Rule::exists('sales_order_items', 'id')],
            'items.*.item_id' => ['required', Rule::exists('items', 'id')],
            'items.*.delivered_quantity' => ['required', 'integer', 'min:0'],
            'items.*.unit_measure_id' => ['required', Rule::exists('unit_measures', 'id')],
            'items.*.available_stock' => ['required', 'numeric', 'min:0'],
            'items.*.remaining_balance_quantity' => ['required', 'numeric', 'min:0'],
            'items.*.remarks' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
