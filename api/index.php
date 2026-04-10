<?php

// Prepare Vercel /tmp storage directories
$storageDir = '/tmp/storage';
$dirs = [
    $storageDir.'/app/public',
    $storageDir.'/framework/cache/data',
    $storageDir.'/framework/sessions',
    $storageDir.'/framework/testing',
    $storageDir.'/framework/views',
    $storageDir.'/logs',
    $storageDir.'/bootstrap/cache',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
}

// Forward to Laravel's standard index.php
require __DIR__ . '/../public/index.php';
