<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('target_wilayahs', function (Blueprint $table) {
            $table->integer('target_penggalang')->default(0)->after('target_korte_2029');
        });
    }

    public function down(): void
    {
        Schema::table('target_wilayahs', function (Blueprint $table) {
            $table->dropColumn('target_penggalang');
        });
    }
};
