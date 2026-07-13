<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifyOtpRequest extends FormRequest
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

            'code' => [
                'required',
                'digits:6',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.required' =>
                'Nomor WhatsApp wajib diisi.',

            'phone.exists' =>
                'Nomor WhatsApp tidak terdaftar.',

            'code.required' =>
                'Kode OTP wajib diisi.',

            'code.digits' =>
                'Kode OTP harus terdiri dari 6 digit.',
        ];
    }
}