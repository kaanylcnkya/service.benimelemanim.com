<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'role')) {
                $table->string('role', 30)->default('customer')->after('id');
            }

            if (! Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 30)->unique()->nullable()->after('email');
            }

            if (! Schema::hasColumn('users', 'city_id')) {
                $table->unsignedBigInteger('city_id')->nullable()->after('phone');
            }

            if (! Schema::hasColumn('users', 'district_id')) {
                $table->unsignedBigInteger('district_id')->nullable()->after('city_id');
            }

            if (! Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('district_id');
            }

            if (! Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('is_active');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            foreach ([
                'role',
                'phone',
                'city_id',
                'district_id',
                'is_active',
                'last_login_at',
            ] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};