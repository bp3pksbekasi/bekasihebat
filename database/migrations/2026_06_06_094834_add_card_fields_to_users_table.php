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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'nik')) {
                $table->string('nik', 16)->nullable()->after('phone');
            }
            if (!Schema::hasColumn('users', 'ttl_tempat')) {
                $table->string('ttl_tempat')->nullable()->after('nik');
            }
            if (!Schema::hasColumn('users', 'ttl_tanggal')) {
                $table->date('ttl_tanggal')->nullable()->after('ttl_tempat');
            }
            if (!Schema::hasColumn('users', 'jenis_kelamin')) {
                $table->string('jenis_kelamin', 1)->nullable()->after('ttl_tanggal');
            }
            if (!Schema::hasColumn('users', 'foto_path')) {
                $table->string('foto_path')->nullable()->after('jenis_kelamin');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $cols = [];
            if (Schema::hasColumn('users', 'ttl_tempat')) {
                $cols[] = 'ttl_tempat';
            }
            if (Schema::hasColumn('users', 'ttl_tanggal')) {
                $cols[] = 'ttl_tanggal';
            }
            if (Schema::hasColumn('users', 'jenis_kelamin')) {
                $cols[] = 'jenis_kelamin';
            }
            if (Schema::hasColumn('users', 'foto_path')) {
                $cols[] = 'foto_path';
            }
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};
