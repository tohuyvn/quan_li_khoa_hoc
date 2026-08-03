<?php

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Bootstrap the custom MVC environment
        if (!defined('BASE_PATH')) {
            require_once dirname(__DIR__) . '/src/bootstrap.php';
        }
    }
}
