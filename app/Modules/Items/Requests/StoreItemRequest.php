<?php

namespace App\Modules\Items\Requests;

use App\Modules\Items\Helpers\ItemOptions;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('items.create') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return self::rulesArray();
    }

    /**
     * @return array<string, mixed>
     */
    public static function rulesArray(): array
    {
        return [
            'item_name' => ['required', 'string', 'max:255'],
            'item_code' => ['required', 'string', 'max:100', Rule::unique('items', 'item_code')],
            'item_type' => ['required', 'string', 'max:100', Rule::exists('item_types', 'name')->where('status', 'active')],
            'item_source' => ['required', Rule::in(ItemOptions::SOURCES)],
            'supplier_id' => [
                'required',
                Rule::exists('business_partners', 'id')->where('type', 'supplier'),
            ],
            'supplier_price' => ['required', 'numeric', 'min:0'],
            'percentage' => ['required', 'numeric', 'min:0'],
            'available_stock' => ['required', 'integer', 'min:0'],
            'reorder_point' => ['required', 'integer', 'min:0'],
            'taxable' => ['required', Rule::in(ItemOptions::TAXABLE)],
            'item_image_upload' => ['nullable', 'file', 'extensions:jpg,jpeg,png,gif,bmp,webp,svg,avif,heic,heif,tif,tiff,ico', 'max:10240'],
            'status' => ['required', Rule::in(ItemOptions::STATUSES)],
        ];
    }
}
