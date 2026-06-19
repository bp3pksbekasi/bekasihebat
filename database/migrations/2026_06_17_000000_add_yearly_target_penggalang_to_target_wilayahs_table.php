<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('target_wilayahs', function (Blueprint $table) {
            if (!Schema::hasColumn('target_wilayahs', 'target_penggalang_2026')) {
                $table->integer('target_penggalang_2026')->default(0)->after('target_penggalang');
            }
            if (!Schema::hasColumn('target_wilayahs', 'target_penggalang_2027')) {
                $table->integer('target_penggalang_2027')->default(0)->after('target_penggalang_2026');
            }
            if (!Schema::hasColumn('target_wilayahs', 'target_penggalang_2028')) {
                $table->integer('target_penggalang_2028')->default(0)->after('target_penggalang_2027');
            }
            if (!Schema::hasColumn('target_wilayahs', 'target_penggalang_2029')) {
                $table->integer('target_penggalang_2029')->default(0)->after('target_penggalang_2028');
            }
        });
    }

    public function down(): void
    {
        Schema::table('target_wilayahs', function (Blueprint $table) {
            $table->dropColumn([
                'target_penggalang_2026',
                'target_penggalang_2027',
                'target_penggalang_2028',
                'target_penggalang_2029',
            ]);
        });
    }
};
