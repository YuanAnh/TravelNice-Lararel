<?php

namespace Tests\Unit;

use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_booking_code_auto_generated_on_create(): void
    {
        $booking = Booking::factory()->create();
        $this->assertNotNull($booking->booking_code);
        $this->assertStringStartsWith('TN', $booking->booking_code);
        $this->assertEquals(8, strlen($booking->booking_code));
    }

    public function test_is_pending_returns_true_when_pending(): void
    {
        $booking = Booking::factory()->make(['status' => 'pending']);
        $this->assertTrue($booking->isPending());
        $this->assertFalse($booking->isPaid());
    }

    public function test_is_paid_returns_true_when_paid(): void
    {
        $booking = Booking::factory()->make(['status' => 'paid']);
        $this->assertTrue($booking->isPaid());
        $this->assertFalse($booking->isPending());
    }

    public function test_is_completed_returns_true_when_completed(): void
    {
        $booking = Booking::factory()->make(['status' => 'completed']);
        $this->assertTrue($booking->isCompleted());
    }

    public function test_is_cancelled_returns_true_when_cancelled(): void
    {
        $booking = Booking::factory()->make(['status' => 'cancelled']);
        $this->assertTrue($booking->isCancelled());
    }

    public function test_net_total_subtracts_discount(): void
    {
        $booking = Booking::factory()->make([
            'total_price'     => 10000000,
            'discount_amount' => 1000000,
        ]);
        $this->assertEquals(9000000, $booking->netTotal());
    }

    public function test_net_total_without_discount(): void
    {
        $booking = Booking::factory()->make([
            'total_price'     => 5000000,
            'discount_amount' => 0,
        ]);
        $this->assertEquals(5000000, $booking->netTotal());
    }

    public function test_booking_codes_are_unique(): void
    {
        $booking1 = Booking::factory()->create();
        $booking2 = Booking::factory()->create();
        $this->assertNotEquals($booking1->booking_code, $booking2->booking_code);
    }
}