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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 20)
                ->nullable()
                ->after('email');

            $table->enum('role', [
                'user',
                'expert',
                'admin',
            ])
                ->default('user')
                ->after('password');

            $table->text('avatar')
                ->nullable()
                ->after('role');

            $table->text('address')
                ->nullable()
                ->after('avatar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'role',
                'avatar',
                'address',
            ]);
        });
    }
};