<form wire:submit.prevent="save" class="space-y-5">
    <section class="erp-panel">
        <div class="erp-panel-header">
            <h3 class="text-sm font-semibold text-slate-950">{{ $title }}</h3>
        </div>
        <div class="erp-panel-body space-y-4">
            <div class="grid gap-3 lg:grid-cols-2">
                <x-admin.form-field label="Company Name" name="company_name" wire:model.blur="company_name" required />
                <x-admin.form-field label="Company Code" name="company_code" wire:model.blur="company_code" class="uppercase" maxlength="50" required />
            </div>

            <div class="grid gap-3 lg:grid-cols-4">
                <x-admin.form-field label="TIN Number" name="tin_number" wire:model.live.debounce.250ms="tin_number" inputmode="numeric" maxlength="15" placeholder="000-000-000-000" required />
                <x-admin.form-field label="Contact Person" name="contact_person" wire:model.blur="contact_person" required />
                <x-admin.form-field label="Contact No." name="contact_no" wire:model.live.debounce.250ms="contact_no" inputmode="numeric" maxlength="11" required />
                <x-admin.form-field label="Agent Name" name="agent_name" wire:model.blur="agent_name" required />
            </div>

            <div class="grid gap-3 lg:grid-cols-5">
                <x-admin.form-field label="Credit Limit" name="credit_limit" type="number" wire:model.blur="credit_limit" min="0" step="0.01" required />
                <x-admin.select-field label="Under PESA" name="under_pesa" wire:model.live="under_pesa" required>
                    <option value="yes">Yes</option>
                    <option value="no">No</option>
                </x-admin.select-field>
                <x-admin.select-field label="VAT" name="vatable" wire:model.live="vatable" required>
                    <option value="non_vat">Non VAT</option>
                    <option value="with_vat">With VAT</option>
                </x-admin.select-field>
                <x-admin.select-field label="Terms" name="terms" wire:model.live="terms" required>
                    <option value="30">30 days</option>
                    <option value="60">60 days</option>
                    <option value="90">90 days</option>
                    @if (($partnerType ?? null) === 'supplier')
                        <option value="cash">Cash</option>
                        <option value="check">Check</option>
                    @endif
                </x-admin.select-field>
                <x-admin.select-field label="Status" name="status" wire:model.live="status" required>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </x-admin.select-field>
            </div>

            <x-admin.textarea-field label="Company Address" name="company_address" wire:model.blur="company_address" placeholder="Street, barangay, city, province" />
        </div>
    </section>

    <div class="sticky bottom-0 flex items-center justify-end gap-2 border-t border-slate-200 bg-slate-100/95 py-4 backdrop-blur">
        <a href="{{ $cancelRoute }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancel</a>
        <button type="submit" class="rounded-md bg-slate-950 px-4 py-2 text-sm font-semibold text-white shadow-sm erp-focus-ring hover:bg-slate-800">
            {{ $submitLabel }}
        </button>
    </div>
</form>
