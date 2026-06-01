<?php

namespace App\Modules\BusinessPartners\Requests;

use App\Modules\BusinessPartners\Helpers\BusinessPartnerOptions;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBusinessPartnerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('business-partners.update') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $businessPartner = $this->route('businessPartner');

        return self::rulesArray(is_object($businessPartner) ? $businessPartner->id : (int) $businessPartner);
    }

    /**
     * @return array<string, mixed>
     */
    public static function rulesArray(?int $businessPartnerId = null): array
    {
        return [
            'company_name' => ['required', 'string', 'max:255'],
            'company_code' => ['required', 'string', 'max:50', Rule::unique('business_partners', 'company_code')->ignore($businessPartnerId)],
            'tin_number' => ['required', 'regex:/^\d{3}-\d{3}-\d{3}-\d{3}$/'],
            'contact_person' => ['required', 'string', 'max:255'],
            'contact_no' => ['required', 'numeric', 'digits_between:1,11'],
            'credit_limit' => ['nullable', 'numeric', 'min:0'],
            'company_address' => ['nullable', 'string'],
            'under_pesa' => ['required', Rule::in(BusinessPartnerOptions::UNDER_PESA)],
            'vatable' => ['required', Rule::in(BusinessPartnerOptions::VATABLE)],
            'terms' => ['required', Rule::in(BusinessPartnerOptions::TERMS)],
            'status' => ['required', Rule::in(BusinessPartnerOptions::STATUSES)],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'tin_number.regex' => 'The TIN number must use the format 000-000-000-000.',
            'contact_no.digits_between' => 'The contact number must not exceed 11 digits.',
        ];
    }
}
