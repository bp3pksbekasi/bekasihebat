<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'org_level')) {
                $table->string('org_level')->default('dpra')->after('role');
                // dpd = pimpinan/pengurus DPD Kab. Bekasi
                // dpc = pengurus DPC (kecamatan)
                // dpra = kader DPRa (desa/kelurahan)
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('org_level');
        });
    }
};
