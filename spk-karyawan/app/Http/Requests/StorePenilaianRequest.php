<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePenilaianRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isDirektur();
    }

    public function rules(): array
    {
        return [
            'penilaian'   => ['required', 'array'],
            'penilaian.*' => ['required', 'exists:periode_sub_kriteria,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'penilaian.required'   => 'Semua kriteria harus diisi.',
            'penilaian.*.required' => 'Pilih nilai untuk setiap kriteria.',
            'penilaian.*.exists'   => 'Nilai yang dipilih tidak valid.',
        ];
    }
}