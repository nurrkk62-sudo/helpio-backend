<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Expert;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | CATEGORY
        |--------------------------------------------------------------------------
        */

        $ac = Category::create([
            'name' => 'Service AC',
        ]);

        $listrik = Category::create([
            'name' => 'Kelistrikan',
        ]);

        $plumbing = Category::create([
            'name' => 'Plumbing',
        ]);

        /*
        |--------------------------------------------------------------------------
        | ADMIN
        |--------------------------------------------------------------------------
        */

        User::create([
            'name' => 'Admin HelpIO',

            'email' => 'admin@helpio.com',

            'password' => Hash::make('password123'),

            'phone' => '628111111111',

            'role' => 'admin',

            'address' => 'Makassar',
        ]);

        /*
        |--------------------------------------------------------------------------
        | USER
        |--------------------------------------------------------------------------
        */

        User::create([
            'name' => 'Putri User',

            'email' => 'putri@helpio.com',

            'password' => Hash::make('password123'),

            'phone' => '6281234567891',

            'role' => 'user',

            'address' => 'Jakarta',
        ]);

        /*
        |--------------------------------------------------------------------------
        | EXPERT
        |--------------------------------------------------------------------------
        */

        $expertUser = User::create([
            'name' => 'Budi Teknisi',

            'email' => 'budi@helpio.com',

            'password' => Hash::make('password123'),

            'phone' => '6281234567890',

            'role' => 'expert',

            'address' => 'Jakarta',
        ]);

        Expert::create([
            'user_id' => $expertUser->id,

            'category_id' => $ac->id,

            'location' => 'Jakarta Selatan',

            'experience' => '9 Tahun',

            'rating' => 4.9,

            'review_count' => 120,

            'completed_jobs' => 250,

            'starting_price' => 100000,

            'banner' => null,

            'bio' => 'Teknisi AC profesional.',

            'operating_hours' =>
                'Senin - Sabtu (08.00 - 18.00)',

            'verified' => true,

            'verification_status' => 'approved',
        ]);

        /*
        |--------------------------------------------------------------------------
        | USER RANDOM
        |--------------------------------------------------------------------------
        */

        User::factory(10)->create();
    }
}