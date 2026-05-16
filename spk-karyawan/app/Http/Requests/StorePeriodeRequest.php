<?php

namespace App\Http\Requests;

use App\Models\Periode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StorePeriodeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->canManage();
    }

    public function rules(): array
    {
        return [
            'bulan'      => ['required', 'integer', 'min:1', 'max:12'],
            'tahun'      => ['required', 'integer', 'min:2020', 'max:2099'],
            'keterangan' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            $exists = Periode::where('bulan', $this->integer('bulan'))
                ->where('tahun', $this->integer('tahun'))
                ->exists();
            if ($exists) {
                $v->errors()->add('bulan', 'Periode untuk bulan dan tahun tersebut sudah ada.');
            }
        });
    }
}