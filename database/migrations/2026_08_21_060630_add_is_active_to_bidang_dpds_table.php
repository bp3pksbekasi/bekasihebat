<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bidang_dpds', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('is_dpra');
        });
    }

    public function down(): void
    {
        Schema::table('bidang_dpds', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
};
