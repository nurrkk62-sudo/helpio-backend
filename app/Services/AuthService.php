<?php

namespace App\Services;

use App\Models\Expert;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function registerExpert(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make(
                    $data['password']
                ),
                'phone' => $data['phone'],
                'role' => 'expert',
                'avatar' => null,
                'address' => $data['address'] ?? null,
            ]);

            $expert = Expert::create([
                'user_id' => $user->id,
                'category_id' => $data['category_id'],
                'location' => $data['location'],
                'experience' =>
                    $data['experience'] ?? null,
                'rating' => 0,
                'review_count' => 0,
                'completed_jobs' => 0,
                'starting_price' =>
                    $data['starting_price'],
                'banner' => null,
                'bio' => $data['bio'] ?? null,
                'operating_hours' =>
                    $data['operating_hours'] ?? null,
                'verified' => false,
                'verification_status' => 'pending',
            ]);

            $expert->load([
                'user',
                'category',
            ]);

            $token = $user
                ->createToken('api-token')
                ->plainTextToken;

            return [
                'user' => $user,
                'expert' => $expert,
                'token' => $token,
            ];
        });
    }
}