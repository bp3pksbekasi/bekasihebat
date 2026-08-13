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
            $table->string('afiliasi_pilkades')->nullable()->after('caleg_terpilih_nama');
            $table->string('afiliasi_calon_lain')->nullable()->after('afiliasi_pilkades');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profil_rws', function (Blueprint $table) {
            $table->dropColumn(['afiliasi_pilkades', 'afiliasi_calon_lain']);
        });
    }
};
