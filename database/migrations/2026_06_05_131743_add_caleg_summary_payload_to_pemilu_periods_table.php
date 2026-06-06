<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pemilu_periods', function (Blueprint $table) {
            $table->json('caleg_summary_payload')->nullable()->after('source_meta');
        });
    }

    public function down(): void
    {
        Schema::table('pemilu_periods', function (Blueprint $table) {
            $table->dropColumn('caleg_summary_payload');
        });
    }
};
