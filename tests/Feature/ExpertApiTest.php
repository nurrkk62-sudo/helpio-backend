<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Expert;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ExpertApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test filter expert berdasarkan category.
     */
    public function test_can_filter_experts_by_category(): void
    {
        $category = Category::query()->create([
            'name' => 'Service AC',
        ]);

        $expertUser = User::factory()->create([
            'role' => 'expert',
        ]);

        $expert = Expert::query()->create([
            'user_id' => $expertUser->id,
            'category_id' => $category->id,
            'location' => 'Jakarta Selatan',
            'experience' => '5 Tahun',
            'rating' => 0,
            'review_count' => 0,
            'completed_jobs' => 0,
            'starting_price' => 100000,
            'bio' => 'Teknisi AC profesional.',
            'operating_hours' => 'Senin - Sabtu',
            'verified' => false,
            'verification_status' => 'pending',
        ]);

        $authenticatedUser = User::factory()->create([
            'role' => 'user',
        ]);

        Sanctum::actingAs(
            $authenticatedUser
        );

        $response = $this->getJson(
            '/api/v1/experts?category_id=' . $category->id
        );

        $response->assertStatus(200);

        $response->assertJsonFragment([
            'id' => $expert->id,
            'category_id' => $category->id,
        ]);
    }
}