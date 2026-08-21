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
        Schema::table('bidang_dpds', function (Blueprint $table) {
            $table->boolean('is_dpd')->default(true)->after('urutan');
            $table->boolean('is_dpc')->default(false)->after('is_dpd');
            $table->boolean('is_dpra')->default(false)->after('is_dpc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bidang_dpds', function (Blueprint $table) {
            $table->dropColumn(['is_dpd', 'is_dpc', 'is_dpra']);
        });
    }
};
