<?php

namespace App\Http\Controllers;

use App\Models\Expert;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExpertController extends Controller
{
    /**
     * Menampilkan semua expert.
     */
    public function index(): JsonResponse
    {
        $experts = Expert::with([
            'user',
            'category',
        ])->get();

        return response()->json($experts, 200);
    }

    /**
     * Menyimpan expert baru.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'category_id' => 'required|exists:categories,id',
            'location' => 'required|string|max:100',
            'experience' => 'nullable|string|max:50',
            'rating' => 'nullable|numeric|min:0|max:5',
            'review_count' => 'nullable|integer|min:0',
            'completed_jobs' => 'nullable|integer|min:0',
            'starting_price' => 'nullable|numeric|min:0',
            'banner' => 'nullable|string',
            'bio' => 'nullable|string',
            'operating_hours' => 'nullable|string|max:100',
            'verified' => 'nullable|boolean',
            'verification_status' =>
                'nullable|in:pending,approved,rejected,revision',
        ]);

        $expert = Expert::create($validated);

        $expert->load([
            'user',
            'category',
        ]);

        return response()->json($expert, 201);
    }

    /**
     * Menampilkan satu expert.
     */
    public function show(Expert $expert): JsonResponse
    {
        $expert->load([
            'user',
            'category',
        ]);

        return response()->json($expert, 200);
    }

    /**
     * Mengubah data expert.
     */
    public function update(
        Request $request,
        Expert $expert
    ): JsonResponse {
        $validated = $request->validate([
            'user_id' => 'sometimes|exists:users,id',
            'category_id' => 'sometimes|exists:categories,id',
            'location' => 'sometimes|string|max:100',
            'experience' => 'nullable|string|max:50',
            'rating' => 'nullable|numeric|min:0|max:5',
            'review_count' => 'nullable|integer|min:0',
            'completed_jobs' => 'nullable|integer|min:0',
            'starting_price' => 'nullable|numeric|min:0',
            'banner' => 'nullable|string',
            'bio' => 'nullable|string',
            'operating_hours' => 'nullable|string|max:100',
            'verified' => 'nullable|boolean',
            'verification_status' =>
                'nullable|in:pending,approved,rejected,revision',
        ]);

        $expert->update($validated);

        $expert->load([
            'user',
            'category',
        ]);

        return response()->json($expert, 200);
    }

    /**
     * Menghapus expert.
     */
    public function destroy(Expert $expert): JsonResponse
    {
        $expert->delete();

        return response()->json(null, 204);
    }
}