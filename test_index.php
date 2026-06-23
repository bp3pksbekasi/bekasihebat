<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$c = app(App\Livewire\BukuIndukRw\Index::class);
$c->search = 'KARANGSATRIA';
$view = $c->render();
$rws = $view->getData()['rws'];
foreach($rws as $rw) {
    if ($rw->nomor_rw == '007') {
        echo json_encode($rw->toArray());
    }
}
