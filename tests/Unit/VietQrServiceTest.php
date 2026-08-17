<?php

namespace Tests\Unit;

use App\Services\VietQrService;
use PHPUnit\Framework\TestCase;

class VietQrServiceTest extends TestCase
{
    public function testExtractOrderCodeFromTransferContent(): void
    {
        $this->assertSame(
            'HD260812-XZ3IU',
            VietQrService::extractOrderCode('THANH TOAN HD260812-XZ3IU')
        );
        $this->assertSame(
            'HD260812-XZ3IU',
            VietQrService::extractOrderCode('NGUYEN VAN A chuyen THANH TOAN HD260812-XZ3IU FT123')
        );
        $this->assertSame(
            'HD260812-XZ3IU',
            VietQrService::extractOrderCode('random text', 'HD260812-XZ3IU')
        );
        $this->assertNull(VietQrService::extractOrderCode('chuyen tien khong ma'));
    }

    public function testAmountsMatch(): void
    {
        $this->assertTrue(VietQrService::amountsMatch(30000, 30000));
        $this->assertFalse(VietQrService::amountsMatch(30000, 29900));
        $this->assertFalse(VietQrService::amountsMatch(0, 0));
    }
}
