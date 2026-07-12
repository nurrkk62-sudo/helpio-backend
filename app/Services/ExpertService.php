<?php

namespace App\Services;

use App\Models\Expert;
use Illuminate\Database\Eloquent\Collection;

class ExpertService
{
    public function all(): Collection
    {
        return Expert::with([
            'user',
            'category',
        ])->get();
    }

    public function find(int $id): Expert
    {
        return Expert::with([
            'user',
            'category',
        ])->findOrFail($id);
    }

    public function create(array $data): Expert
    {
        $expert = Expert::create($data);

        return $expert->load([
            'user',
            'category',
        ]);
    }

    public function update(int $id, array $data): Expert
    {
        $expert = Expert::findOrFail($id);

        $expert->update($data);

        return $expert->load([
            'user',
            'category',
        ]);
    }

    public function delete(int $id): void
    {
        $expert = Expert::findOrFail($id);

        $expert->delete();
    }
}