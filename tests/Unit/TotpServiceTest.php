<?php

namespace Tests\Unit;

use App\Services\TotpService;
use Tests\TestCase;

class TotpServiceTest extends TestCase
{
    public function test_generate_secret_and_verify_current_code(): void
    {
        $secret = TotpService::generateSecret();
        $this->assertSame(16, strlen($secret));
        $code = TotpService::currentCode($secret);
        $this->assertMatchesRegularExpression('/^\d{6}$/', $code);
        $this->assertTrue(TotpService::verify($secret, $code));
        $this->assertFalse(TotpService::verify($secret, '000000'));
    }

    public function test_provisioning_uri_contains_secret(): void
    {
        $secret = 'JBSWY3DPEHPK3PXP';
        $uri = TotpService::provisioningUri($secret, 'admin@shop.test', 'Shop Hades');
        $this->assertStringContainsString('otpauth://totp/', $uri);
        $this->assertStringContainsString($secret, $uri);
    }
}
