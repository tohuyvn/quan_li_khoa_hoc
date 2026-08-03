#!/usr/bin/env php
<?php

require __DIR__ . '/../src/bootstrap.php';

use App\DatabaseMigrator;
use App\Seed;

try {
    DatabaseMigrator::run();
    Seed::run();
    echo "Migration completed. Database: " . config('database.name') . PHP_EOL;
    echo "Source: shop_hades.sql (schema + data)" . PHP_EOL;
} catch (Throwable $e) {
    fwrite(STDERR, 'Migration failed: ' . $e->getMessage() . PHP_EOL);
    exit(1);
}
