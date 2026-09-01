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
            if (!Schema::hasColumn('bidang_dpds', 'singkatan')) {
                $table->string('singkatan')->nullable()->after('nama');
            }
            if (!Schema::hasColumn('bidang_dpds', 'kabid')) {
                $table->string('kabid')->nullable()->after('pic_hp');
            }
            if (!Schema::hasColumn('bidang_dpds', 'nohpkabid')) {
                $table->string('nohpkabid')->nullable()->after('kabid');
            }
            if (!Schema::hasColumn('bidang_dpds', 'sekbid')) {
                $table->string('sekbid')->nullable()->after('nohpkabid');
            }
            if (!Schema::hasColumn('bidang_dpds', 'nohpsekbid')) {
                $table->string('nohpsekbid')->nullable()->after('sekbid');
            }
            if (!Schema::hasColumn('bidang_dpds', 'periode')) {
                $table->string('periode')->nullable()->after('nohpsekbid');
            }
            if (!Schema::hasColumn('bidang_dpds', 'is_dpd')) {
                $table->boolean('is_dpd')->default(true)->after('urutan');
            }
            if (!Schema::hasColumn('bidang_dpds', 'is_dpc')) {
                $table->boolean('is_dpc')->default(false)->after('is_dpd');
            }
            if (!Schema::hasColumn('bidang_dpds', 'is_dpra')) {
                $table->boolean('is_dpra')->default(false)->after('is_dpc');
            }
            if (!Schema::hasColumn('bidang_dpds', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('is_dpra');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bidang_dpds', function (Blueprint $table) {
            $cols = ['singkatan','kabid','nohpkabid','sekbid','nohpsekbid','periode','is_dpd','is_dpc','is_dpra','is_active'];
            $existing = array_filter($cols, fn($c) => Schema::hasColumn('bidang_dpds', $c));
            if ($existing) {
                $table->dropColumn(array_values($existing));
            }
        });
    }
};
