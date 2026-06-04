<?php

namespace App\Modules\BusinessPartners\Livewire\Concerns;

use App\Modules\BusinessPartners\Models\BusinessPartner;
use App\Modules\BusinessPartners\Requests\StoreBusinessPartnerRequest;
use App\Modules\BusinessPartners\Requests\UpdateBusinessPartnerRequest;
use App\Modules\BusinessPartners\Services\BusinessPartnerService;
use Illuminate\Validation\Rule;

trait ManagesBusinessPartnerForm
{
    public ?BusinessPartner $partnerRecord = null;

    public string $company_name = '';

    public string $company_code = '';

    public string $tin_number = '';

    public string $contact_person = '';

    public string $contact_no = '';

    public string $agent_name = '';

    public string $credit_limit = '0.00';

    public string $company_address = '';

    public string $under_pesa = 'no';

    public string $vatable = 'non_vat';

    public string $terms = '30';

    public string $status = 'active';

    abstract protected function partnerType(): string;

    abstract protected function indexRoute(): string;

    protected function formRules(): array
    {
        $rules = $this->partnerRecord
            ? UpdateBusinessPartnerRequest::rulesArray($this->partnerRecord->id)
            : StoreBusinessPartnerRequest::rulesArray();

        $rules['terms'] = [
            'required',
            Rule::in($this->partnerType() === 'supplier' ? ['30', '60', '90', 'cash', 'check'] : ['30', '60', '90']),
        ];

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    protected function validationAttributes(): array
    {
        return [
            'company_name' => 'company name',
            'company_code' => 'company code',
            'tin_number' => 'TIN number',
            'contact_person' => 'contact person',
            'contact_no' => 'contact number',
            'agent_name' => 'agent name',
            'credit_limit' => 'credit limit',
            'company_address' => 'company address',
            'under_pesa' => 'under PESA',
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'tin_number.regex' => 'The TIN number must use the format 000-000-000-000.',
            'contact_no.digits_between' => 'The contact number must not exceed 11 digits.',
        ];
    }

    public function updatedCompanyCode(string $value): void
    {
        $this->company_code = strtoupper($value);
    }

    public function updatedTinNumber(string $value): void
    {
        $digits = substr(preg_replace('/\D/', '', $value) ?? '', 0, 12);
        $parts = array_filter(str_split($digits, 3), fn (string $part) => $part !== '');
        $this->tin_number = implode('-', $parts);
    }

    public function updatedContactNo(string $value): void
    {
        $this->contact_no = substr(preg_replace('/\D/', '', $value) ?? '', 0, 11);
    }

    public function updatedCreditLimit(string $value): void
    {
        $this->credit_limit = preg_replace('/[^\d.]/', '', $value) ?: '0';
    }

    protected function fillFromPartner(BusinessPartner $businessPartner): void
    {
        $this->partnerRecord = $businessPartner;
        $this->company_name = $businessPartner->company_name;
        $this->company_code = $businessPartner->company_code;
        $this->tin_number = $businessPartner->tin_number;
        $this->contact_person = $businessPartner->contact_person;
        $this->contact_no = $businessPartner->contact_no;
        $this->agent_name = (string) $businessPartner->agent_name;
        $this->credit_limit = number_format((float) $businessPartner->credit_limit, 2, '.', '');
        $this->company_address = $businessPartner->company_address ?? '';
        $this->under_pesa = $businessPartner->under_pesa;
        $this->vatable = $businessPartner->vatable;
        $this->terms = (string) $businessPartner->terms;
        $this->status = $businessPartner->status;
    }

    public function save(): mixed
    {
        $partners = app(BusinessPartnerService::class);

        $payload = $this->validate(
            $this->formRules(),
            $this->messages(),
            $this->validationAttributes()
        );

        $payload['updated_by'] = auth()->id();

        if ($this->partnerRecord) {
            $freshPartner = BusinessPartner::query()
                ->where('type', $this->partnerType())
                ->find($this->partnerRecord->id);

            if (! $freshPartner) {
                return redirect()
                    ->route($this->indexRoute().'.index')
                    ->with('toast', str($this->partnerType())->headline().' record was already deleted or no longer exists.');
            }

            $this->authorize('update', $freshPartner);
            $partner = $partners->update($freshPartner, $payload);
            $message = str($this->partnerType())->headline().' updated successfully.';
        } else {
            $this->authorize('create', BusinessPartner::class);
            $payload['created_by'] = auth()->id();
            $partner = $partners->create($this->partnerType(), $payload);
            $message = str($this->partnerType())->headline().' created successfully.';
        }

        return redirect()
            ->route($this->indexRoute().'.index')
            ->with('toast', $message);
    }
}
