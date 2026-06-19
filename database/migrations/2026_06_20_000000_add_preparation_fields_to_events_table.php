<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table): void {
            if (! Schema::hasColumn('events', 'speakers')) {
                $table->text('speakers')->nullable()->after('pic_hp');
            }
            if (! Schema::hasColumn('events', 'funding_source')) {
                $table->string('funding_source')->nullable()->after('speakers');
            }
            if (! Schema::hasColumn('events', 'target_program')) {
                $table->string('target_program')->nullable()->after('funding_source');
            }
            if (! Schema::hasColumn('events', 'requirements')) {
                $table->text('requirements')->nullable()->after('target_program');
            }
            if (! Schema::hasColumn('events', 'budget_notes')) {
                $table->text('budget_notes')->nullable()->after('requirements');
            }
        });

        Schema::table('event_reports', function (Blueprint $table): void {
            if (! Schema::hasColumn('event_reports', 'rating')) {
                $table->string('rating')->nullable()->after('realisasi_anggaran');
            }
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table): void {
            if (Schema::hasColumn('events', 'speakers')) {
                $table->dropColumn('speakers');
            }
            if (Schema::hasColumn('events', 'funding_source')) {
                $table->dropColumn('funding_source');
            }
            if (Schema::hasColumn('events', 'target_program')) {
                $table->dropColumn('target_program');
            }
            if (Schema::hasColumn('events', 'requirements')) {
                $table->dropColumn('requirements');
            }
            if (Schema::hasColumn('events', 'budget_notes')) {
                $table->dropColumn('budget_notes');
            }
        });

        Schema::table('event_reports', function (Blueprint $table): void {
            if (Schema::hasColumn('event_reports', 'rating')) {
                $table->dropColumn('rating');
            }
        });
    }
};
