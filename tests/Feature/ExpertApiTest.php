<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ExpertApiTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_can_filter_experts_by_category()
{
    // 1. Buat data dummy
    $expert = Expert::factory()->create(['category_id' => 1]);

    // 2. Jalankan request ke API dengan filter
    $response = $this->getJson('/api/v1/experts?category_id=1');

    // 3. Pastikan data yang muncul benar
    $response->assertStatus(200)
             ->assertJsonFragment(['id' => $expert->id]);
}
}
