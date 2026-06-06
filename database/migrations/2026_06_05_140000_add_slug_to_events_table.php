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
        if (! Schema::hasColumn('events', 'slug')) {
            Schema::table('events', function (Blueprint $table): void {
                $table->string('slug')->nullable()->after('uuid');
            });
        }

        DB::table('events')
            ->select('id', 'judul', 'uuid')
            ->where(function ($query): void {
                $query->whereNull('slug')->orWhere('slug', '');
            })
            ->orderBy('id')
            ->get()
            ->each(function (object $row): void {
                $base = Str::slug((string) ($row->judul ?: 'event')) ?: 'event';
                $slug = $base;
                $suffix = 1;

                while (DB::table('events')->where('slug', $slug)->where('id', '!=', $row->id)->exists()) {
                    $suffix++;
                    $slug = $base.'-'.$suffix;
                }

                DB::table('events')->where('id', $row->id)->update(['slug' => $slug]);
            });

        if (! $this->indexExists('events', 'events_slug_unique')) {
            Schema::table('events', function (Blueprint $table): void {
                $table->unique('slug', 'events_slug_unique');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('events', 'slug')) {
            Schema::table('events', function (Blueprint $table): void {
                if ($this->indexExists('events', 'events_slug_unique')) {
                    $table->dropUnique('events_slug_unique');
                }
                $table->dropColumn('slug');
            });
        }
    }

    private function indexExists(string $table, string $indexName): bool
    {
        $database = DB::getDatabaseName();

        return DB::table('information_schema.statistics')
            ->where('table_schema', $database)
            ->where('table_name', $table)
            ->where('index_name', $indexName)
            ->exists();
    }
};
