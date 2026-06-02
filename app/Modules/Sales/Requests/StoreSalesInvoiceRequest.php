<?php

namespace App\Modules\Sales\Requests;

use App\Modules\Sales\Helpers\SalesInvoiceOptions;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSalesInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('sales-invoices.create') ?? false;
    }

    public function rules(): array
    {
        return self::rulesArray();
    }

    public static function rulesArray(?int $salesInvoiceId = null): array
    {
        return [
            'sales_invoice_no' => ['required', 'string', 'max:50', Rule::unique('sales_invoices', 'sales_invoice_no')->ignore($salesInvoiceId)],
            'invoice_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:invoice_date'],
            'delivery_receipt_id' => ['required', Rule::exists('delivery_receipts', 'id')],
            'sales_order_id' => ['required', Rule::exists('sales_orders', 'id')],
            'business_partner_id' => ['required', Rule::exists('business_partners', 'id')],
            'currency' => ['required', Rule::in(SalesInvoiceOptions::CURRENCIES)],
            'tax_rate' => ['required', Rule::in(SalesInvoiceOptions::TAX_RATES)],
            'withholding_tax_rate' => ['required', Rule::in(SalesInvoiceOptions::WITHHOLDING_TAX_RATES)],
            'remarks' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.delivery_receipt_item_id' => ['required', Rule::exists('delivery_receipt_items', 'id')],
            'items.*.sales_order_item_id' => ['required', Rule::exists('sales_order_items', 'id')],
            'items.*.item_id' => ['required', Rule::exists('items', 'id')],
            'items.*.unit_measure_id' => ['required', Rule::exists('unit_measures', 'id')],
            'items.*.quantity' => ['required', 'numeric', 'min:0'],
            'items.*.invoiceable_quantity' => ['required', 'numeric', 'min:0'],
            'items.*.price' => ['required', 'numeric', 'min:0'],
            'items.*.withholding_tax_rate' => ['required', Rule::in(SalesInvoiceOptions::WITHHOLDING_TAX_RATES)],
            'items.*.description' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
