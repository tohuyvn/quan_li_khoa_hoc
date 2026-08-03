<?php

namespace Tests\Unit;

use App\Models\Order;
use Tests\TestCase;

class OrderTest extends TestCase
{
    public function test_generate_code_format(): void
    {
        $code = Order::generateCode();
        
        // Format should match HDYYMMDD-XXXXX
        // HD (2) + YYMMDD (6) + - (1) + XXXXX (5) = 14 chars
        $this->assertEquals(14, strlen($code));
        $this->assertStringStartsWith('HD', $code);
        $this->assertStringContainsString('-', $code);
        
        // Parts check
        $parts = explode('-', $code);
        $this->assertCount(2, $parts);
        
        $prefix = $parts[0]; // HDYYMMDD
        $suffix = $parts[1]; // XXXXX
        
        $this->assertEquals(8, strlen($prefix));
        $this->assertEquals(5, strlen($suffix));
        
        $datePart = substr($prefix, 2);
        $this->assertEquals(date('ymd'), $datePart);
    }
}
