# Phase 1 — Bedah Dapil: Schema & Models

## INSTRUKSI PENTING — BACA DULU

1. **JANGAN search codebase**. Semua file di prompt ini BARU, harus CREATE.
2. **Skip pencarian** untuk: `Tps`, `VoteResult`, `PoliticalParty`, `Caleg`, `DptSummary`. File-file ini belum ada.
3. Yang HARUS kamu lakukan: create migration, model, seeder sesuai spec. Tidak perlu konfirmasi.

## KONTEKS PROJECT

- Laravel 12 + Filament 5 + Spatie Permission (sudah terinstall)
- Tabel ini sudah ada: `users`, `indonesia_provinces/cities/districts/villages`, `dpd`, `dpc`, `dpra`, `dapil`, `roles`
- Database: MySQL `bekasi_hebat`
- Model `Dapil` sudah ada dengan fillable `number`, `name`, `allocated_seats`, dst

## GOAL

Phase 1 schema untuk modul "Bedah Dapil" — analytics suara DPRD per wilayah. Buat 5 migration, 5 model, 1 seeder partai.

---

## FILE 1 (CREATE): Migration `create_political_parties_table`

Run: `php artisan make:migration create_political_parties_table`

Isi file migration:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('political_parties', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('nomor_urut')->unique();
            $table->string('code', 20)->unique();
            $table->string('name');
            $table->string('full_name')->nullable();
            $table->string('color_hex', 7)->nullable();
            $table->boolean('is_tracked')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('political_parties');
    }
};
```

---

## FILE 2 (CREATE): Migration `create_calegs_table`

Run: `php artisan make:migration create_calegs_table`

Isi file migration:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('calegs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('political_party_id')->constrained()->cascadeOnDelete();
            $table->foreignId('dapil_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('nomor_urut');
            $table->string('nama');
            $table->enum('gender', ['L', 'P'])->nullable();
            $table->timestamps();

            $table->index(['dapil_id', 'political_party_id', 'nomor_urut']);
            $table->unique(['dapil_id', 'political_party_id', 'nomor_urut']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calegs');
    }
};
```

---

## FILE 3 (CREATE): Migration `create_tps_table`

Run: `php artisan make:migration create_tps_table`

Isi file migration:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tps', function (Blueprint $table) {
            $table->id();
            $table->string('code', 80)->unique();
            $table->foreignId('dapil_id')->constrained()->cascadeOnDelete();
            $table->string('kecamatan_code', 7)->index();
            $table->string('kelurahan_code', 13)->index();
            $table->string('tps_number', 10);
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamps();

            $table->index(['dapil_id', 'kecamatan_code']);
            $table->index(['kelurahan_code', 'tps_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tps');
    }
};
```

**CATATAN:** nama tabel `tps` (tanpa underscore atau plural) karena `tps` sudah plural sendiri di Bahasa Indonesia.

---

## FILE 4 (CREATE): Migration `create_dpt_summaries_table`

Run: `php artisan make:migration create_dpt_summaries_table`

Isi file migration:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('dpt_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tps_id')->constrained('tps')->cascadeOnDelete();
            $table->string('rt', 5)->nullable();
            $table->string('rw', 5)->nullable();

            $table->unsignedInteger('total_dpt')->default(0);
            $table->unsignedInteger('male')->default(0);
            $table->unsignedInteger('female')->default(0);

            $table->unsignedInteger('gen_z')->default(0);
            $table->unsignedInteger('millennial')->default(0);
            $table->unsignedInteger('gen_x')->default(0);
            $table->unsignedInteger('boomer')->default(0);
            $table->unsignedInteger('age_known')->default(0);
            $table->unsignedInteger('age_unknown')->default(0);

            $table->timestamps();

            $table->index(['tps_id', 'rt', 'rw']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dpt_summaries');
    }
};
```

---

## FILE 5 (CREATE): Migration `create_vote_results_table`

Run: `php artisan make:migration create_vote_results_table`

