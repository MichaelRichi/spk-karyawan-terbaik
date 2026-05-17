<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreKaryawanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama'          => ['required', 'string', 'max:100'],
            'jabatan'       => ['required', 'string', 'max:100'],
            'jenis_kelamin' => ['required', 'in:laki-laki,perempuan'],
            'tanggal_masuk' => ['required', 'date'],
            'status'        => ['required', 'in:aktif,tidak_aktif'],
        ];
    }

    public function messages(): array
    {
        return [
            'jenis_kelamin.in' => 'Jenis kelamin harus laki-laki atau perempuan.',
            'status.in'        => 'Status harus aktif atau tidak aktif.',
        ];
    }
}