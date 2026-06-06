<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nia')->nullable()->unique()->after('email');
            $table->uuid('kader_id')->nullable()->after('nia');
            $table->foreign('kader_id')->references('id')->on('kaders')->nullOnDelete();
            $table->string('role')->default('kader')->after('kader_id');
            $table->string('bidang_slug')->nullable()->after('role');
            $table->string('dapil')->nullable()->after('bidang_slug');
            $table->string('kecamatan')->nullable()->after('dapil');
            $table->string('desa')->nullable()->after('kecamatan');
            $table->string('nomor_rw')->nullable()->after('desa');
            $table->string('status')->default('aktif')->after('nomor_rw');
            $table->timestamp('last_login_at')->nullable()->after('status');
            $table->string('last_login_ip')->nullable()->after('last_login_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['kader_id']);
            $table->dropColumn([
                'nia',
                'kader_id',
                'role',
                'bidang_slug',
                'dapil',
                'kecamatan',
                'desa',
                'nomor_rw',
                'status',
                'last_login_at',
                'last_login_ip',
            ]);
        });
    }
};
