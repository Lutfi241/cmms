<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFloorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $floorId = $this->route('floor')?->id;

        return [
            'building_id' => ['required', 'exists:buildings,id'],
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('floors', 'name')
                    ->where(fn ($q) => $q->where('building_id', $this->building_id)->where('code', $this->code))
                    ->ignore($floorId),
            ],
            'code' => ['required', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'Floor dengan nama dan kode yang sama sudah ada pada building ini.',
        ];
    }
}
