<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderStatusRequest extends FormRequest
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
                'in:Diterima,Dalam Proses,Selesai,Review,Closed,Cancelled',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' =>
                'Status pesanan wajib diisi.',

            'status.string' =>
                'Status pesanan harus berupa teks.',

            'status.in' =>
                'Status pesanan tidak valid.',
        ];
    }
}