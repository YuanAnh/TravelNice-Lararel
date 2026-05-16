<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class VNPayService
{
    private string $tmnCode;
    private string $hashSecret;
    private string $url;
    private string $returnUrl;
    private string $timezone;

    // Thời gian hết hạn link thanh toán (phút)
    private int $expireMinutes = 15;

    public function __construct()
    {
        $this->tmnCode    = config('payment.vnpay.tmn_code');
        $this->hashSecret = config('payment.vnpay.hash_secret');
        $this->url        = config('payment.vnpay.url');
        $this->returnUrl  = config('payment.vnpay.return_url');
        $this->timezone   = 'Asia/Ho_Chi_Minh';
    }

    public function createPaymentUrl(Booking $booking): string
    {
        $now        = now()->timezone($this->timezone);
        $createDate = $now->format('YmdHis');
        $expireDate = $now->copy()
                          ->addMinutes($this->expireMinutes)
                          ->format('YmdHis');

        // ── Cải tiến: dùng booking_code + microtime
        // để tránh trùng TxnRef trong cùng 1 giây
        $txnRef = $booking->booking_code . '_' . now()->valueOf();

        $vnpParams = [
            'vnp_Version'    => '2.1.0',
            'vnp_Command'    => 'pay',
            'vnp_TmnCode'    => $this->tmnCode,
            'vnp_Amount'     => (int)($booking->netTotal() * 100),
            'vnp_CurrCode'   => 'VND',
            'vnp_TxnRef'     => $txnRef,
            'vnp_OrderInfo'  => 'Thanh toan booking ' . $booking->booking_code,
            'vnp_OrderType'  => 'other',
            'vnp_Locale'     => 'vn',
            'vnp_ReturnUrl'  => $this->returnUrl,
            'vnp_IpAddr'     => request()->ip(),
            'vnp_CreateDate' => $createDate,
            'vnp_ExpireDate' => $expireDate,
        ];

        ksort($vnpParams);

        $query     = http_build_query($vnpParams);
        $signature = hash_hmac('sha512', $query, $this->hashSecret);

        // ── Cải tiến: ghi log khi tạo URL để truy vết
        Log::info('VNPay: Tạo URL thanh toán', [
            'booking_code' => $booking->booking_code,
            'txn_ref'      => $txnRef,
            'amount'       => $booking->netTotal(),
        ]);

        return $this->url . '?' . $query . '&vnp_SecureHash=' . $signature;
    }

    public function verifyReturn(array $requestData): bool
    {
        // ── Cải tiến: log cảnh báo khi thiếu hash
        if (!isset($requestData['vnp_SecureHash'])) {
            Log::warning('VNPay: Callback thiếu vnp_SecureHash', [
                'ip'   => request()->ip(),
                'data' => $requestData,
            ]);
            return false;
        }

        $secureHash = $requestData['vnp_SecureHash'];
        unset($requestData['vnp_SecureHash']);
        unset($requestData['vnp_SecureHashType']);

        ksort($requestData);
        $query        = http_build_query($requestData);
        $expectedHash = hash_hmac('sha512', $query, $this->hashSecret);

        $isValid = hash_equals($expectedHash, $secureHash);

        // ── Cải tiến: log kết quả xác thực
        if (!$isValid) {
            Log::warning('VNPay: Chữ ký không hợp lệ', [
                'ip'          => request()->ip(),
                'txn_ref'     => $requestData['vnp_TxnRef'] ?? null,
                'received'    => $secureHash,
                'expected'    => $expectedHash,
            ]);
        }

        return $isValid;
    }
    public function handleReturn(array $requestData): array
    {
        // Bước 1: Xác thực chữ ký
        if (!$this->verifyReturn($requestData)) {
            return [
                'success' => false,
                'message' => 'Chữ ký không hợp lệ.',
            ];
        }

        $responseCode = $requestData['vnp_ResponseCode'] ?? null;
        $txnRef       = $requestData['vnp_TxnRef']       ?? null;
        $bookingCode  = $this->getBookingCode($txnRef);

        if (!$bookingCode) {
            return [
                'success' => false,
                'message' => 'Mã đặt tour không hợp lệ.',
            ];
        }

        $booking = Booking::where('booking_code', $bookingCode)
                          ->first();

        if (!$booking) {
            Log::error('VNPay: Không tìm thấy booking', [
                'booking_code' => $bookingCode,
                'txn_ref'      => $txnRef,
            ]);
            return [
                'success' => false,
                'message' => 'Không tìm thấy đơn đặt tour.',
            ];
        }

        // Bước 2: Cập nhật trong Transaction
        return DB::transaction(function () use (
            $booking, $requestData, $responseCode, $txnRef
        ) {
            $isSuccess = ($responseCode === '00');

            // Cập nhật trạng thái Booking
            $booking->update([
                'status' => $isSuccess ? 'paid' : 'pending',
            ]);

            // Lưu bản ghi Payment
            Payment::updateOrCreate(
                ['booking_id' => $booking->id],
                [
                    'gateway'        => 'vnpay',
                    'transaction_id' => $requestData['vnp_TransactionNo'] ?? null,
                    'amount'         => ($requestData['vnp_Amount'] ?? 0) / 100,
                    'status'         => $isSuccess ? 'success' : 'failed',
                    'paid_at'        => $isSuccess ? now() : null,
                ]
            );

            Log::info('VNPay: Xử lý callback hoàn tất', [
                'booking_code'   => $booking->booking_code,
                'response_code'  => $responseCode,
                'success'        => $isSuccess,
            ]);

            return [
                'success' => $isSuccess,
                'booking' => $booking,
                'message' => $isSuccess
                    ? 'Thanh toán thành công!'
                    : 'Thanh toán thất bại. Mã lỗi: ' . $responseCode,
            ];
        });
    }
    public function getBookingCode(string $txnRef): string
    {
        // ── Cải tiến: validate format trước khi xử lý
        if (empty($txnRef) || !str_contains($txnRef, '_')) {
            Log::warning('VNPay: TxnRef sai định dạng', [
                'txn_ref' => $txnRef
            ]);
            return '';
        }

        $parts = explode('_', $txnRef);
        return $parts[0] ?? '';
    }

    /**
     * Lấy mô tả lỗi từ mã phản hồi VNPay
     */
    public function getResponseMessage(string $code): string
    {
        return match($code) {
            '00' => 'Giao dịch thành công.',
            '07' => 'Trừ tiền thành công nhưng giao dịch bị nghi ngờ gian lận.',
            '09' => 'Thẻ chưa đăng ký dịch vụ Internet Banking.',
            '10' => 'Xác thực thông tin thẻ quá 3 lần.',
            '11' => 'Đã hết hạn chờ thanh toán.',
            '12' => 'Thẻ/tài khoản bị khóa.',
            '13' => 'Sai mật khẩu OTP.',
            '24' => 'Khách hàng hủy giao dịch.',
            '51' => 'Tài khoản không đủ số dư.',
            '65' => 'Vượt hạn mức giao dịch trong ngày.',
            '75' => 'Ngân hàng thanh toán đang bảo trì.',
            '79' => 'Nhập sai mật khẩu thanh toán quá số lần quy định.',
            default => "Lỗi không xác định. Mã: {$code}",
        };
    }
    /**
     * Xử lý callback ngầm từ VNPay (Luồng IPN/Webhook Server-to-Server)
     * Trả về đúng định dạng JSON mà máy chủ VNPay yêu cầu.
     */
    public function handleIpn(array $requestData): array
    {
        // Tái sử dụng lại toàn bộ logic kiểm tra và lưu Database từ hàm handleReturn
        $result = $this->handleReturn($requestData);

        if (!$result['success']) {
            if ($result['message'] === 'Chữ ký không hợp lệ.') {
                return ['RspCode' => '97', 'Message' => 'Invalid signature'];
            }
            if ($result['message'] === 'Không tìm thấy đơn đặt tour.') {
                return ['RspCode' => '01', 'Message' => 'Order not found'];
            }
            return ['RspCode' => '99', 'Message' => 'Unknown error'];
        }

        // Nếu mọi thứ thành công, trả về mã 00 báo cho VNPay biết đã ghi nhận thành công
        return ['RspCode' => '00', 'Message' => 'Confirm Success'];
    }
}