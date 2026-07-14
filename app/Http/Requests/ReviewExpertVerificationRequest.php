<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReviewExpertVerificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('admin_notes')) {
            $this->merge([
                'admin_notes' => is_string($this->admin_notes)
                    ? trim(strip_tags($this->admin_notes))
                    : $this->admin_notes,
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'status' => [
                'required',
                'string',
                'in:approved,rejected',
            ],

            'admin_notes' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' =>
                'Status verifikasi wajib diisi.',

            'status.in' =>
                'Status verifikasi harus approved atau rejected.',

            'admin_notes.max' =>
                'Catatan admin maksimal 2000 karakter.',
        ];
    }
}