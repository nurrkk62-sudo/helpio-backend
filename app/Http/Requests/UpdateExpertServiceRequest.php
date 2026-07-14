<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExpertServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'sometimes',
                'nullable',
                'string',
            ],

            'price' => [
                'sometimes',
                'required',
                'numeric',
                'min:0',
            ],

            'duration_minutes' => [
                'sometimes',
                'nullable',
                'integer',
                'min:1',
            ],

            'is_active' => [
                'sometimes',
                'boolean',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' =>
                'Nama layanan wajib diisi.',

            'name.string' =>
                'Nama layanan harus berupa teks.',

            'name.max' =>
                'Nama layanan maksimal 255 karakter.',

            'price.required' =>
                'Harga layanan wajib diisi.',

            'price.numeric' =>
                'Harga layanan harus berupa angka.',

            'price.min' =>
                'Harga layanan minimal 0.',

            'duration_minutes.integer' =>
                'Durasi layanan harus berupa angka bulat.',

            'duration_minutes.min' =>
                'Durasi layanan minimal 1 menit.',

            'is_active.boolean' =>
                'Status layanan harus berupa true atau false.',
        ];
    }
}