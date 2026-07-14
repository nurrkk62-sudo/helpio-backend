<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminUpdateOrderStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => [
                'required',
                'string',
                'in:Pending,Diterima,Selesai,Review,Closed,Dibatalkan',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' =>
                'Status pesanan wajib diisi.',

            'status.in' =>
                'Status pesanan tidak valid.',
        ];
    }
}