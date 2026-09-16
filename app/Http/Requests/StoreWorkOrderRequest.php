<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWorkOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'asset_id' => ['required', 'exists:assets,id'],
            'description' => ['required', 'string', 'min:10'],
        ];
    }

    public function messages(): array
    {
        return [
            'description.min' => 'Deskripsi permintaan minimal 10 karakter, jelaskan masalahnya dengan detail.',
        ];
    }
}
