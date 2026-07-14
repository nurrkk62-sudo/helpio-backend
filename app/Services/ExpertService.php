<?php

namespace App\Services;

use App\Models\Expert;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ExpertService
{
    /**
     * Mengambil semua data expert.
     */
    public function getAll(): Collection
    {
        return Expert::with([
            'user',
            'category',
        ])->get();
    }

    /**
     * Mengambil satu data expert berdasarkan ID.
     */
    public function findById(int $id): Expert
    {
        return Expert::with([
            'user',
            'category',
        ])->findOrFail($id);
    }

    /**
     * Membuat data expert baru.
     */
    public function create(array $data): Expert
    {
        $expert = Expert::create($data);

        return $expert->load([
            'user',
            'category',
        ]);
    }

    /**
     * Memperbarui data expert.
     */
    public function update(
        int $id,
        array $data
    ): Expert {
        $expert = Expert::findOrFail($id);

        $expert->update($data);

        return $expert->load([
            'user',
            'category',
        ]);
    }

    /**
     * Menghapus data expert.
     */
    public function delete(int $id): void
    {
        $expert = Expert::findOrFail($id);

        $expert->delete();
    }
}