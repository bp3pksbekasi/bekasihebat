<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kortes', function (Blueprint $table) {
            $table->boolean('is_saksi_tps')->default(false)->after('status');
            $table->string('assigned_tps')->nullable()->after('is_saksi_tps');
            $table->string('status_saksi')->default('belum')->after('assigned_tps');
        });
    }

    public function down(): void
    {
        Schema::table('kortes', function (Blueprint $table) {
            $table->dropColumn(['is_saksi_tps', 'assigned_tps', 'status_saksi']);
        });
    }
};
