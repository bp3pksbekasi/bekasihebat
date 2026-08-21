<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bidang_dpds', function (Blueprint $table) {
            $table->string('singkatan')->nullable()->after('nama');
        });
    }

    public function down(): void
    {
        Schema::table('bidang_dpds', function (Blueprint $table) {
            $table->dropColumn('singkatan');
        });
    }
};
