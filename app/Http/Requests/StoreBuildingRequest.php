<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBuildingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $buildingId = $this->route('building')?->id;

        return [
            'site_id' => ['required', 'exists:sites,id'],
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('buildings', 'name')
                    ->where(fn ($q) => $q->where('site_id', $this->site_id)->where('code', $this->code))
                    ->ignore($buildingId),
            ],
            'code' => ['required', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'Building dengan nama dan kode yang sama sudah ada pada site ini.',
        ];
    }
}
