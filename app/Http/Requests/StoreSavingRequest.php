<?php

namespace App\Http\Requests;

use App\Services\SavingCalculator;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class StoreSavingRequest extends ApiRequest
{
    public function rules(): array
    {
        $calc = app(SavingCalculator::class);

        return [
            'package_code' => ['required', Rule::in(array_keys(config('qurma.packages')))],
            'start_month'  => [
                'bail', 'required', 'date_format:Y-m',
                function ($attribute, $value, $fail) use ($calc) {
                    if ($value < now()->format('Y-m')) {
                        $fail('Bulan mulai tidak boleh sebelum bulan ini');
                    } elseif ($message = $calc->startMonthError($value)) {
                        $fail($message);
                    }
                },
            ],
            'tenor_months' => [
                'nullable', 'integer', 'between:1,12',
                function ($attribute, $value, $fail) use ($calc) {
                    $start = $this->input('start_month');

                    if (! is_string($start) || ! Carbon::hasFormat($start, 'Y-m')) {
                        return;
                    }

                    if ($message = $calc->tenorError($start, (int) $value)) {
                        $fail($message);
                    }
                },
            ],
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