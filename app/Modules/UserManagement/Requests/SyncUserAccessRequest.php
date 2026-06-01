<?php

namespace App\Modules\UserManagement\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SyncUserAccessRequest extends FormRequest
{
    public function authorize(): bool
    {
        return ($this->user()?->can('user-management.assign-roles') ?? false)
            || ($this->user()?->can('user-management.assign-permissions') ?? false);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return self::validationRules();
    }

    /**
     * @return array<string, mixed>
     */
    public static function validationRules(): array
    {
        return [
            'roles' => ['array'],
            'roles.*' => ['string', Rule::exists('roles', 'name')],
            'permissions' => ['array'],
            'permissions.*' => ['string', Rule::exists('permissions', 'name')],
        ];
    }
}