Isi file migration:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('vote_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tps_id')->constrained('tps')->cascadeOnDelete();
            $table->foreignId('political_party_id')->constrained()->cascadeOnDelete();
            $table->foreignId('caleg_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedSmallInteger('election_year')->default(2024);
            $table->unsignedInteger('suara')->default(0);
            $table->timestamps();

            $table->index(['election_year', 'tps_id']);
            $table->index(['election_year', 'political_party_id']);
            $table->index(['election_year', 'caleg_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vote_results');
    }
};
```

---

## FILE 6 (CREATE): `app/Models/PoliticalParty.php`

```php
<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PoliticalParty extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_urut',
        'code',
        'name',
        'full_name',
        'color_hex',
        'is_tracked',
    ];

    protected function casts(): array
    {
        return [
            'nomor_urut' => 'integer',
            'is_tracked' => 'boolean',
        ];
    }

    public function calegs()
    {
        return $this->hasMany(Caleg::class);
    }

    public function voteResults()
    {
        return $this->hasMany(VoteResult::class);
    }

    public function scopeTracked($query)
    {
        return $query->where('is_tracked', true);
    }
}
```

---

## FILE 7 (CREATE): `app/Models/Caleg.php`

```php
<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\UserGender;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Caleg extends Model
{
    use HasFactory;

    protected $fillable = [
        'political_party_id',
        'dapil_id',
        'nomor_urut',
        'nama',
        'gender',
    ];

    protected function casts(): array
    {
        return [
            'nomor_urut' => 'integer',
            'gender' => UserGender::class,
        ];
    }

    public function party()
    {
        return $this->belongsTo(PoliticalParty::class, 'political_party_id');
    }

    public function dapil()
    {
        return $this->belongsTo(Dapil::class);
    }

    public function voteResults()
    {
        return $this->hasMany(VoteResult::class);
    }
}
```

**CATATAN:** Class `App\Enums\UserGender` sudah ada (dibuat di phase user sebelumnya). Pakai itu untuk gender cast.

---

## FILE 8 (CREATE): `app/Models/Tps.php`

```php
<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tps extends Model
{
    use HasFactory;

    protected $table = 'tps';

    protected $fillable = [
        'code',
        'dapil_id',
        'kecamatan_code',
        'kelurahan_code',
        'tps_number',
        'latitude',
        'longitude',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    public function dapil()
    {
        return $this->belongsTo(Dapil::class);
    }

    public function dptSummaries()
    {
        return $this->hasMany(DptSummary::class);
    }

    public function voteResults()
    {
        return $this->hasMany(VoteResult::class);
    }

    public function getTotalDptAttribute(): int
    {
        return (int) $this->dptSummaries()->sum('total_dpt');
    }

    public function getTotalSuaraAttribute(): int
    {
        return (int) $this->voteResults()->sum('suara');
    }
}
```

---

## FILE 9 (CREATE): `app/Models/DptSummary.php`

```php
<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DptSummary extends Model
{
    use HasFactory;

    protected $fillable = [
        'tps_id',
        'rt',
        'rw',
        'total_dpt',
        'male',
        'female',
        'gen_z',
        'millennial',
        'gen_x',
        'boomer',
        'age_known',
        'age_unknown',
    ];

    protected function casts(): array
    {
        return [
            'total_dpt' => 'integer',
            'male' => 'integer',
            'female' => 'integer',
            'gen_z' => 'integer',
            'millennial' => 'integer',
            'gen_x' => 'integer',
            'boomer' => 'integer',
        ];
    }

    public function tps()
    {
        return $this->belongsTo(Tps::class);
    }
}
```

---

## FILE 10 (CREATE): `app/Models/VoteResult.php`

```php
<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoteResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'tps_id',
        'political_party_id',
        'caleg_id',
        'election_year',
        'suara',
    ];

    protected function casts(): array
    {
        return [
            'election_year' => 'integer',
            'suara' => 'integer',
        ];
    }

    public function tps()
    {
        return $this->belongsTo(Tps::class);
    }

    public function party()
    {
        return $this->belongsTo(PoliticalParty::class, 'political_party_id');
    }

    public function caleg()
    {
        return $this->belongsTo(Caleg::class);
    }

    public function scopeYear($query, int $year)
    {
        return $query->where('election_year', $year);
    }
}
```

---

## FILE 11 (CREATE): `database/seeders/PoliticalPartiesSeeder.php`

```php
<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\PoliticalParty;
use Illuminate\Database\Seeder;

