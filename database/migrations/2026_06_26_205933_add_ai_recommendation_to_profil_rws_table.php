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
            $table->text('ai_recommendation')->nullable()->after('keterangan_lain');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profil_rws', function (Blueprint $table) {
            $table->dropColumn('ai_recommendation');
        });
    }
};
