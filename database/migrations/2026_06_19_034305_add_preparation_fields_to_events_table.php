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
        Schema::table('events', function (Blueprint $table) {
            if (!Schema::hasColumn('events', 'speakers')) {
                $table->text('speakers')->nullable()->after('pic_hp');
            }
            if (!Schema::hasColumn('events', 'funding_source')) {
                $table->string('funding_source')->nullable()->after('speakers');
            }
            if (!Schema::hasColumn('events', 'target_program')) {
                $table->string('target_program')->nullable()->after('funding_source');
            }
            if (!Schema::hasColumn('events', 'requirements')) {
                $table->text('requirements')->nullable()->after('target_program');
            }
            if (!Schema::hasColumn('events', 'budget_notes')) {
                $table->text('budget_notes')->nullable()->after('requirements');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            //
        });
    }
};
