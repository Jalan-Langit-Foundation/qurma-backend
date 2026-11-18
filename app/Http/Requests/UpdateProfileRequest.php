<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateProfileRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'name'  => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($this->user()->id)],
            'phone' => ['required', 'regex:/^(08|62)[0-9]{8,13}$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'  => 'Nama wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email'    => 'Format email tidak valid',
            'email.unique'   => 'Email sudah terdaftar',
            'phone.required' => 'Nomor WhatsApp wajib diisi',
            'phone.regex'    => 'Nomor WhatsApp tidak valid (diawali 08 atau 62, 10-15 digit)',
        ];
    }
}