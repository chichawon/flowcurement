<?php

namespace App\Modules\BusinessPartners\Services;

use App\Modules\AuditTrail\Services\AuditTrailService;
use App\Modules\BusinessPartners\Models\BusinessPartner;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class BusinessPartnerService
{
    private const MODULE = 'business-partners';

    /**
     * @param array{search?: string|null, status?: string|null, vatable?: string|null, terms?: string|int|null, under_pesa?: string|null, with_trashed?: bool} $filters
     * @return LengthAwarePaginator<int, BusinessPartner>
     */
    public function paginate(string $type, array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return BusinessPartner::query()
            ->with(['creator:id,name', 'updater:id,name'])
            ->where('type', $type)
            ->when($filters['with_trashed'] ?? false, fn (Builder $query) => $query->withTrashed())
            ->when($filters['search'] ?? null, function (Builder $query, string $search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('company_name', 'like', "%{$search}%")
                        ->orWhere('company_code', 'like', "%{$search}%")
                        ->orWhere('contact_person', 'like', "%{$search}%")
                        ->orWhere('agent_name', 'like', "%{$search}%");
                });
            })
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status))
            ->when($filters['vatable'] ?? null, fn (Builder $query, string $vatable) => $query->where('vatable', $vatable))
            ->when($filters['terms'] ?? null, fn (Builder $query, mixed $terms) => $query->where('terms', (string) $terms))
            ->when($filters['under_pesa'] ?? null, fn (Builder $query, string $underPesa) => $query->where('under_pesa', $underPesa))
            ->latest('id')
            ->paginate($perPage);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function create(string $type, array $data): BusinessPartner
    {
        return DB::transaction(function () use ($type, $data): BusinessPartner {
            $businessPartner = BusinessPartner::query()->create($this->payload($data, $type));

            app(AuditTrailService::class)->record(
                self::MODULE,
                'created',
                $businessPartner,
                null,
                $businessPartner->fresh()->getAttributes(),
                str($type)->headline().' created: '.$businessPartner->company_name
            );

            return $businessPartner;
        });
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(BusinessPartner $businessPartner, array $data): BusinessPartner
    {
        return DB::transaction(function () use ($businessPartner, $data): BusinessPartner {
            $oldValues = $businessPartner->getOriginal();

            $businessPartner->update($this->payload($data, $businessPartner->type, false));
            $businessPartner->refresh();

            app(AuditTrailService::class)->record(
                self::MODULE,
                'updated',
                $businessPartner,
                $oldValues,
                $businessPartner->getAttributes(),
                str($businessPartner->type)->headline().' updated: '.$businessPartner->company_name
            );

            return $businessPartner;
        });
    }

    public function markDeleted(BusinessPartner $businessPartner): void
    {
        DB::transaction(function () use ($businessPartner): void {
            $oldValues = $businessPartner->getOriginal();
            $businessPartner->delete();

            app(AuditTrailService::class)->record(
                self::MODULE,
                'deleted',
                $businessPartner,
                $oldValues,
                $businessPartner->fresh()?->getAttributes(),
                str($businessPartner->type)->headline().' deleted: '.$businessPartner->company_name
            );
        });
    }

    public function restore(BusinessPartner $businessPartner): void
    {
        DB::transaction(function () use ($businessPartner): void {
            $oldValues = $businessPartner->getOriginal();
            $businessPartner->restore();
            $businessPartner->refresh();

            app(AuditTrailService::class)->record(
                self::MODULE,
                'restored',
                $businessPartner,
                $oldValues,
                $businessPartner->getAttributes(),
                str($businessPartner->type)->headline().' restored: '.$businessPartner->company_name
            );
        });
    }

    public function forceDelete(BusinessPartner $businessPartner): void
    {
        DB::transaction(function () use ($businessPartner): void {
            $oldValues = $businessPartner->getOriginal();

            app(AuditTrailService::class)->record(
                self::MODULE,
                'force_deleted',
                $businessPartner,
                $oldValues,
                null,
                str($businessPartner->type)->headline().' permanently deleted: '.$businessPartner->company_name
            );

            $businessPartner->forceDelete();
        });
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function payload(array $data, string $type, bool $creating = true): array
    {
        $payload = Arr::only($data, [
            'company_name',
            'company_code',
            'tin_number',
            'contact_person',
            'contact_no',
            'agent_name',
            'credit_limit',
            'company_address',
            'under_pesa',
            'vatable',
            'terms',
            'status',
            'created_by',
            'updated_by',
        ]);

        $payload['type'] = $type;
        $payload['company_code'] = strtoupper((string) ($payload['company_code'] ?? ''));
        $payload['credit_limit'] = (float) ($payload['credit_limit'] ?? 0);
        $payload['terms'] = (string) ($payload['terms'] ?? '30');

        if (! $creating) {
            unset($payload['created_by']);
        }

        return $payload;
    }
}
