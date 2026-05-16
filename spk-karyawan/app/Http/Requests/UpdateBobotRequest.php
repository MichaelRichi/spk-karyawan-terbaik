<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateBobotRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isDirektur() || $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'bobot'   => ['required', 'array'],
            'bobot.*' => ['required', 'numeric', 'min:0', 'max:100'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            $total = array_sum($this->input('bobot', []));
            if (abs($total - 100) > 0.01) {
                $v->errors()->add('bobot', "Total bobot harus 100%. Saat ini: {$total}%.");
            }
        });
    }

    public function messages(): array
    {
        return [
            'bobot.required'   => 'Data bobot wajib diisi.',
            'bobot.*.numeric'  => 'Nilai bobot harus angka.',
            'bobot.*.min'      => 'Bobot tidak boleh kurang dari 0.',
            'bobot.*.max'      => 'Bobot tidak boleh lebih dari 100.',
        ];
    }
}