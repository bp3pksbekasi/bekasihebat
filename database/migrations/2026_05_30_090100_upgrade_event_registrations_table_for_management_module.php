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
        Schema::table('event_registrations', function (Blueprint $table): void {
            if (! Schema::hasColumn('event_registrations', 'uuid')) {
                $table->uuid('uuid')->nullable()->after('id');
            }

            if (! Schema::hasColumn('event_registrations', 'event_id')) {
                $table->foreignId('event_id')->nullable()->after('uuid')->constrained('events')->cascadeOnDelete();
            }

            if (! Schema::hasColumn('event_registrations', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('event_id')->constrained('users')->cascadeOnDelete();
            }

            if (! Schema::hasColumn('event_registrations', 'status')) {
                $table->string('status')->default('registered')->after('user_id');
            }

            if (! Schema::hasColumn('event_registrations', 'affiliate_user_id')) {
                $table->foreignId('affiliate_user_id')->nullable()->after('status')->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('event_registrations', 'attended_at')) {
                $table->timestamp('attended_at')->nullable()->after('affiliate_user_id');
            }
        });

        DB::table('event_registrations')
            ->select('id')
            ->whereNull('uuid')
            ->orderBy('id')
            ->get()
            ->each(fn (object $row) => DB::table('event_registrations')->where('id', $row->id)->update(['uuid' => (string) Str::uuid()]));

        if (! $this->hasIndex('event_registrations', 'event_registrations_uuid_unique')) {
            Schema::table('event_registrations', function (Blueprint $table): void {
                $table->unique('uuid', 'event_registrations_uuid_unique');
            });
        }

        if (! $this->hasIndex('event_registrations', 'event_registrations_event_id_status_index')) {
            Schema::table('event_registrations', function (Blueprint $table): void {
                $table->index(['event_id', 'status'], 'event_registrations_event_id_status_index');
            });
        }

        if (! $this->hasIndex('event_registrations', 'event_registrations_event_id_user_id_unique')) {
            Schema::table('event_registrations', function (Blueprint $table): void {
                $table->unique(['event_id', 'user_id'], 'event_registrations_event_id_user_id_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::table('event_registrations', function (Blueprint $table): void {
            foreach ([
                'event_registrations_uuid_unique',
                'event_registrations_event_id_status_index',
                'event_registrations_event_id_user_id_unique',
            ] as $index) {
                if ($this->hasIndex('event_registrations', $index)) {
                    $table->dropIndex($index);
                }
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
};
