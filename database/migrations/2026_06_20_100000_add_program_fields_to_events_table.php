<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (!Schema::hasColumn('events', 'org_level')) {
                $table->string('org_level')->default('dpra')->after('created_by');
            }
            if (!Schema::hasColumn('events', 'bidang_dpd_id')) {
                $table->uuid('bidang_dpd_id')->nullable()->after('org_level');
                $table->foreign('bidang_dpd_id')->references('id')->on('bidang_dpds')->nullOnDelete();
            }
            if (!Schema::hasColumn('events', 'org_kecamatan')) {
                $table->string('org_kecamatan')->nullable()->after('bidang_dpd_id');
            }
            if (!Schema::hasColumn('events', 'org_desa')) {
                $table->string('org_desa')->nullable()->after('org_kecamatan');
            }
        });

        Schema::table('events', function (Blueprint $table) {
            $table->index('org_level');
            $table->index('bidang_dpd_id');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['bidang_dpd_id']);
            $table->dropIndex(['org_level']);
            $table->dropIndex(['bidang_dpd_id']);
            $table->dropColumn(['org_level', 'bidang_dpd_id', 'org_kecamatan', 'org_desa']);
        });
    }
};
