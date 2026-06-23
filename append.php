<?php
$content = file_get_contents('resources/views/livewire/buku-induk-rw/detail.blade.php');
$drawer = file_get_contents('drawer_temp.blade.php');
$drawerLines = explode("\n", $drawer);
$drawerContent = implode("\n", array_slice($drawerLines, 0, 191));

$pos = strrpos($content, '</div>');
if ($pos !== false) {
    $content = substr($content, 0, $pos) . "\n" . $drawerContent . "\n</div>";
    file_put_contents('resources/views/livewire/buku-induk-rw/detail.blade.php', $content);
    echo "Appended successfully.";
}
