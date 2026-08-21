<?php
// Temporary OPcache clear script - DELETE after use
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "OPcache cleared successfully.\n";
} else {
    echo "OPcache not enabled.\n";
}

// Also clear file stat cache
clearstatcache(true);
echo "File stat cache cleared.\n";

// Show PHP version and loaded files
echo "PHP Version: " . PHP_VERSION . "\n";
echo "Timestamp: " . date('Y-m-d H:i:s') . "\n";
