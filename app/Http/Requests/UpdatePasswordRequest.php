<?php

namespace App\Http\Requests;

class UpdatePasswordRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'current_password' => ['required', 'string'],
            'password'         => ['required', 'string', 'min:8', 'confirmed', 'different:current_password'],
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.required' => 'Password saat ini wajib diisi',
            'password.required'         => 'Password baru wajib diisi',
            'password.min'              => 'Password minimal 8 karakter',
            'password.confirmed'        => 'Konfirmasi password tidak sama',
            'password.different'        => 'Password baru harus berbeda dari password saat ini',
        ];
    }
}