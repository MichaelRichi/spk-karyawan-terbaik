<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreKriteriaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->canManage();
    }

    public function rules(): array
    {
        return [
            'nama'          => ['required', 'string', 'max:100'],
            'jenis'         => ['required', 'in:benefit,cost'],
            'bobot' => ['required', 'numeric', 'min:0', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'jenis.in'          => 'Tipe harus benefit atau cost.',
            'bobot.min' => 'Bobot tidak boleh kurang dari 0.',
            'bobot.max' => 'Bobot tidak boleh lebih dari 100.',
        ];
    }
}