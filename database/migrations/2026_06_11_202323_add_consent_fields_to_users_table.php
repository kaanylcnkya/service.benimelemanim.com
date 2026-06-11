<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'kvkk_accepted_at')) {
                $table->timestamp('kvkk_accepted_at')->nullable()->after('last_login_at');
            }

            if (! Schema::hasColumn('users', 'terms_accepted_at')) {
                $table->timestamp('terms_accepted_at')->nullable()->after('kvkk_accepted_at');
            }

            if (! Schema::hasColumn('users', 'ip_address')) {
                $table->string('ip_address', 45)->nullable()->after('terms_accepted_at');
            }

            if (! Schema::hasColumn('users', 'user_agent')) {
                $table->text('user_agent')->nullable()->after('ip_address');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            foreach ([
                'kvkk_accepted_at',
                'terms_accepted_at',
                'ip_address',
                'user_agent',
            ] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};