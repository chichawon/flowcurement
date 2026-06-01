<?php

namespace App\Modules\BusinessPartners\Policies;

use App\Models\User;
use App\Modules\BusinessPartners\Helpers\BusinessPartnerPermissions;
use App\Modules\BusinessPartners\Models\BusinessPartner;

class BusinessPartnerPolicy
{
    public function viewAny(User $actor): bool
    {
        return $actor->can(BusinessPartnerPermissions::VIEW);
    }

    public function view(User $actor, BusinessPartner $businessPartner): bool
    {
        return $actor->can(BusinessPartnerPermissions::VIEW);
    }

    public function create(User $actor): bool
    {
        return $actor->can(BusinessPartnerPermissions::CREATE);
    }

    public function update(User $actor, BusinessPartner $businessPartner): bool
    {
        return $actor->can(BusinessPartnerPermissions::UPDATE);
    }

    public function delete(User $actor, BusinessPartner $businessPartner): bool
    {
        return $actor->can(BusinessPartnerPermissions::DELETE);
    }

    public function restore(User $actor, BusinessPartner $businessPartner): bool
    {
        return $actor->can(BusinessPartnerPermissions::RESTORE);
    }

    public function forceDelete(User $actor, BusinessPartner $businessPartner): bool
    {
        return $actor->can(BusinessPartnerPermissions::FORCE_DELETE);
    }
}
