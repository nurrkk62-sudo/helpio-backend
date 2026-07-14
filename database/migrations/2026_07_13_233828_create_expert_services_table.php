<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expert_services', function (Blueprint $table) {
            $table->id();

            $table->foreignId('expert_id')
                ->constrained('experts')
                ->cascadeOnDelete();

            $table->string('name');

            $table->text('description')
                ->nullable();

            $table->decimal(
                'price',
                12,
                2
            );

            $table->unsignedInteger('duration_minutes')
                ->nullable();

            $table->boolean('is_active')
                ->default(true);

            $table->timestamps();

            $table->index([
                'expert_id',
                'is_active',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expert_services');
    }
};