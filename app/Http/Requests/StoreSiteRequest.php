<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSiteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // otorisasi sudah dicek via middleware permission di route
    }

    public function rules(): array
    {
        $siteId = $this->route('site')?->id;

        return [
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('sites', 'name')
                    ->where(fn ($query) => $query->where('type', $this->type))
                    ->ignore($siteId),
            ],
            'type' => ['required', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'Site dengan nama dan tipe yang sama sudah terdaftar.',
        ];
    }
}
