<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'expert_verifications',
            function (Blueprint $table) {
                $table->id();

                $table->foreignId('expert_id')
                    ->unique()
                    ->constrained('experts')
                    ->cascadeOnDelete();

                $table->string(
                    'identity_number',
                    100
                );

                $table->string(
                    'identity_document'
                );

                $table->string(
                    'certificate_document'
                )->nullable();

                $table->text('notes')
                    ->nullable();

                $table->enum('status', [
                    'pending',
                    'approved',
                    'rejected',
                ])->default('pending');

                $table->text('admin_notes')
                    ->nullable();

                $table->foreignId('reviewed_by')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();

                $table->timestamp('reviewed_at')
                    ->nullable();

                $table->timestamps();

                $table->index('status');
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'expert_verifications'
        );
    }
};