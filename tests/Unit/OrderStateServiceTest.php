<?php

namespace Tests\Unit;

use App\OrderStateService;
use Tests\TestCase;

class OrderStateServiceTest extends TestCase
{
    public function test_pending_can_move_to_confirmed(): void
    {
        $this->assertTrue(OrderStateService::canTransition('pending', 'confirmed'));
    }

    public function test_completed_cannot_move_to_pending(): void
    {
        $this->assertFalse(OrderStateService::canTransition('completed', 'pending'));
    }

    public function test_same_status_is_allowed(): void
    {
        $this->assertTrue(OrderStateService::canTransition('shipping', 'shipping'));
    }

    public function test_confirmed_can_move_to_packing(): void
    {
        $this->assertTrue(OrderStateService::canTransition('confirmed', 'packing'));
    }

    public function test_packing_can_move_to_shipping(): void
    {
        $this->assertTrue(OrderStateService::canTransition('packing', 'shipping'));
    }

    public function test_packing_cannot_move_directly_to_completed(): void
    {
        $this->assertFalse(OrderStateService::canTransition('packing', 'completed'));
    }
}
