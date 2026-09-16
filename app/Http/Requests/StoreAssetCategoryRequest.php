<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAssetCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $categoryId = $this->route('asset_category')?->id;

        return [
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('asset_categories', 'name')->ignore($categoryId),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'Kategori aset dengan nama tersebut sudah ada.',
        ];
    }
}
