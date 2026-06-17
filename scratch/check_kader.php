<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Kader;

$kaders = Kader::query()
    ->where('jenjang', '!=', 'pelopor')
    ->where(function ($query) {
        $query->where('is_upa', false)
              ->orWhereNull('is_upa');
    })
    ->get();

echo "Total Kader yang bukan Pelopor dan belum UPA: " . $kaders->count() . "\n\n";

// Show distribution per jenjang
$distribution = Kader::query()
    ->where('jenjang', '!=', 'pelopor')
    ->where(function ($query) {
        $query->where('is_upa', false)
              ->orWhereNull('is_upa');
    })
    ->select('jenjang', \DB::raw('count(*) as total'))
    ->groupBy('jenjang')
    ->get();

echo "Distribusi per Jenjang:\n";
foreach ($distribution as $d) {
    echo "- " . $d->jenjang . ": " . $d->total . "\n";
}

echo "\nContoh 5 data kader:\n";
foreach ($kaders->take(5) as $k) {
    echo "- ID: {$k->id}, Nama: {$k->nama}, Jenjang: {$k->jenjang}, is_upa: " . ($k->is_upa ? 'true' : 'false') . ", Desa: {$k->desa}\n";
}
