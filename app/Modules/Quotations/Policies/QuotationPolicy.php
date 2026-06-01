<?php

namespace App\Modules\Quotations\Policies;

use App\Models\User;
use App\Modules\Quotations\Helpers\QuotationPermissions;
use App\Modules\Quotations\Models\Quotation;

class QuotationPolicy
{
    public function viewAny(User $actor): bool { return $actor->can(QuotationPermissions::VIEW); }
    public function view(User $actor, Quotation $quotation): bool { return $actor->can(QuotationPermissions::VIEW); }
    public function create(User $actor): bool { return $actor->can(QuotationPermissions::CREATE); }
    public function update(User $actor, Quotation $quotation): bool
    {
        return $actor->can(QuotationPermissions::UPDATE)
            && blank($quotation->reference_sales_order_id);
    }
    public function delete(User $actor, Quotation $quotation): bool
    {
        return $actor->can(QuotationPermissions::DELETE)
            && blank($quotation->reference_sales_order_id);
    }
    public function print(User $actor, Quotation $quotation): bool { return $actor->can(QuotationPermissions::PRINT); }
    public function approve(User $actor, Quotation $quotation): bool { return $actor->can(QuotationPermissions::APPROVE); }
    public function convert(User $actor, Quotation $quotation): bool { return $actor->can(QuotationPermissions::CONVERT); }
}
