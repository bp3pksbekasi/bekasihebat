<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('anggota_dewans', function (Blueprint $table): void {
            if (! Schema::hasColumn('anggota_dewans', 'suara_2024')) {
                $table->integer('suara_2024')->default(0)->after('dapil');
            }

            if (! Schema::hasColumn('anggota_dewans', 'status_petahana')) {
                $table->boolean('status_petahana')->default(false)->after('suara_2024');
            }

            if (! Schema::hasColumn('anggota_dewans', 'jabatan_fraksi')) {
                $table->string('jabatan_fraksi')->nullable()->after('status_petahana');
            }

            if (! Schema::hasColumn('anggota_dewans', 'jabatan_dprd')) {
                $table->string('jabatan_dprd')->nullable()->after('jabatan_fraksi');
            }

            if (! Schema::hasColumn('anggota_dewans', 'jabatan_partai')) {
                $table->string('jabatan_partai')->nullable()->after('jabatan_dprd');
            }

            if (! Schema::hasColumn('anggota_dewans', 'wilayah_dapil')) {
                $table->string('wilayah_dapil')->nullable()->after('jabatan_partai');
            }
        });
    }

    public function down(): void
    {
        Schema::table('anggota_dewans', function (Blueprint $table): void {
            $columns = [
                'wilayah_dapil',
                'jabatan_partai',
                'jabatan_dprd',
                'jabatan_fraksi',
                'status_petahana',
                'suara_2024',
            ];

            $existingColumns = array_values(array_filter(
                $columns,
                fn (string $column): bool => Schema::hasColumn('anggota_dewans', $column)
            ));

            if ($existingColumns !== []) {
                $table->dropColumn($existingColumns);
            }
        });
    }
};
