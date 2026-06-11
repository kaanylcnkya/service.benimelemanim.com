<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_requests', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');

            $table->string('title', 190);
            $table->string('service_type', 120);

            $table->unsignedBigInteger('city_id');
            $table->unsignedBigInteger('district_id');

            $table->string('address_detail', 255)->nullable();

            $table->date('work_date')->nullable();
            $table->string('work_time', 50)->nullable();

            $table->text('description')->nullable();
            $table->string('budget', 100)->nullable();

            $table->string('status', 30)->default('open');

            $table->timestamps();

            $table->index('user_id');
            $table->index(['city_id', 'district_id']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_requests');
    }
};