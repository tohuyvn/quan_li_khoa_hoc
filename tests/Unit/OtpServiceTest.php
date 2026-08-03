<?php

namespace Tests\Unit;

use App\OtpService;
use Tests\TestCase;

class OtpServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        OtpService::clear();
    }

    public function test_generate_otp_code_of_correct_length(): void
    {
        $code = OtpService::generate();
        $this->assertMatchesRegularExpression('/^\d{6}$/', $code);
    }

    public function test_send_stores_otp_in_session(): void
    {
        // This will write to logs if mail fails, but returns true
        $success = OtpService::send('test_otp@example.com', 'test_action', ['id' => 123]);
        $this->assertTrue($success);

        $pending = OtpService::pending();
        $this->assertNotNull($pending);
        $this->assertEquals('test_otp@example.com', $pending['email']);
        $this->assertEquals('test_action', $pending['action']);
        $this->assertEquals(123, $pending['payload']['id']);
        $this->assertNotEmpty($pending['code']);
    }

    public function test_verify_matches_correct_code(): void
    {
        OtpService::send('test_otp@example.com', 'test_action', []);
        $pending = OtpService::pending();
        $code = $pending['code'];

        $this->assertTrue(OtpService::verify($code));
        $this->assertFalse(OtpService::verify('000000'));
    }

    public function test_verify_fails_on_expired_otp(): void
    {
        OtpService::send('test_otp@example.com', 'test_action', []);
        
        // Mock expiration in session
        $_SESSION['pending_otp']['expires_at'] = time() - 1;

        $pending = OtpService::pending();
        $code = $pending['code'];

        $this->assertFalse(OtpService::verify($code));
        // Verify should have cleared the expired session
        $this->assertNull(OtpService::pending());
    }

    public function test_can_resend_respects_cooldown(): void
    {
        OtpService::send('test_otp@example.com', 'test_action', []);
        
        // Freshly sent OTP should not allow resending immediately
        $this->assertFalse(OtpService::canResend());

        // Mock sent time to be in the past (e.g. 65 seconds ago)
        $_SESSION['pending_otp']['sent_at'] = time() - 65;

        $this->assertTrue(OtpService::canResend());
    }
}
