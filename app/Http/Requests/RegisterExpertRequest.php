<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterExpertRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'unique:users,email',
            ],

            'password' => [
                'required',
                'string',
                'min:6',
                'confirmed',
            ],

            'phone' => [
                'required',
                'string',
                'max:20',
                'unique:users,phone',
            ],

            'address' => [
                'nullable',
                'string',
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

            'starting_price' => [
                'required',
                'numeric',
                'min:0',
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
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' =>
                'Nama wajib diisi.',

            'email.required' =>
                'Email wajib diisi.',

            'email.email' =>
                'Format email tidak valid.',

            'email.unique' =>
                'Email sudah digunakan.',

            'password.required' =>
                'Password wajib diisi.',

            'password.min' =>
                'Password minimal 6 karakter.',

            'password.confirmed' =>
                'Konfirmasi password tidak sesuai.',

            'phone.required' =>
                'Nomor WhatsApp wajib diisi.',

            'phone.unique' =>
                'Nomor WhatsApp sudah digunakan.',

            'category_id.required' =>
                'Kategori keahlian wajib dipilih.',

            'category_id.exists' =>
                'Kategori keahlian tidak ditemukan.',

            'location.required' =>
                'Lokasi ahli wajib diisi.',

            'starting_price.required' =>
                'Harga awal wajib diisi.',

            'starting_price.numeric' =>
                'Harga awal harus berupa angka.',

            'starting_price.min' =>
                'Harga awal tidak boleh negatif.',
        ];
    }
}