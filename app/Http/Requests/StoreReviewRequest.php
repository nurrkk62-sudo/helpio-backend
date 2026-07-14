<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('comment')) {
            $this->merge([
                'comment' => is_string($this->comment)
                    ? trim(strip_tags($this->comment))
                    : $this->comment,
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'order_id' => [
                'required',
                'string',
                'exists:orders,id',
            ],

            'rating' => [
                'required',
                'integer',
                'between:1,5',
            ],

            'comment' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'order_id.required' =>
                'Pesanan wajib dipilih.',

            'order_id.exists' =>
                'Pesanan tidak ditemukan.',

            'rating.required' =>
                'Rating wajib diisi.',

            'rating.integer' =>
                'Rating harus berupa angka.',

            'rating.between' =>
                'Rating harus antara 1 sampai 5.',

            'comment.max' =>
                'Komentar maksimal 2000 karakter.',
        ];
    }
}