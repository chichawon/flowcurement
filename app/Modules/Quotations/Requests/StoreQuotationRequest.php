<?php

namespace App\Modules\Quotations\Requests;

use App\Modules\Quotations\Helpers\QuotationOptions;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreQuotationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('quotations.create') ?? false;
    }

    public function rules(): array
    {
        return self::rulesArray();
    }

    public static function rulesArray(): array
    {
        return [
            'quotation_date' => ['required', 'date'],
            'validity_date' => ['required', 'date', 'after_or_equal:quotation_date'],
            'business_partner_id' => ['required', Rule::exists('business_partners', 'id')->where('type', 'client')],
            'agent_name' => ['required', 'string', 'max:255'],
            'currency' => ['required', Rule::in(QuotationOptions::CURRENCIES)],
            'tax_rate' => ['required', Rule::in(QuotationOptions::TAX_RATES)],
            'remarks' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_id' => ['required', Rule::exists('items', 'id')],
            'items.*.description' => ['nullable', 'string', 'max:1000'],
            'items.*.unit_measure_id' => ['required', Rule::exists('unit_measures', 'id')->where('status', 'active')],
            'items.*.item_price' => ['required', 'numeric', 'min:0'],
            'items.*.quantity' => ['required', 'numeric', 'min:1'],
        ];
    }
}
