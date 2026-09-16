<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'asset_category_id' => ['required', 'exists:asset_categories,id'],
            'location_area_id' => ['required', 'exists:location_areas,id'],
            'status' => ['required', 'in:active,maintenance,retired'],
            'description' => ['nullable', 'string'],
        ];
    }
}
