<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExpertVerificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('identity_number')) {
            $this->merge([
                'identity_number' => is_string(
                    $this->identity_number
                )
                    ? trim(
                        strip_tags(
                            $this->identity_number
                        )
                    )
                    : $this->identity_number,
            ]);
        }

        if ($this->has('notes')) {
            $this->merge([
                'notes' => is_string($this->notes)
                    ? trim(strip_tags($this->notes))
                    : $this->notes,
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'identity_number' => [
                'required',
                'string',
                'max:100',
            ],

            'identity_document' => [
                'required',
                'string',
                'max:2048',
            ],

            'certificate_document' => [
                'nullable',
                'string',
                'max:2048',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'identity_number.required' =>
                'Nomor identitas wajib diisi.',

            'identity_document.required' =>
                'Dokumen identitas wajib diisi.',

            'notes.max' =>
                'Catatan maksimal 2000 karakter.',
        ];
    }
}