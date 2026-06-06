<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kaders', function (Blueprint $table) {
            $table->string('nia')->nullable()->unique()->after('no_kta');
            $table->string('bidang_slug')->nullable()->after('nia');
            $table->boolean('is_activated')->default(false)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('kaders', function (Blueprint $table) {
            $table->dropColumn([
                'nia',
                'bidang_slug',
                'is_activated',
            ]);
        });
    }
};
