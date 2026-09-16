<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLocationAreaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $areaId = $this->route('location_area')?->id;

        return [
            'floor_id' => ['required', 'exists:floors,id'],
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('location_areas', 'name')
                    ->where(fn ($q) => $q->where('floor_id', $this->floor_id)->where('code', $this->code))
                    ->ignore($areaId),
            ],
            'code' => ['required', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'Location Area dengan nama dan kode yang sama sudah ada pada floor ini.',
        ];
    }
}
