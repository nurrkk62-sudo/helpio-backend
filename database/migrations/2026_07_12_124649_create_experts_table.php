<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('experts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('category_id')
                ->constrained('categories')
                ->cascadeOnDelete();

            $table->string('location', 100);

            $table->string('experience', 50)
                ->nullable();

            $table->decimal('rating', 2, 1)
                ->default(0.0);

            $table->unsignedInteger('review_count')
                ->default(0);

            $table->unsignedInteger('completed_jobs')
                ->default(0);

            $table->decimal('starting_price', 12, 2)
                ->default(0);

            $table->text('banner')
                ->nullable();

            $table->text('bio')
                ->nullable();

            $table->string('operating_hours', 100)
                ->nullable();

            $table->boolean('verified')
                ->default(false);

            $table->enum('verification_status', [
                'pending',
                'approved',
                'rejected',
                'revision',
            ])->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('experts');
    }
};