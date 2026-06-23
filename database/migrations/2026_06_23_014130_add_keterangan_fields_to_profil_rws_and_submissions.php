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
        Schema::table('profil_rws', function (Blueprint $table) {
            $table->text('profil_warga_keterangan')->nullable()->after('profil_warga');
            $table->text('faktor_penyebab_keterangan')->nullable()->after('faktor_penyebab');
            $table->text('strategi_keterangan')->nullable()->after('strategi');
        });

        Schema::table('rw_profile_submissions', function (Blueprint $table) {
            $table->text('profil_warga_keterangan')->nullable()->after('profil_warga');
            $table->text('faktor_penyebab_keterangan')->nullable()->after('faktor_penyebab');
            $table->text('strategi_keterangan')->nullable()->after('strategi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profil_rws_and_submissions', function (Blueprint $table) {
            //
        });
    }
};
