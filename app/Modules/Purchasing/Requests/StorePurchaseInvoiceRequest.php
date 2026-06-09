<?php

namespace App\Modules\Purchasing\Requests;

use App\Modules\Purchasing\Helpers\PurchaseInvoiceOptions;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePurchaseInvoiceRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array { return self::rulesArray(); }

    public static function rulesArray(?int $ignoreId = null): array
    {
        return [
            'purchase_invoice_no' => ['required', 'max:100', Rule::unique('purchase_invoices', 'purchase_invoice_no')->ignore($ignoreId)],
            'invoice_date' => ['required', 'date'],
            'supplier_invoice_no' => ['required', 'max:255'],
            'purchase_order_id' => ['nullable', 'exists:purchase_orders,id'],
            'supplier_id' => ['required', Rule::exists('business_partners', 'id')->where('type', 'supplier')],
            'due_date' => ['nullable', 'date', 'after_or_equal:invoice_date'],
            'currency' => ['required', Rule::in(PurchaseInvoiceOptions::CURRENCIES)],
            'tax_rate' => ['required', Rule::in(PurchaseInvoiceOptions::TAX_RATES)],
            'remarks' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_id' => ['required', 'exists:items,id'],
            'items.*.description' => ['nullable', 'max:1000'],
            'items.*.unit_measure_id' => ['required', 'exists:unit_measures,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.price' => ['required', 'numeric', 'min:0'],
        ];
    }
}
