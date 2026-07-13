<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendOtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phone' => [
                'required',
                'string',
                'exists:users,phone',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.required' =>
                'Nomor WhatsApp wajib diisi.',

            'phone.string' =>
                'Nomor WhatsApp harus berupa teks.',

            'phone.exists' =>
                'Nomor WhatsApp tidak terdaftar.',
        ];
    }
}