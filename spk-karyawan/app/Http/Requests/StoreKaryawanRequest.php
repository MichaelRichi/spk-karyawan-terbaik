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
            'tgl_lahir'     => ['nullable', 'date'],
            'jenis_kelamin' => ['required', 'in:Laki-laki,Perempuan'],
            'tgl_masuk'     => ['required', 'date'],
            'status'        => ['required', 'in:aktif,tidak_aktif'],
            'no_telepon'    => ['nullable', 'string', 'max:20'],
            'alamat'        => ['nullable', 'string', 'max:500'],
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