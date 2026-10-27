<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class StoreSavingRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'package_code' => ['required', Rule::in(array_keys(config('qurma.packages')))],
            'start_month'  => [
                'bail', 'required', 'date_format:Y-m',
                function ($attribute, $value, $fail) {
                    if ($value < now()->format('Y-m')) {
                        $fail('Bulan mulai tidak boleh sebelum bulan ini');
                    }
                },
            ],
            'tenor_months' => ['nullable', 'integer', 'between:1,12'],
        ];
    }

    public function messages(): array
    {
        return [
            'package_code.required'   => 'Paket wajib dipilih',
            'package_code.in'         => 'Paket tidak ditemukan',
            'start_month.required'    => 'Bulan mulai wajib diisi',
            'start_month.date_format' => 'Format bulan mulai harus YYYY-MM',
            'tenor_months.integer'    => 'Lama menabung minimal 1 dan maksimal 12 bulan',
            'tenor_months.between'    => 'Lama menabung minimal 1 dan maksimal 12 bulan',
        ];
    }
}