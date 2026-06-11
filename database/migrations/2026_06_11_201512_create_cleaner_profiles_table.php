<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cleaner_profiles', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');

            $table->json('services')->nullable();

            $table->string('experience', 100)->nullable();
            $table->string('daily_price', 100)->nullable();
            $table->text('description')->nullable();

            $table->boolean('is_verified')->default(false);
            $table->boolean('is_visible')->default(true);

            $table->timestamps();

            $table->index('user_id');
            $table->index(['is_verified', 'is_visible']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cleaner_profiles');
    }
};