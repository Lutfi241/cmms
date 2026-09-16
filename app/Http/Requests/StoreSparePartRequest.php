<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSparePartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $sparePartId = $this->route('spare_part')?->id;

        return [
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('spare_parts', 'name')
                    ->where(fn ($q) => $q->where('part_number', $this->part_number))
                    ->ignore($sparePartId),
            ],
            'part_number' => ['required', 'string', 'max:100'],
            'unit' => ['required', 'string', 'max:50'],
            'stock' => ['required', 'integer', 'min:0'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'Spare part dengan nama dan nomor part yang sama sudah terdaftar.',
        ];
    }
}
