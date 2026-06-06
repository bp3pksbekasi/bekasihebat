<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kegiatan_rws', function (Blueprint $table): void {
            if (! Schema::hasColumn('kegiatan_rws', 'tampil_galeri')) {
                $table->boolean('tampil_galeri')->default(false)->after('foto');
            }
        });
    }

    public function down(): void
    {
        Schema::table('kegiatan_rws', function (Blueprint $table): void {
            if (Schema::hasColumn('kegiatan_rws', 'tampil_galeri')) {
                $table->dropColumn('tampil_galeri');
            }
        });
    }
};
