<?php

namespace App\Services;

use App\Models\Booking;
use Illuminate\Http\Request;

class VNPayService
{
    private string $tmnCode;
    private string $hashSecret;
    private string $url;
    private string $returnUrl;

    public function __construct()
    {
        $this->tmnCode    = config('payment.vnpay.tmn_code');
        $this->hashSecret = config('payment.vnpay.hash_secret');
        $this->url        = config('payment.vnpay.url');
        $this->returnUrl  = config('payment.vnpay.return_url');
    }

    /**
     * Tạo URL thanh toán VNPay
     */
    public function createPaymentUrl(Booking $booking): string
    {
        $vnpParams = [
            'vnp_Version'    => '2.1.0',
            'vnp_Command'    => 'pay',
            'vnp_TmnCode'    => $this->tmnCode,
            'vnp_Amount'     => (int)($booking->netTotal() * 100), 
            'vnp_CurrCode'   => 'VND',
            'vnp_TxnRef'     => $booking->booking_code . '_' . time(),
            'vnp_OrderInfo'  => 'Thanh toan booking ' . $booking->booking_code,
            'vnp_OrderType'  => 'other',
            'vnp_Locale'     => 'vn',
            'vnp_ReturnUrl'  => $this->returnUrl,
            'vnp_IpAddr'     => request()->ip(),
            'vnp_CreateDate' => now()->format('YmdHis'),
            'vnp_ExpireDate' => now()->addMinutes(15)->format('YmdHis'),
        ];

        ksort($vnpParams);

        $query     = http_build_query($vnpParams);
        $signature = hash_hmac('sha512', $query, $this->hashSecret);

        return $this->url . '?' . $query . '&vnp_SecureHash=' . $signature;
    }

    /**
     * Xác thực callback từ VNPay
     */
    public function verifyReturn(Request $request): bool
    {
        $secureHash = $request->get('vnp_SecureHash');
        $params = $request->except(['vnp_SecureHash', 'vnp_SecureHashType']);

        ksort($params);
        $query         = http_build_query($params);
        $expectedHash  = hash_hmac('sha512', $query, $this->hashSecret);

        return hash_equals($expectedHash, $secureHash);
    }

    /**
     * Lấy booking_code từ TxnRef
     */
    public function getBookingCode(string $txnRef): string
    {
        return explode('_', $txnRef)[0];
    }
}