class PoliticalPartiesSeeder extends Seeder
{
    public function run(): void
    {
        $parties = [
            ['nomor_urut' => 1,  'code' => 'PKB',          'name' => 'PKB',      'full_name' => 'Partai Kebangkitan Bangsa',             'color_hex' => '#008000', 'is_tracked' => true],
            ['nomor_urut' => 2,  'code' => 'GERINDRA',     'name' => 'Gerindra', 'full_name' => 'Partai Gerakan Indonesia Raya',         'color_hex' => '#C8102E', 'is_tracked' => true],
            ['nomor_urut' => 3,  'code' => 'PDIP',         'name' => 'PDI-P',    'full_name' => 'Partai Demokrasi Indonesia Perjuangan', 'color_hex' => '#D72027', 'is_tracked' => true],
            ['nomor_urut' => 4,  'code' => 'GOLKAR',       'name' => 'Golkar',   'full_name' => 'Partai Golongan Karya',                 'color_hex' => '#FFD700', 'is_tracked' => true],
            ['nomor_urut' => 5,  'code' => 'NASDEM',       'name' => 'NasDem',   'full_name' => 'Partai NasDem',                         'color_hex' => '#005EA8', 'is_tracked' => true],
            ['nomor_urut' => 6,  'code' => 'BURUH',        'name' => 'Buruh',    'full_name' => 'Partai Buruh',                          'color_hex' => '#FF6B00', 'is_tracked' => false],
            ['nomor_urut' => 7,  'code' => 'GELORA',       'name' => 'Gelora',   'full_name' => 'Partai Gelombang Rakyat Indonesia',     'color_hex' => '#1B4F8B', 'is_tracked' => false],
            ['nomor_urut' => 8,  'code' => 'PKS',          'name' => 'PKS',      'full_name' => 'Partai Keadilan Sejahtera',             'color_hex' => '#000000', 'is_tracked' => true],
            ['nomor_urut' => 9,  'code' => 'PKN',          'name' => 'PKN',      'full_name' => 'Partai Kebangkitan Nusantara',          'color_hex' => '#1E3A8A', 'is_tracked' => false],
            ['nomor_urut' => 10, 'code' => 'HANURA',       'name' => 'Hanura',   'full_name' => 'Partai Hati Nurani Rakyat',             'color_hex' => '#F58220', 'is_tracked' => false],
            ['nomor_urut' => 11, 'code' => 'GARUDA',       'name' => 'Garuda',   'full_name' => 'Partai Garda Republik Indonesia',       'color_hex' => '#FBBF24', 'is_tracked' => false],
            ['nomor_urut' => 12, 'code' => 'DEMOKRAT',     'name' => 'Demokrat', 'full_name' => 'Partai Demokrat',                       'color_hex' => '#003E7E', 'is_tracked' => true],
            ['nomor_urut' => 13, 'code' => 'PSI',          'name' => 'PSI',      'full_name' => 'Partai Solidaritas Indonesia',          'color_hex' => '#D80027', 'is_tracked' => true],
            ['nomor_urut' => 14, 'code' => 'PERINDO',      'name' => 'Perindo',  'full_name' => 'Partai Persatuan Indonesia',            'color_hex' => '#0066B3', 'is_tracked' => false],
            ['nomor_urut' => 15, 'code' => 'PPP',          'name' => 'PPP',      'full_name' => 'Partai Persatuan Pembangunan',          'color_hex' => '#0F7B0F', 'is_tracked' => true],
            ['nomor_urut' => 16, 'code' => 'PARTAI_UMMAT', 'name' => 'Ummat',    'full_name' => 'Partai Ummat',                          'color_hex' => '#1F2937', 'is_tracked' => false],
            ['nomor_urut' => 17, 'code' => 'PAN',          'name' => 'PAN',      'full_name' => 'Partai Amanat Nasional',                'color_hex' => '#1B5FAA', 'is_tracked' => true],
        ];

        foreach ($parties as $party) {
            PoliticalParty::updateOrCreate(
                ['nomor_urut' => $party['nomor_urut']],
                $party
            );
        }

        $this->command->info('Seeded ' . count($parties) . ' political parties. 10 partai tracked: PKB, Gerindra, PDIP, Golkar, NasDem, PKS, Demokrat, PSI, PPP, PAN');
    }
}
```

**CATATAN:** 17 partai di-seed (semua peserta Pemilu 2024). Flag `is_tracked = true` hanya 10 partai. Sisanya tetap tersimpan kalau-kalau data CSV mengandung partai lain, tapi default UI cuma tampilkan yang tracked.

---

## FILE 12 (UPDATE): `database/seeders/DatabaseSeeder.php`

Buka file existing `database/seeders/DatabaseSeeder.php`. Tambahkan `PoliticalPartiesSeeder::class` ke dalam array `$this->call([...])` di method `run()`. Tempatkan di posisi paling akhir setelah seeder lain.

Contoh hasil akhir (assuming existing seeders):

```php
public function run(): void
{
    $this->call([
        RolesAndPermissionsSeeder::class,
        // ... seeder lain yang sudah ada ...
        PoliticalPartiesSeeder::class,
    ]);
}
```

**CATATAN:** Tidak ada FK dependency dari `PoliticalParty` ke seeder lain selain `dapil` yang sudah seeded sebelumnya. Aman ditaruh di akhir.

---

## EKSEKUSI

Jalankan dua command ini:

```bash
php artisan migrate
php artisan db:seed --class=PoliticalPartiesSeeder
```

---

## VERIFIKASI

Buka tinker:

```bash
php artisan tinker
```

Jalankan:

```php
// 1. Cek partai
\App\Models\PoliticalParty::count();
\App\Models\PoliticalParty::tracked()->count();
\App\Models\PoliticalParty::tracked()->pluck('name');

