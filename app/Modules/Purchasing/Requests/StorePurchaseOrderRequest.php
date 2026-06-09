<?php

namespace App\Modules\Purchasing\Requests;

use App\Modules\Purchasing\Helpers\PurchaseOrderOptions;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePurchaseOrderRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array { return self::rulesArray(); }

    public static function rulesArray(?int $ignoreId = null): array
    {
        return [
            'purchase_order_no' => ['required', 'max:100', Rule::unique('purchase_orders', 'purchase_order_no')->ignore($ignoreId)],
            'purchase_order_date' => ['required', 'date'],
            'expected_delivery_date' => ['nullable', 'date', 'after_or_equal:purchase_order_date'],
            'supplier_id' => ['required', Rule::exists('business_partners', 'id')->where('type', 'supplier')],
            'currency' => ['required', Rule::in(PurchaseOrderOptions::CURRENCIES)],
            'tax_rate' => ['required', Rule::in(PurchaseOrderOptions::TAX_RATES)],
            'remarks' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_id' => ['required', 'exists:items,id'],
            'items.*.description' => ['nullable', 'max:1000'],
            'items.*.lead_time' => ['nullable', 'max:255'],
            'items.*.unit_measure_id' => ['required', 'exists:unit_measures,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.price' => ['required', 'numeric', 'min:0'],
            'items.*.remarks' => ['nullable', 'max:1000'],
        ];
    }
}
