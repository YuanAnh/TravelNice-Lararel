<?php

namespace Tests\Unit;

use App\Models\Booking;
use App\Models\TourSlot;
use App\Models\Tour;
use App\Services\VNPayService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VNPayServiceTest extends TestCase
{
    use RefreshDatabase;

    private VNPayService $service;

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'payment.vnpay.tmn_code'    => 'TESTCODE',
            'payment.vnpay.hash_secret' => 'TESTSECRET1234567890ABCDEF123456',
            'payment.vnpay.url'         => 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html',
            'payment.vnpay.return_url'  => 'http://localhost:8000/payment/vnpay/return',
        ]);
        $this->service = new VNPayService();
    }

    public function test_create_payment_url_contains_required_params(): void
    {
        $booking = Booking::factory()->create([
            'total_price'  => 5000000,
            'status'       => 'pending',
        ]);

        $url = $this->service->createPaymentUrl($booking);

        $this->assertStringContainsString('vnp_TmnCode=TESTCODE', $url);
        $this->assertStringContainsString('vnp_Amount=500000000', $url); // × 100
        $this->assertStringContainsString('vnp_SecureHash=', $url);
        $this->assertStringContainsString($booking->booking_code, $url);
    }

    public function test_get_booking_code_from_txn_ref(): void
    {
        $txnRef = 'TNTEST01_1234567890';
        $code   = $this->service->getBookingCode($txnRef);
        $this->assertEquals('TNTEST01', $code);
    }

    public function test_verify_return_fails_with_invalid_signature(): void
    {
        $request = new \Illuminate\Http\Request();
        $request->merge([
            'vnp_ResponseCode' => '00',
            'vnp_TxnRef'       => 'TNTEST01_123',
            'vnp_SecureHash'   => 'invalidsignature',
        ]);

        $result = $this->service->verifyReturn($request->all());
        $this->assertFalse($result);
    }
}