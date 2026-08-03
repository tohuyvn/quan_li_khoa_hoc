<?php

namespace Tests\Unit;

use App\JwtService;
use Tests\TestCase;

class JwtServiceTest extends TestCase
{
    public function test_encode_and_decode_successful(): void
    {
        $payload = [
            'user_id' => 42,
            'email' => 'test@example.com',
            'exp' => time() + 3600
        ];

        $token = JwtService::encode($payload);
        $this->assertNotEmpty($token);
        $this->assertCount(3, explode('.', $token));

        $decoded = JwtService::decode($token);
        $this->assertNotNull($decoded);
        $this->assertEquals(42, $decoded['user_id']);
        $this->assertEquals('test@example.com', $decoded['email']);
    }

    public function test_decode_fails_on_tampered_signature(): void
    {
        $payload = [
            'user_id' => 42,
            'exp' => time() + 3600
        ];

        $token = JwtService::encode($payload);
        $parts = explode('.', $token);
        // Guarantee signature modification by changing the first character of the signature part
        $parts[2][0] = $parts[2][0] === 'A' ? 'B' : 'A';
        $tamperedToken = implode('.', $parts);

        $decoded = JwtService::decode($tamperedToken);
        $this->assertNull($decoded);
    }

    public function test_decode_fails_on_expired_token(): void
    {
        $payload = [
            'user_id' => 42,
            'exp' => time() - 10 // Expired 10 seconds ago
        ];

        $token = JwtService::encode($payload);
        $decoded = JwtService::decode($token);
        $this->assertNull($decoded);
    }
}
