<?php

namespace App\Http\Requests;



class StoreExpertRequest extends BaseApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => [
                'required',
                'exists:users,id',
            ],
            'category_id' => [
                'required',
                'exists:categories,id',
            ],
            'location' => [
                'required',
                'string',
                'max:100',
            ],
            'experience' => [
                'nullable',
                'string',
                'max:50',
            ],
            'rating' => [
                'nullable',
                'numeric',
                'min:0',
                'max:5',
            ],
            'review_count' => [
                'nullable',
                'integer',
                'min:0',
            ],
            'completed_jobs' => [
                'nullable',
                'integer',
                'min:0',
            ],
            'starting_price' => [
                'required',
                'numeric',
                'min:0',
            ],
            'banner' => [
                'nullable',
                'string',
            ],
            'bio' => [
                'nullable',
                'string',
            ],
            'operating_hours' => [
                'nullable',
                'string',
                'max:100',
            ],
            'verified' => [
                'nullable',
                'boolean',
            ],
            'verification_status' => [
                'nullable',
                'in:pending,approved,rejected,revision',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'User wajib dipilih.',
            'user_id.exists' => 'User tidak ditemukan.',

            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.exists' => 'Kategori tidak ditemukan.',

            'location.required' => 'Lokasi ahli wajib diisi.',
            'location.string' => 'Lokasi harus berupa teks.',
            'location.max' => 'Lokasi maksimal 100 karakter.',

            'experience.max' =>
                'Pengalaman maksimal 50 karakter.',

            'rating.numeric' => 'Rating harus berupa angka.',
            'rating.min' => 'Rating minimal adalah 0.',
            'rating.max' => 'Rating maksimal adalah 5.',

            'review_count.integer' =>
                'Jumlah ulasan harus berupa angka bulat.',

            'review_count.min' =>
                'Jumlah ulasan tidak boleh negatif.',

            'completed_jobs.integer' =>
                'Jumlah pekerjaan selesai harus berupa angka bulat.',

            'completed_jobs.min' =>
                'Jumlah pekerjaan selesai tidak boleh negatif.',

            'starting_price.required' =>
                'Harga awal wajib diisi.',

            'starting_price.numeric' =>
                'Harga awal harus berupa angka.',

            'starting_price.min' =>
                'Harga awal tidak boleh negatif.',

            'verification_status.in' =>
                'Status verifikasi tidak valid.',
        ];
    }
}