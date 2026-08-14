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
            $table->string('afiliasi_pkk')->nullable()->after('afiliasi_calon_lain');
            $table->string('afiliasi_karang_taruna')->nullable()->after('afiliasi_pkk');
            $table->string('afiliasi_dkm')->nullable()->after('afiliasi_karang_taruna');
            $table->string('afiliasi_tokoh')->nullable()->after('afiliasi_dkm');
            $table->string('sosial_media')->nullable()->after('afiliasi_tokoh');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profil_rws', function (Blueprint $table) {
            $table->dropColumn([
                'afiliasi_pkk',
                'afiliasi_karang_taruna',
                'afiliasi_dkm',
                'afiliasi_tokoh',
                'sosial_media'
            ]);
        });
    }
};
