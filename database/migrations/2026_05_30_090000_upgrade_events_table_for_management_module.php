<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table): void {
            if (! Schema::hasColumn('events', 'uuid')) {
                $table->uuid('uuid')->nullable()->after('id');
            }

            if (! Schema::hasColumn('events', 'judul')) {
                $table->string('judul')->default('Event Tanpa Judul')->after('uuid');
            }

            if (! Schema::hasColumn('events', 'deskripsi')) {
                $table->text('deskripsi')->nullable()->after('judul');
            }

            if (! Schema::hasColumn('events', 'jenis')) {
                $table->string('jenis')->default('lainnya')->after('deskripsi');
            }

            if (! Schema::hasColumn('events', 'tanggal_mulai')) {
                $table->dateTime('tanggal_mulai')->nullable()->after('jenis');
            }

            if (! Schema::hasColumn('events', 'tanggal_selesai')) {
                $table->dateTime('tanggal_selesai')->nullable()->after('tanggal_mulai');
            }

            if (! Schema::hasColumn('events', 'lokasi')) {
                $table->string('lokasi')->default('-')->after('tanggal_selesai');
            }

            if (! Schema::hasColumn('events', 'lokasi_desa')) {
                $table->string('lokasi_desa')->nullable()->after('lokasi');
            }

            if (! Schema::hasColumn('events', 'lokasi_kecamatan')) {
                $table->string('lokasi_kecamatan')->nullable()->after('lokasi_desa');
            }

            if (! Schema::hasColumn('events', 'lokasi_dapil')) {
                $table->string('lokasi_dapil')->nullable()->after('lokasi_kecamatan');
            }

            if (! Schema::hasColumn('events', 'kapasitas')) {
                $table->integer('kapasitas')->default(0)->after('lokasi_dapil');
            }

            if (! Schema::hasColumn('events', 'is_public')) {
                $table->boolean('is_public')->default(false)->after('kapasitas');
            }

            if (! Schema::hasColumn('events', 'cover_image')) {
                $table->string('cover_image')->nullable()->after('is_public');
            }

            if (! Schema::hasColumn('events', 'status')) {
                $table->string('status')->default('draft')->after('cover_image');
            }

            if (! Schema::hasColumn('events', 'level_approval')) {
                $table->string('level_approval')->default('dpra')->after('status');
            }

            if (! Schema::hasColumn('events', 'penyelenggara')) {
                $table->string('penyelenggara')->nullable()->after('level_approval');
            }

            if (! Schema::hasColumn('events', 'pic_nama')) {
                $table->string('pic_nama')->nullable()->after('penyelenggara');
            }

            if (! Schema::hasColumn('events', 'pic_hp')) {
                $table->string('pic_hp')->nullable()->after('pic_nama');
            }

            if (! Schema::hasColumn('events', 'kegiatan_rw_id')) {
                $table->uuid('kegiatan_rw_id')->nullable()->after('pic_hp');
            }

            if (! Schema::hasColumn('events', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('kegiatan_rw_id')->constrained('users')->nullOnDelete();
            }
        });

        DB::table('events')
            ->select('id')
            ->whereNull('uuid')
            ->orderBy('id')
            ->get()
            ->each(fn (object $row) => DB::table('events')->where('id', $row->id)->update(['uuid' => (string) Str::uuid()]));

        DB::table('events')
            ->whereNull('tanggal_mulai')
            ->update(['tanggal_mulai' => DB::raw('created_at')]);

        DB::table('events')
            ->where('judul', '')
            ->update(['judul' => 'Event Tanpa Judul']);

        if (! $this->hasIndex('events', 'events_uuid_unique')) {
            Schema::table('events', function (Blueprint $table): void {
                $table->unique('uuid', 'events_uuid_unique');
            });
        }

        if (! $this->hasForeign('events', 'events_kegiatan_rw_id_foreign')) {
            Schema::table('events', function (Blueprint $table): void {
                $table->foreign('kegiatan_rw_id')->references('id')->on('kegiatan_rws')->nullOnDelete();
            });
        }

        if (! $this->hasIndex('events', 'events_status_index')) {
            Schema::table('events', function (Blueprint $table): void {
                $table->index('status', 'events_status_index');
            });
        }

        if (! $this->hasIndex('events', 'events_is_public_index')) {
            Schema::table('events', function (Blueprint $table): void {
                $table->index('is_public', 'events_is_public_index');
            });
        }

        if (! $this->hasIndex('events', 'events_tanggal_mulai_index')) {
            Schema::table('events', function (Blueprint $table): void {
                $table->index('tanggal_mulai', 'events_tanggal_mulai_index');
            });
        }

        if (! $this->hasIndex('events', 'events_lokasi_dapil_index')) {
            Schema::table('events', function (Blueprint $table): void {
                $table->index('lokasi_dapil', 'events_lokasi_dapil_index');
            });
        }
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table): void {
            foreach ([
                'events_uuid_unique',
                'events_status_index',
                'events_is_public_index',
                'events_tanggal_mulai_index',
                'events_lokasi_dapil_index',
            ] as $index) {
                if ($this->hasIndex('events', $index)) {
                    $table->dropIndex($index);
                }
            }

            if ($this->hasForeign('events', 'events_kegiatan_rw_id_foreign')) {
                $table->dropForeign('events_kegiatan_rw_id_foreign');
            }
        });
    }

    private function hasIndex(string $table, string $indexName): bool
    {
        $database = DB::getDatabaseName();

        return DB::table('information_schema.statistics')
            ->where('table_schema', $database)
            ->where('table_name', $table)
            ->where('index_name', $indexName)
            ->exists();
    }

    private function hasForeign(string $table, string $foreignName): bool
    {
        $database = DB::getDatabaseName();

        return DB::table('information_schema.table_constraints')
            ->where('constraint_schema', $database)
            ->where('table_name', $table)
            ->where('constraint_name', $foreignName)
            ->where('constraint_type', 'FOREIGN KEY')
            ->exists();
    }
};
