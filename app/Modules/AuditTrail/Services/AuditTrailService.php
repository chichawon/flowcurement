<?php

namespace App\Modules\AuditTrail\Services;

use App\Modules\AuditTrail\Models\AuditTrail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class AuditTrailService
{
    /**
     * @param array<string, mixed>|null $oldValues
     * @param array<string, mixed>|null $newValues
     */
    public function record(
        string $module,
        string $action,
        ?Model $auditable = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?string $description = null,
    ): AuditTrail {
        return AuditTrail::query()->create([
            'module' => $module,
            'action' => $action,
            'auditable_type' => $auditable?->getMorphClass(),
            'auditable_id' => $auditable?->getKey(),
            'description' => $description,
            'old_values' => $this->sanitize($oldValues),
            'new_values' => $this->sanitize($newValues),
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
            'created_by' => auth()->id(),
        ]);
    }

    /**
     * @param array<string, mixed>|null $values
     * @return array<string, mixed>|null
     */
    private function sanitize(?array $values): ?array
    {
        if ($values === null) {
            return null;
        }

        return Arr::except($values, [
            'password',
            'remember_token',
            'password_confirmation',
        ]);
    }
}
