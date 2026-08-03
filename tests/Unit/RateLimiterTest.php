<?php

namespace Tests\Unit;

use App\Services\RateLimiter;
use App\DB;
use Tests\IntegrationTestCase;

class RateLimiterTest extends IntegrationTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Clear rate_limits table before each test
        $db = DB::connection();
        $db->exec('DELETE FROM rate_limits');
    }

    public function test_rate_limiter_allows_requests_under_limit(): void
    {
        $key = 'test_key_under_limit';
        
        // Max 3 attempts, decay 60s
        $this->assertTrue(RateLimiter::check($key, 3, 60));
        $this->assertTrue(RateLimiter::check($key, 3, 60));
        $this->assertTrue(RateLimiter::check($key, 3, 60));
        
        // 4th attempt should fail
        $this->assertFalse(RateLimiter::check($key, 3, 60));
    }

    public function test_rate_limiter_remaining_seconds(): void
    {
        $key = 'test_key_remaining';
        
        $this->assertTrue(RateLimiter::check($key, 2, 10));
        
        $remaining = RateLimiter::remainingSeconds($key);
        $this->assertGreaterThan(0, $remaining);
        $this->assertLessThanOrEqual(10, $remaining);
    }
}
