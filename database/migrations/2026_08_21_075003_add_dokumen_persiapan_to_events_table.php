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
        Schema::table('events', function (Blueprint $table) {
            $table->string('dokumen_permohonan_pencairan')->nullable()->after('funding_source');
            $table->string('dokumen_proposal')->nullable()->after('dokumen_permohonan_pencairan');
            $table->string('dokumen_dpa')->nullable()->after('dokumen_proposal');
            $table->string('dokumen_lpj_sebelumnya')->nullable()->after('dokumen_dpa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn([
                'dokumen_permohonan_pencairan',
                'dokumen_proposal',
                'dokumen_dpa',
                'dokumen_lpj_sebelumnya',
            ]);
        });
    }
};
