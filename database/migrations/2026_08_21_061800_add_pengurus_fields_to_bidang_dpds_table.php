<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bidang_dpds', function (Blueprint $table) {
            $table->string('kabid')->nullable()->after('pic_hp');
            $table->string('nohpkabid')->nullable()->after('kabid');
            $table->string('sekbid')->nullable()->after('nohpkabid');
            $table->string('nohpsekbid')->nullable()->after('sekbid');
            $table->string('periode')->nullable()->after('nohpsekbid');
        });
    }

    public function down(): void
    {
        Schema::table('bidang_dpds', function (Blueprint $table) {
            $table->dropColumn(['kabid', 'nohpkabid', 'sekbid', 'nohpsekbid', 'periode']);
        });
    }
};