// 2. Cek tabel kosong tapi schema valid
\App\Models\Tps::count();
\App\Models\Caleg::count();
\App\Models\VoteResult::count();
\App\Models\DptSummary::count();

// 3. Cek relasi
$party = \App\Models\PoliticalParty::where('code', 'PKS')->first();
$dapil = \App\Models\Dapil::where('number', 1)->first();

$caleg = \App\Models\Caleg::create([
    'political_party_id' => $party->id,
    'dapil_id' => $dapil->id,
    'nomor_urut' => 1,
    'nama' => 'Test Caleg',
    'gender' => 'L',
]);

echo $caleg->party->name;
echo $caleg->dapil->name;

exit
```

Expected output:
- `PoliticalParty::count()` = 17
- `PoliticalParty::tracked()->count()` = 10
- Tracked list: PKB, Gerindra, PDI-P, Golkar, NasDem, PKS, Demokrat, PSI, PPP, PAN
- `Tps::count()`, `Caleg::count()` dst = 0
- Test caleg create berhasil, party->name = "PKS"

---

## CONSTRAINT

JANGAN BUAT:
- Filament resource
- Controller
- Livewire component
- Import action / job
- Route

JANGAN MODIFY:
- Migration existing
- Seeder lain (kecuali DatabaseSeeder untuk tambah PoliticalPartiesSeeder)
- Model User atau model existing lain

JANGAN INSTALL:
- Package tambahan

Phase 1 cuma fondasi schema. Import data dan UI di phase berikutnya.
