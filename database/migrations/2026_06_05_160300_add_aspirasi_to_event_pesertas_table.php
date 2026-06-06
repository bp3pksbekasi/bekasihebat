<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_pesertas', function (Blueprint $table): void {
            if (! Schema::hasColumn('event_pesertas', 'aspirasi')) {
                $table->text('aspirasi')->nullable()->after('catatan');
            }
        });
    }

    public function down(): void
    {
        Schema::table('event_pesertas', function (Blueprint $table): void {
            if (Schema::hasColumn('event_pesertas', 'aspirasi')) {
                $table->dropColumn('aspirasi');
            }
        });
    }
};
