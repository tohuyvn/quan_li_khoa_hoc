<?php

namespace Tests;

use App\DB;

abstract class IntegrationTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (!DB::isAvailable()) {
            $this->markTestSkipped('MySQL test database chưa sẵn sàng.');
        }

        \App\DatabaseMigrator::run();
    }
}
