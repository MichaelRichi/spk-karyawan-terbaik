<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreKaryawanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->canManage();
    }

    public function rules(): array
    {
        return [
            'nama'          => ['required', 'string', 'max:100'],
            'jabatan'       => ['required', 'string', 'max:100'],
            'jenis_kelamin' => ['required', 'in:laki-laki,perempuan'],
            'tanggal_masuk' => ['required', 'date', 'before_or_equal:today'],
            'status'        => ['required', 'in:tetap,tidak_tetap'],
        ];
    }

    public function messages(): array
    {
        return [
            'jenis_kelamin.in'              => 'Jenis kelamin tidak valid.',
            'tanggal_masuk.before_or_equal' => 'Tanggal masuk tidak boleh lebih dari hari ini.',
            'status.in'                     => 'Status harus tetap atau tidak tetap.',
        ];
    }
}