#!/usr/bin/env php
<?php

require __DIR__ . '/../src/bootstrap.php';

use App\DB;

try {
    $db = DB::connection();
    // Delete shipper locations older than 24 hours
    $stmt = $db->prepare('DELETE FROM shipper_locations WHERE created_at < NOW() - INTERVAL 1 DAY');
    $stmt->execute();
    $rows = $stmt->rowCount();
    echo "Cleanup completed. Deleted {$rows} shipper location records older than 24 hours." . PHP_EOL;
} catch (Throwable $e) {
    fwrite(STDERR, 'Cleanup failed: ' . $e->getMessage() . PHP_EOL);
    exit(1);
}
