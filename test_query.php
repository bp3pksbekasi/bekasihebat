<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::where('name', 'LIKE', '%Marhadi%')->first();
auth()->login($user);

$sisir = app(\App\Livewire\BedahDapil\SisirRw::class);
$method = new ReflectionMethod($sisir, 'filteredTargetQuery');
$method->setAccessible(true);
$query = $method->invoke($sisir);

$results = $query->get();
echo "Number of results: " . $results->count() . "\n";
echo "First few: \n";
foreach($results->take(5) as $r) {
    echo "- " . $r->desa . " (" . $r->kecamatan . ")\n";
}
