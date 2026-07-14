<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $input = $this->all();

        array_walk($input, function (&$value): void {
            if (is_string($value)) {
                $value = trim(strip_tags($value));
            }
        });

        $this->merge($input);
    }

    public function rules(): array
    {
        return [
            'expert_service_id' => [
                'required',
                'integer',
                'exists:expert_services,id',
            ],

            'address' => [
                'required',
                'string',
            ],

            'date' => [
                'required',
                'date',
                'after_or_equal:today',
            ],

            'time' => [
                'required',
                'date_format:H:i',
            ],

            'description' => [
                'required',
                'string',
            ],

            'photo_url' => [
                'nullable',
                'string',
                'max:2048',
            ],

            'notes' => [
                'nullable',
                'string',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'expert_service_id.required' =>
                'Layanan expert wajib dipilih.',

            'expert_service_id.exists' =>
                'Layanan expert tidak ditemukan.',

            'address.required' =>
                'Alamat pengerjaan wajib diisi.',

            'date.required' =>
                'Tanggal pengerjaan wajib diisi.',

            'date.after_or_equal' =>
                'Tanggal pengerjaan tidak boleh sebelum hari ini.',

            'time.required' =>
                'Jam kedatangan wajib diisi.',

            'time.date_format' =>
                'Format jam harus HH:mm.',

            'description.required' =>
                'Deskripsi masalah wajib diisi.',
        ];
    }
}