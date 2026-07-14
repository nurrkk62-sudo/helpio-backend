<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->string('id', 20)->primary();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('expert_id')
                ->constrained('experts')
                ->cascadeOnDelete();

            $table->string('service_title', 150);

            $table->decimal('price', 12, 2);

            $table->text('address');

            $table->date('date');

            $table->time('time');

            $table->text('description');

            $table->text('photo_url')
                ->nullable();

            $table->text('notes')
                ->nullable();

            $table->enum('status', [
                'Pending',
                'Diterima',
                'Dalam Proses',
                'Selesai',
                'Review',
                'Closed',
                'Cancelled',
            ])->default('Pending');

            $table->string('payment_method', 100)
                ->default(
                    'Cash / COD / Transfer Langsung Offline'
                );

            $table->timestamps();

            $table->index([
                'user_id',
                'status',
            ]);

            $table->index([
                'expert_id',
                'status',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};